<?php



class GW_App_System Extends GW_App_Base
{

	var $forked_methods=Array();
	var $one_instance=true;

	
	function init()
	{
		$this->initDb();
		
		$this->config = new GW_Config('system_app/');
		$this->config->pid = getmypid();
		

		$this->registerInnerMethod('actionDoTasks', 5);	
		$this->registerInnerMethod('actionDoSendDefered', 60);	
		

		pcntl_signal(SIGUSR1, array(&$this,"forceDoTasks"));
		
		$this->msg('Hello');
	}

	
	
	static function getRunningPid()
	{
		$cfg = GW::getInstance('GW_Config');
		$pid = $cfg->get('system_app/pid');
		
		if(GW_Proc_Ctrl::isRunning($pid, 'system.php'))
			return $pid;
	}
	
	static function triggerUSR1()
	{	
		if($pid = self::getRunningPid())
		{
			GW_App_Base::sendSignal($pid, 10);
			return true;
		}else{
			//dump('system.php Not running');
			self::runSelf();
			
		}
	}
	
	static function startIfNotStarted()
	{
		if($pid = self::getRunningPid()){
			return false;
		}else{
			self::runSelf();
			return true;
		}
	} 
	
	
	static function runSelf($restart=false)
	{		
		$cmd =GW::s('DIR/ROOT')."daemon/system.php";
		
		if($restart)
			$cmd.=' -terminate';
		
		GW_Proc_Ctrl::startDaemon($cmd, GW::s('DIR/LOGS').'system.log');
	}
	
	
	function action0()
	{
		parent::action0();
		
		//$this->interface->process();
	}
	

	function actionCronTasks()
	{
			
	}
	
	function forceDoTasks()
	{
		$this->msg('force do tasks!');
		
		sleep(1);//0.3sec
		
		$this->actionDoTasks();
	}
	
	function actionDoTasks()
	{
		$count = GW_Tasks_App::checkAndRun();
		
		$this->msg("$count Tasks");
	}
	

	function quit($exit = 1)
	{
		//$this->interface->shutdown();
		
		parent::quit($exit);
	}

	
	function setPidFile()
	{
		//proc_name + md5(path) + 1st argument
		
		$this->process_pid_file = GW::s('DIR/TEMP').'app_'.$this->proc_name.'_'.md5($this->path) ;		
	}
	
	function backgroundRequest($path, $get_args=[])
	{
		$token = GW::getInstance('gw_temp_access')->getToken(GW_USER_SYSTEM_ID);
		
		$get_args['temp_access']=GW_USER_SYSTEM_ID.','.$token;
		$path .= (strpos($path,'?')===false ? '?' : '&') . http_build_query($get_args);
		
		GW_Http_Agent::impuls($url=GW::getInstance('GW_Config')->get('sys/project_url').$path);
		
		return $url;
	}	

}

