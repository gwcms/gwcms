<?php

class Tasks_Health_Task extends GW_Tasks_App
{	
	
	var $max_execution_time=500;
	
	
	function checkTimeLimits()
	{
		$task0 = new GW_Task;
		$list = $task0->getOverTimeLimit();
		
		if(!count($list))
			return true;
			
		
		foreach($list as $item)
		{
			
			//wipeout ghosts
			if(!$item->checkRunning())
			{
				$this->msg("System error. Not closed task {$item->id}:{$item->running}:{$item->name}");
				$this->error_code++;
				
				$item->running=0;
				$item->update(Array('running'));
				
				continue;
			}			
			
			
			$item->procKill();
			$this->msg("Try kill {$item->id}:{$item->running}:{$item->name}");
			sleep(2);
			
			if(!$item->checkRunning())
				continue;
				
			$item->procKill(true);
			$this->msg("Try hard kill");
			sleep(1);
			
			if(!$item->checkRunning())
				continue;
			
			$this->msg("Fail to kill");
			$this->error_code++;
		}
	
	}
	
	function process()
	{
		$this->checkTimeLimits();
		
		if(!$this->error_code)
			$this->msg("Smooth we go! No errors!");
		
	}
}