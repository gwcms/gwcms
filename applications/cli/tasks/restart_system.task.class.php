<?php

class Restart_System_Task extends GW_Tasks_App
{	
	function process()
	{
		
		$pid = GW_App_System::getRunningPid();
		GW_App_System::runSelf();
		
		sleep(1);
		
		$new_pid = GW_App_System::getRunningPid();
		
		if($pid != $new_pid)
			$this->msg("Success. New pid: $return");
		else{
			$this->error_code=6;
			$this->error_message="Failed";
		}
	}
}