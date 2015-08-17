<?php

class GW_Tasks_App extends GW_App_Base
{
	var $data;
	var $error_code=0;
	var $error_message;
	var $timer;
	var $debug=false;
	
	
	//todo:
	
	var $max_execution_time=0; //nurodoma maksimalaus vykdymo sekundes 0-neribojama
	var $single_instance=1;

	function __construct($data)
	{
		$this->initDb();
		
		
				
		if(is_object($data))
			$this->data = $data;
		else
			$this->loadData($data);
			
			
		$this->timer = new GW_Timer;
		$this->debug = $this->data->arguments['debug'];
		
		
	}
	
	function init()
	{
		
	}
	
	function beforeProcess()
	{		
		$this->data->running = getmypid();
		
		if($this->max_execution_time)
			$this->data->halt_time = date('Y-m-d H:i:s', strtotime("+ $this->max_execution_time second"));
		
		//if task is called without database
		if(is_callable(Array($this->data,'update')))
			$this->data->update(Array('running','halt_time'));	

		if($this->debug)
		{
			$this->msg('Running in debug mode ($this->debug=1)');
		
			$this->msg(Array('arguments'=>$this->data->arguments));
		}
		
	}
	
	function process()
	{
	}
	
	function afterProcess()
	{
		$this->output('-----TASKINFO-----');
		$this->output("TASK_ID: {$this->data->id} :TASK_ID");
		$this->output("ERROR_CODE: $this->error_code :ERROR_CODE");
		$this->output("ERROR_MESSAGE: $this->error_message :ERROR_MESSAGE");
	}
	


	function loadData($id)
	{
		$this->data = new GW_Task($id);
		$this->data->load();	
	}
		
	function error($code, $msg='', $die=1)
	{
		$this->data->error_code=$code;
		$this->data->error_msg=$msg;
		
		if($die)
			$this->data->running=0;
		
		$this->data->update(Array('error_code','error_msg','running'));
		
		die($this->data->error_msg."\n");
	}	
	
	static function getClassName($task_name)
	{		
		return pathinfo($task_name, PATHINFO_FILENAME).'_Task';
	}
	
	static function getFilename($task_name)
	{
		//allowed chars a-z, 0-9, _, /
		//dont ever change and add char "." it would allow to exit from cli/tasks directory (/../../)
		$task_name = preg_replace('/[^a-z0-9_\/]/i','', $task_name);
		
		return GW::s('DIR/ROOT').'daemon/tasks/'.$task_name.'.task.class.php';
	}
	
	static function loadTaskFile($task_name)
	{
		if(file_exists($tmp = self::getFilename($task_name)))
			require_once $tmp;
		else	
			$this->error(602, "Cant find task file '$tmp'");	
	}
	
	//call from task.php
	function runInside()
	{
		$name = $this->data->name;
		
		if($this->single_instance && !$this->data->canSingleInstanceRun())
			$this->error(603, "Canceled (Single instance limit)");			

			
		self::run($name, $this->data);
	}
	
	static function run($task_name, $data=Array())
	{
		self::loadTaskFile($task_name);
		
		$class_name = self::getClassName($task_name);
		$task = new $class_name($data);		
		$task->init();
		$task->beforeProcess();
		$task->process();
		$task->afterProcess();	
	}
	
	static function runDirect($task, $arguments)
	{
		$data->arguments = $arguments;
		self::run($task, $data);
	}
	
	static function runSeparateWrap($task_id)
	{
		
		$cmd = GW::s('DIR/ROOT').'daemon/task.php '.$task_id.' wrap >/tmp/gw_cms_task_wrap 2>&1 &';
		shell_exec($cmd);
	}
	
	function __get_value($name, &$output)
	{
		if(strpos($output, $name)===false)
			return false;
			
		$tmp = explode("$name: ", $output, 2);
		$tmp = explode(" :$name", $tmp[1], 2);
		
		return $tmp[0];
	}
	
	function runSeparate()
	{
				
		$t = new GW_Timer;
		
		$logfile=GW::$dir['LOGS'].'task_'.$this->data->id.'.log';
		
		shell_exec(GW::s('DIR/ROOT').'daemon/task.php '.$this->data->id.' >'.$logfile.' 2>&1');
		
		$output=file_get_contents($logfile);
		
		unlink($logfile);
		
		$this->data->speed=$t->stop(5);	
				
		
		if(self::__get_value('TASK_ID', $output)!= $this->data->id){
			$this->data->error_code='601';
			$this->data->error_msg="Failed to finish";
		}else{
			$this->data->error_code = self::__get_value('ERROR_CODE', $output);
			$this->data->error_msg = self::__get_value('ERROR_MESSAGE', $output);
		}
		

		
		$this->data->output = preg_replace('/-----TASKINFO-----.*/is','', $output);
				
		
		$name = $this->data->name;
		$id = $this->data->id;
		$this->msg("[$name][$id] out:\n{$this->data->output}");
		
		
		$this->data->finish_time = date('Y-m-d H:i:s');
		$this->data->running = 0;
		
		$this->data->update(Array('output','error_code','error_msg','finish_time','running','speed'));
	}	
	
	function addTask($vals)
	{
		$this->data = new GW_Task();
		$this->data->setValues($vals);
		$this->data->insert();
	}
	
	function checkAndRun()
	{
		$list = GW_Task::getForExecutionStatic();
		
		foreach($list as $item)
			self::runSeparateWrap($item->id);
			
		return count($list);		
	}
	
	function msg($msg)
	{
		echo ('['.$this->timer->stop(4).'] '.$msg."\n");
	}	
	
	function output($msg)
	{
		echo $msg."\n";
	}		
}