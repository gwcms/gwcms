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
			
		$errors=0;
		
		foreach($list as $item)
		{
			
			//wipeout ghosts
			if(!$item->checkRunning())
			{
				$this->msg("System error. Not closed task {$item->id}:{$item->running}:{$item->name}");
				$errors++;
				
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
			$this->error_code='601';
		}
		
		if($errors)
			$this->msg("Found problems $errors");
	
	}
	
	
	function cleanUpHistory()
	{
		$max = (int)GW_Config::singleton()->get('sys/max_tasks_history_length');
		
		if(!$max)
			return false;
		
		$rows = GW::db()->fetch_rows("SELECT count(*) as `cnt`, `name`, `error_code` FROM `gw_tasks` GROUP BY `name`, error_code HAVING `cnt` > $max");
		
		
		foreach($rows as $row)
		{
			$del = $row['cnt']-$max;
			$q = "DELETE FROM `gw_tasks` WHERE `name`=? AND `error_code`=? ORDER BY `insert_time` ASC LIMIT $del";
			GW::db()->query(GW_DB::prepare_query([$q, $row['name'],$row['error_code']]));
		}
		
		$this->msg($rows);
	}
	
	function process()
	{
		$this->checkTimeLimits();
		$this->cleanUpHistory();		
	}
}