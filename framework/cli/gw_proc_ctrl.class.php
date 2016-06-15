<?php

class GW_Proc_Ctrl
{

	static function isRunning($pid, $procname)
	{
		$list = shell_exec("ps -p $pid -o cmd= 2>&1");

		return strpos($list, $procname) !== false;
	}

	static function getRunningProcesses()
	{
		//http://www.linux.ie/newusers/beginners-linux-guide/ps.php
		$out = shell_exec('ps -xo "%p - %a" 2>/dev/null');


		$out = explode("\n", $out);
		$proc = Array();


		foreach ($out as $i => $line)
			if (strpos($line, GW::s('DIR/ROOT')) === false) {
				unset($out[$i]);
			} else {
				list($procid, $cmd) = explode(' - ', $line, 2);
				$proc[trim($procid)] = Array('id' => trim($procid), 'cmd' => trim($cmd));
			}

		return $proc;
	}

	/**
	 * test_running: pries kilinant patikrinti ar veikia
	 * procname: patikrinti ar tikrai tas porceso pavadinimas
	 * kill: nurodyti po kiek sekundziu procesui nesustojus zudyma atlikti
	 * 
	 * grazinamos reiksmes
	 *  false - nepavyko
	 *  1 - sustabdytas su SIGTERM
	 *  2 - sustabdytas su SIGKILL
	 *  
	 */
	function terminate($params)
	{

		$pid = $params['pid'];
		$procname = $params['procname'];

		if (!self::isRunning($pid, $procname))
			return false;

		$m = isset($params['messages']) && $params['messages'];

		posix_kill($pid, 15);

		if ($m)
			echo "Terminate $pid $procname ";

		if (isset($params['kill'])) {
			$wait_seconds = $params['kill'];

			for ($cnt = $wait_seconds; $cnt > 0; $cnt--) {
				if (!self::isRunning($pid, $procname)) {
					if ($m)
						echo " OK\n";

					return 1;
				}

				if ($m)
					echo ".";
				sleep(1);
			}

			if ($m)
				echo "FAIL \nDo kill! ";

			posix_kill($pid, 9);

			//wait before result
			for ($cnt = 15; $cnt > 0; $cnt--) {
				if ($success = !self::isRunning($pid, $procname))
					break;

				if ($m)
					echo ".";
				usleep(100000);
			}

			if ($m)
				echo '' . ($success ? "OK" : "FAIL") . "\n";

			return $success ? 2 : false;
		}
	}

	static function killWait($pid, $proc_name, $wait = 30, $messages = false)
	{
		return self::terminate(Array('pid' => $pid, 'procname' => $proc_name, 'kill' => $wait, 'messages' => $messages));
	}

	static function startDaemon($cmd, $logfile)
	{
		shell_exec($cmd = "$cmd >> $logfile 2>&1 &");

		//dump($cmd);
	}

	static function sendSignal($pid, $signalNo, $test_is_running = false)
	{
		if ($test_is_running && !GW_App_Base::isRunning($pid, $test_is_running))
			return dump('Fail. "' . $test_is_running . '" process is not running');

		//$output = shell_exec("kill -$signalNo $pid");

		$output = posix_kill($pid, $signalNo);

		return $output;
	}
}
