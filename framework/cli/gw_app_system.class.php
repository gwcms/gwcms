<?php

class GW_App_System Extends GW_App_Base
{

	var $forked_methods = Array();
	var $one_instance = true;
	public $plugins=[];

	function init()
	{
		$this->initDb();

		$this->config = new GW_Config('system_app/');
		$this->config->pid = getmypid();


		$this->registerInnerMethod('actionDoTasks', 5);
		$this->registerInnerMethod('actionCronTasks', 60, time() - 55); //first execution after 5secs
		$this->registerInnerMethod('actionTimeMessage', 3600, time() - 3601); //first execution immediately


		pcntl_signal(SIGUSR1, array(&$this, "forceDoTasks"));

		$this->msg('Hello');
		
		$this->runPlugins();
	}
	
	function runPlugins()
	{
		$plugins = GW::s('SYSTEM_DAEMON_PLUGINS');
		
		if(!$plugins)
			return false;
		
		foreach($plugins as $name)
		{
			$fname = "GW_App_System_".$name;
			$plugin = new $fname($this);
			$this->plugins[$name] = $plugin;
			
			$plugin->init();
			
			$this->msg("Adding plugin $name");
		}
	}

	static function getRunningPid()
	{
		$cfg = GW::getInstance('GW_Config');
		$pid = $cfg->get('system_app/pid');

		if (GW_Proc_Ctrl::isRunning($pid, 'system.php'))
			return $pid;
	}

	static function triggerUSR1()
	{
		if ($pid = self::getRunningPid()) {
			GW_App_Base::sendSignal($pid, 10);
			return true;
		} else {
			//dump('system.php Not running');
			self::runSelf();
		}
	}

	static function startIfNotStarted()
	{
		if ($pid = self::getRunningPid()) {
			return false;
		} else {
			self::runSelf();
			return true;
		}
	}

	static function runSelf($restart = false)
	{
		$cmd = GW::s('DIR/ROOT') . "daemon/system.php";

		if ($restart)
			$cmd.=' -terminate';

		GW_Proc_Ctrl::startDaemon($cmd, GW::s('DIR/LOGS') . 'system.log');
	}

	function action0()
	{
		parent::action0();

		//$this->interface->process();
	}

	function forceDoTasks()
	{
		$this->msg('force do tasks!');

		sleep(1); //0.3sec

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

		$this->process_pid_file = GW::s('DIR/TEMP') . 'app_' . $this->proc_name . '_' . md5($this->path);
	}

	/**
	 * match example
	 * ..:05:.. 
	 * 	valanda:'..' (match'ins betkuria valanda)
	 *  minute: '05' (match'ins kai laikas bus 5minutes)
	 *  sekunde: '..' (matchins betkuria sekunde)
	 *  
	 *  kiti pavyzdziai 
	 *  	"0\d" matchins betkuri skaiciu 01,02,03,...,09
	 *  	".[02468]" matchins lyginius skaicius 00,02,04,...58
	 *  
	 *  intervalas nurodomas tam kad uzduotis nebutu vykdoma dar kart netrukus
	 *  galima nurodyti time match kad butu vykdoma kiekvienos valandos pirma minute
	 *  bet intervalas kas dvi valandas, jeigu ivyktu klaida ir nebutu ivykdyta pirma valanda butu ivykdoma antra
	 *  
	 *  metodas patikrina intervala ir jeigu matchina tai issaugo kad metodas yra dabar ivykdytas
	 *  
	 *  galima paleisti skripta nurodzius intervala cron.php 
	 *   
	 */
	function checkAndRunInterval($time_match, $interval)
	{
		$config = GW_Config::singleton();

		if (strpos($time_match, ' ') === false)
			$time_match = '....-..-.. ' . $time_match;

		$match = preg_match("/$time_match/", date('Y-m-d H:i:s'), $m) ? 1 : 0;
		
		$last_exec = $config->get($cron_id = "ctask $time_match $interval");



		$dif = time() - strtotime($last_exec);

		//debug
		echo "lastexec $time_match#$interval - $last_exec\n";
		echo "diff: $dif\n";
		echo "exec?: ".($match && $dif >= $interval * 60 ?'yes':'no')."\n";
		


		if ($match && ($dif >= $interval * 60 ) || (isset($GLOBALS['argv'][1]) && $GLOBALS['argv'][1] == $interval)) {
			$this->msg('[' . date('H:i:s') . "] run $interval");
			$config->set($cron_id, date('Y-m-d H:i:s'));
			return true;
		} else {
			return false;
		}
	}

	function actionCronTasks()
	{
		$crontask0 = new GW_CronTask;
		$time_matches = $crontask0->getAllTimeMatches();
		
		print_r($time_matches);

		foreach ($time_matches as $tm) {
			list($time_match, $interval) = explode('#', $tm);

			if (self::checkAndRunInterval($time_match, $interval)) {
				//run all interval tasks
				echo "Run $tm\n";

				$inner = $crontask0->getByTimeMatchExecute($tm);

				foreach ($inner as $task) {
					if (file_exists($f = GW::s('DIR/ROOT') . 'daemon/tasks/' . $task->name . ".inner.php")) {
						$t = new GW_Timer();
						include $f;
						$this->msg($msg = "Inner task: " . $task->name . ", speed: " . $t->stop());
					} else {
						$this->msg($msg = "Inner not found: " . $task->name." ($f)");
					}

					echo "$msg\n";
				}
			}
		}
	}
}
