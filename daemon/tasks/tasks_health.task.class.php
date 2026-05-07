<?php

class Tasks_Health_Task extends GW_Tasks_App
{	
	
	var $max_execution_time=500;
	
	function cleanupPickedTasks()
	{
		$task0 = new GW_Task;
		$olderThan = date('Y-m-d H:i:s', strtotime('-15 minute'));
		$list = $task0->findAll("running=-2 AND `time` < '".GW::db()->escape($olderThan)."'", ['limit' => 200]);

		foreach ($list as $item) {
			$this->msg("Reset stale picked task {$item->id}:{$item->name}");
			$item->running = 0;
			$item->error_code = 604;
			$item->error_msg = 'Stale picked task reset by tasks_health';
			$item->finish_time = date('Y-m-d H:i:s');
			$item->update(Array('running', 'error_code', 'error_msg', 'finish_time'));
		}
	}

	function cleanupHistory()
	{
		$task0 = new GW_Task;
		$olderThan = date('Y-m-d H:i:s', strtotime('-30 day'));
		$list = $task0->findAll("running=0 AND newest=0 AND finish_time!='0000-00-00 00:00:00' AND finish_time < '".GW::db()->escape($olderThan)."'", ['limit' => 1000]);

		foreach ($list as $item)
			$item->delete();

		if (count($list))
			$this->msg('Removed old finished tasks: '.count($list));
	}
	
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
		$this->cleanupPickedTasks();
		$this->cleanupHistory();
		$this->checkTimeLimits();
		
		if(!$this->error_code)
			$this->msg("Smooth we go! No errors!");
		
	}
}
