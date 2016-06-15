<?php

class GW_App_Base
{

	public $proc_name;
	public $path;
	public $params;
	public $timers = Array();
	public $STOP_SIGNAL = false;
	public $develop = false;
	public $start_time;
	public $CAN_QUIT = true;
	public $proc_act_delay = 200000;
	public $process_pid_file;
	public $one_instance = false;
	public $kill_old_instance = true;
	public $stop_process = false;
	public $collect_messages = false;
	public $messages_buffer = '';

	function __construct()
	{
		$this->initSignals();
		$this->start_time = microtime(1);

		$this->parseParams();
		$this->path = $GLOBALS['argv'][0];
		$this->proc_name = basename($this->path);

		$this->params = $this->parseParams();
		$this->setPidFileName();

		if (isset($this->params['terminate_only'])) {
			$this->killOldInstance();
			$this->quit();
		}


		$this->checkSingleInstance();

		stream_set_blocking(STDIN, false);

		if (is_callable(Array($this, 'init')))
			$this->init();


		$this->process();
	}

	function initDb()
	{
		GW::db();
	}

	function initSignals()
	{

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

	function setPidFileName()
	{
		//proc_name + md5(path) + 1st argument
		$this->process_pid_file = '/tmp/gw_app_' . $this->proc_name . '_' . md5($this->path);
	}

	//uzregistruoti tameri nurodyti 
	//pavadinima - kad galetu veliau atjungti taimerio vykdyma
	//callback - ka vykdyti
	//interval - kas kiek laiko vykdyti
	//exec1st iskart ivykdys taimerius per pirma pakvietima
	function registerTimer($id, $callback, $interval, $exec1st = false)
	{
		$this->timers[$id] = Array('interval' => $interval, 'callback' => $callback, 'lastexec' => $exec1st ? $exec1st : microtime(1));
	}

	function unregisterTimer($id)
	{
		unset($this->timers[$id]);
	}

	//uzregistruoti vidini programos klases metoda vykdymui kas x sekundziu
	function registerInnerMethod($name, $interval, $exec1st = false)
	{
		$this->registerTimer($name, Array(&$this, $name), $interval, $exec1st);
	}

	//parametru pavyzdziai --bananu_skaicius=5 -rodyti_bananus

	function parseParams()
	{
		$params = array();
		foreach ($GLOBALS['argv'] as $arg)
			if (preg_match('/--(.*?)=(.*)/', $arg, $reg))
				$params[$reg[1]] = $reg[2];
			elseif (preg_match('/-([a-z0-9_-]*)/i', $arg, $reg))
				$params[$reg[1]] = true;

		return $params;
	}

	function action0()
	{
		if ($this->STOP_SIGNAL)
			$this->quit();
	}

	//taimeriai vykdomi 
	//jei skirtumas tarp 
	//paskutinio vykdimo laiko ir 
	//dabarties yra didesnis nei 
	//taimerio nustatytas intervalas (sekundemis)

	function processTimers()
	{
		$this->action0();

		foreach ($this->timers as $id => $timer) {

			if (microtime(1) - $timer['lastexec'] < $timer['interval'])
				continue;

			$this->timers[$id]['lastexec'] = microtime(true);
			call_user_func($timer['callback']);
		}
	}

	function process()
	{
		while (1) {
			$this->processTimers();
			usleep($this->proc_act_delay);
		}
	}

	function uptime($past = Null)
	{
		if (!$past)
			$past = $this->start_time;

		return microtime(true) - $past;
	}

	function memUsage()
	{
		return GW_Math_Helper::cFileSize(memory_get_usage());
	}

	function sigHandler($signo)
	{
		$this->msg('SIG:' . $signo);

		switch ($signo) {
			case SIGCHLD:pcntl_waitpid(-1, $status);
				break;

			case SIGWINCH:; //kai ivyksta konsoles resaizinimas
				break;
			case SIGINT:;
			case SIGQUIT:;
			case SIGTERM:;
			case SIGHUP: $this->STOP_SIGNAL = true;
				break;


			default: $this->msg("Unhandled signal: $signo");
		}

		if ($this->STOP_SIGNAL && $this->CAN_QUIT)
			$this->quit();
	}

	function quit($exit = 1)
	{
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
	static function sendSignal($pid, $signalNo, $test_is_running = false)
	{
		if ($test_is_running && !GW_Proc_Ctrl::isRunning($pid, $test_is_running))
			return $this->msg('Fail. "' . $test_is_running . '" process is not running');

		//$output = shell_exec("kill -$signalNo $pid");

		$output = posix_kill($pid, $signalNo);

		return $output;
	}

	function timeMessage()
	{
		$this->msg('Current time: ' . date('Y-m-d H:i:s'));
	}

	function enableConsoleMessages()
	{
		if ($this->collect_messages) {
			$this->collect_messages = false;
			$this->outputCollectedMessages();
			$this->msg('Console messages enabled');
		}
	}

	function toogleConsoleMessages()
	{
		if ($this->collect_messages) {
			$this->enableConsoleMessages();
		} else {
			$this->msg('Console messages disabled');
			$this->collect_messages = true;
		}
	}

	function msg($msg)
	{

		static $lastmsg;

		$pre = date('i:s') . ' ';
		$post = "\n";


		if ($lastmsg == '.' && $msg != '.') {
			$pre = "\n" . $pre;
		} elseif ($msg == '.' && $lastmsg == '.') {
			$pre = "";
			$post = "";
		} elseif ($msg == "." && $lastmsg != '.') {
			$post = "";
		}


		$lastmsg = $msg;


		//su kiekviena zinute rodyti laika (minute:sekunde)
		$str = $pre . (is_array($msg) ? json_encode($msg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $msg) . $post;

		if ($this->collect_messages)
			$this->messages_buffer.=$str;
		else
			echo $str;

		return $str;
	}

	function outputCollectedMessages()
	{
		echo $this->messages_buffer;
		$this->messages_buffer = '';
	}

	//tam kad galetume zinoti ar jau vykdomas procesas, kad turetume galimybe nutraukti ji
	function getLastProcPid()
	{
		return (int) @file_get_contents($this->process_pid_file);
	}

	function writePid()
	{
		//$this->msg("write pid ".getmypid());
		file_put_contents($this->process_pid_file, getmypid());
	}

	function killOldInstance()
	{
		$pid = $this->getLastProcPid();

		//$this->msg("kill $pid");

		if (!GW_Proc_Ctrl::killWait($pid, $this->proc_name, 30, true))
			$this->msg("Can't kill pid: $pid, procname: $this->proc_name");
	}

	function checkSingleInstance()
	{


		if ($this->one_instance) {
			$pid = $this->getLastProcPid();

			//$this->msg("lastpid $pid\n");
			//$this->msg("lastproc ($pid)({$this->proc_name}) runngin: ".(GW_Proc_Ctrl::isRunning($pid, $this->proc_name)?1:0));



			if ($pid && GW_Proc_Ctrl::isRunning($pid, $this->proc_name)) {
				if ($this->kill_old_instance && isset($this->params['terminate'])) {
					$this->msg("Terminating old instance (pid: $pid)");
					$this->killOldInstance();
				} else {
					$this->msg('Cant run. Another instance already running. Add -terminate if you want to kill running instance');
					$this->quit();
				}
			}
		}


		$this->writePid();
	}

	function removePidFile()
	{
		if (@file_get_contents($this->process_pid_file) == getmypid())
			unlink($this->process_pid_file);
	}

	static function readStdIn()
	{
		return trim(fgets(STDIN));
	}

	/**
	 * this helps to open child process which dies after executor process exits
	 * wait - seconds, wait for finish
	 * - if another proccess is open by proc_open and is still running
	 *   do not use proc_close;
	 */
	static function startProc($cmd, $wait = 0)
	{
		$proc = proc_open($cmd, [], $pipes);

		if ($wait) {
			for ($i = $wait; $i > 0; $i--) {
				$status = proc_get_status($proc);
				if (!$status['running'])
					break;

				usleep(100000);
			}
		}

		//$exit=proc_close($proc);
		//echo "exitcode $exit\n\n";		


		return $proc;
	}

	public $stdin_buff;

	function readStdInInput()
	{
		while (true) {
			$c = fgetc(STDIN);

			if ($c !== false) {
				if ($c != "\n") {
					$this->stdin_buff .= $c;
				} else {
					$this->inputCMD($this->stdin_buff);
					$this->stdin_buff = '';
				}
			} else {
				break;
			}
		}
	}

	function actionTimeMessage()
	{
		$this->msg('Time: ' . date('Y-m-d H:i:s'));
	}
}
