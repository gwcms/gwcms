<?php


class Module_Tasks extends GW_Common_Module
{	

	public $default_view = 'list';	
	
	function init()
	{
		parent::init();
		$this->list_params['paging_enabled']=1;
	}

	
	
	function __eventAfterList($list)
	{	

		$this->loadTasksList();		
		
		if(!GW_App_System::getRunningPid())
			$this->setError("admin/cli/system.php is not running");
		
		
		if($this->tpl_vars['grouped'])
		{
			$counts = GW_Task::singleton()->findAll(false, ['group_by'=>'name','select'=>'count(*) as cnt, `name`','return_simple'=>1, 'key_field'=>'name']);
		
			foreach($list as $item)
				$item->counts = $counts[$item->name]['cnt'] ?? 0;
		}
		
		file_put_contents('/tmp/sms_tasks_debug', json_encode([$_GET, $_SERVER['REQUEST_URI']]));
	}
	
	function __eventAfterListParams(&$params)
	{
		$this->tpl_vars['grouped']=0;
		
		if(!$params['conditions']){
			$params['conditions'] = '`newest`=1';
			$this->tpl_vars['grouped']=1;
		}
		
		
	}

	
	function loadTasksList()
	{
		$tasks = glob(GW::s('DIR/ROOT').'daemon/tasks/*'); 
		
		
		foreach($tasks as $i => $task)
			$tasks[$i] = str_replace('.task.class','' ,pathinfo($task, PATHINFO_FILENAME));
			
		$this->tpl_vars['tasks']= $tasks;	
	}	

	/*Nusiusti signala system procesui vykdyti uzduoti*/
	
	function doRunTask()
	{
		GW_Task::addSingleStatic($_REQUEST['task'], Array('debug'=>1));

		$this->jump();
	}
	
	/* Paleisti uzduoti ir gauti atsakyma narsykleje*/
	
	function doRunTaskDirect()
	{
		GW_Tasks_App::runDirect($_REQUEST['task'], Array('debug'=>1));
	}	

	function doRemoveAll()
	{
		while($list = $this->model->findAll('running=0',Array('limit'=>1000)))
		{
			foreach($list as $item)
				$item->delete();
				
			$count +=count($list);
		}
			
		$this->setPlainMessage("Removed items: ".$count);
		$this->jump();
	}
	
	function doHaltTask()
	{
		$item = $this->getDataObjectById();
		
		$_REQUEST['id']=$item->running;
		
		$this->dohaltProc();
	}	
	
	function doRestartSystem()
	{
		$t = new GW_Timer();
		
		$oldpid = GW_App_System::getRunningPid();
		
		GW_App_System::runSelf(true);
		
		
		
		$i=0;
		while(true)
		{
			$runningpid=GW_App_System::getRunningPid();
			
			if($runningpid && $runningpid!=$oldpid)
			{
				$this->setPlainMessage('Restart ok - '.$t->stop().'s');
				break;
			}
			
			$i++;
			usleep(100000);//0.5sec
			
			if($i>50){ // 5s timeout
				$this->app->setError('Failed to restart');
				break;
			}
		}
		
		
		$this->jump();
	}
	
	function viewProcesses()
	{
		$_GET['clean']=1;
		$this->list_params['paging_enabled']=0;
		
		$list = GW_Dummy_Data_Object::buildListStatic(GW_Proc_Ctrl::getRunningProcesses());
		
		return Array('list'=>$list);
	}
	
	function doHaltProc()
	{
		$pid=$_REQUEST['id'];
		
		$all = GW_Proc_Ctrl::getRunningProcesses();
		
		if(!isset($all[$pid]))
		{
			$this->setError("Fail to terminate. Process ($pid) not found");
			$this->jump();
		}
		
		if($_REQUEST['sigkill'])
			$return=posix_kill($pid, 9); //hard
		else
			$return=posix_kill($pid, 15); //soft
			
			
		$this->setPlainMessage("Task done: ".$return);
		
		$this->jump();
	}
	
	
	function doShowLogs()
	{
		$taskname=$_REQUEST['task'];
		
		$this->list_params['filters']['name']=Array('=',$taskname);
		
		$this->jump();
	}
}

