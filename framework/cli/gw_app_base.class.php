<?php

class GW_App_Base {

	var $proc_name;
	var $path;
	var $params;
	var $timers = Array();
	var $STOP_SIGNAL = false;
	var $develop = false;
	var $start_time;
	var $CAN_QUIT = true;
	var $proc_act_delay = 200000;
	var $process_pid_file;
	var $one_instance = false;
	var $kill_old_instance = true;
	var $stop_process = false;

	function initDb() {
		GW::db();
	}

	function __construct() {
		$this->initSignals();
		$this->start_time = microtime(1);

		$this->parseParams();
		$this->path = $GLOBALS['argv'][0];
		$this->proc_name = basename($this->path);

		$this->params = $this->parseParams();

		$this->setPidFile();

		$this->killOldInstance();

		if (is_callable(Array($this, 'init')))
			$this->init();


		$this->process();
	}

	function initSignals() {

		//on windows OS this will not work
		//if(OS_WIN) return;

		declare(ticks = 1);

		pcntl_signal(SIGINT, array(&$this, "sigHandler"));
		pcntl_signal(SIGWINCH, array(&$this, "sigHandler"));
		pcntl_signal(SIGHUP, array(&$this, "sigHandler"));
		pcntl_signal(SIGQUIT, array(&$this, "sigHandler"));
		pcntl_signal(SIGTERM, array(&$this, "sigHandler"));
		pcntl_signal(SIGCHLD, array(&$this, "sigHandler"));
	}

	function setPidFile() {
		//proc_name + md5(path) + 1st argument

		$this->process_pid_file = GW::s('DIR/TEMP') . 'app_' . $this->proc_name . '_' . md5($this->path . ' ' . (isset($GLOBALS['argv'][1]) ? $GLOBALS['argv'][1] : ''));
	}

	//uzregistruoti tameri nurodyti 
	//pavadinima - kad galetu veliau atjungti taimerio vykdyma
	//callback - ka vykdyti
	//interval - kas kiek laiko vykdyti
	//exec1st iskart ivykdys taimerius per pirma pakvietima
	function registerTimer($id, $callback, $interval, $exec1st=false) {
		$this->timers[$id] = Array('interval' => $interval, 'callback' => $callback, 'lastexec' => !$exec1st ? 0 : microtime(1));
	}

	function unregisterTimer($id) {
		unset($this->timers[$id]);
	}

	function registerInnerMethod($name, $interval) {
		$this->registerTimer($name, Array(&$this, $name), $interval);
	}

	static function parseParams() {
		$params = array();
		foreach ($GLOBALS['argv'] as $arg)
			if (preg_match('/--(.*?)=(.*)/', $arg, $reg))
				$params[$reg[1]] = $reg[2];
			elseif (preg_match('/-([a-z0-9_-]*)/i', $arg, $reg))
				$params[$reg[1]] = true;

		return $params;
	}

	function action0() {
		if ($this->STOP_SIGNAL)
			$this->quit();
	}

	function processTimers() {
		$this->action0();

		foreach ($this->timers as $id => $timer) {

			if (microtime(1) - $timer['lastexec'] < $timer['interval'])
				continue;

			$this->timers[$id]['lastexec'] = microtime(true);
			call_user_func($timer['callback']);
		}
	}

	function process() {
		while (1) {
			$this->processTimers();
			usleep($this->proc_act_delay);
		}
	}

	function uptime($past = Null) {
		if (!$past)
			$past = $this->start_time;

		return microtime(true) - $past;
	}

	function memUsage() {
		return GW_Math_Helper::cFileSize(memory_get_usage());
	}

	function sigHandler($signo) {
		switch ($signo) {
			case SIGCHLD:pcntl_waitpid(-1, $status);
				break;

			case SIGINT:;
			case SIGWINCH:;
			case SIGQUIT:;
			case SIGTERM:;
			case SIGHUP: $this->STOP_SIGNAL = true;
				break;


			default: $this->msg("Unhandled signal: $signo");
		}

		if ($this->STOP_SIGNAL && $this->CAN_QUIT)
			$this->quit();
	}

	function quit($exit = 1) {
		$this->removePidFile();
		self::msg('goodbye');

		if ($exit)
			exit;
	}

	/**
	 * send signal via kill command,
	 * if it is needed check if application running by sending part of commandline 
	 * for example GW_App_Base::sendSignal(123132, SIGUSR1, 'system.php')
	 */
	static function sendSignal($pid, $signalNo, $test_is_running = false) {
		if ($test_is_running && !GW_Proc_Ctrl::isRunning($pid, $test_is_running))
			return $this->msg('Fail. "' . $test_is_running . '" process is not running');

		//$output = shell_exec("kill -$signalNo $pid");

		$output = posix_kill($pid, $signalNo);

		return $output;
	}

	function timeMessage() {
		$this->msg('Current time: ' . date('Y-m-d H:i:s'));
	}

	function msg($msg) {
		if (is_array($msg))
			$msg = json_encode($msg, JSON_PRETTY_PRINT);

		echo (date('i:s') . ' ' . $msg . "\n");
	}

	function getLastProcPid() {
		return (int) @file_get_contents($this->process_pid_file);
	}

	function writePid() {
		file_put_contents($this->process_pid_file, getmypid());
	}

	function killOldInstance() {
		if ($this->one_instance) {
			$pid = $this->getLastProcPid();

			if ($pid && GW_Proc_Ctrl::isRunning($pid, $this->proc_name)) {

				if ($this->kill_old_instance && isset($this->params['terminate'])) {
					self::msg("Terminating old instance (pid: $pid)");

					if (!GW_Proc_Ctrl::killWait($pid, $this->proc_name, 30, true))
						die("Can't kill");
				}else {
					self::msg('Cant run. Another instance already running. Add -terminate if you want to kill running instance');
					$this->quit();
				}
			}
		}

		$this->writePid();
	}

	function removePidFile() {
		if (@file_get_contents($this->process_pid_file) == getmypid())
			unlink($this->process_pid_file);
	}

	static function readStdIn() {
		return trim(fgets(STDIN));
	}

}

