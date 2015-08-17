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

		pcntl_signal(SIGUSR1, array(&$this,"forceDoTasks"));
		
		$this->msg('Hello');
	}

	
	
	static function getRunningPid()
	{
		$cfg = GW_Config::singleton();
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
			dump('system already running');
		}else{
			self::runSelf();
		}
	} 
	
	
	static function runSelf()
	{
		GW_Proc_Ctrl::startDaemon(GW::$dir['ADMIN']."cli/system.php", GW::$dir['LOGS'].'system.log');
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


}

