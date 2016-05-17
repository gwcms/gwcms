<?php

class GW_CronTask extends GW_Data_Object {

	var $table = 'gw_crontasks';
	var $validators = Array('params' => 'gw_json');

	function getAllTimeMatches() {
		$times = $this->getDB()->fetch_one_column("SELECT DISTINCT time_match FROM `{$this->table}` WHERE active =1", 'time_match');

		return $times;
	}

	function getByTimeMatch($tm) {
		return $this->findAll(Array('active=1 AND time_match=?', $tm));
	}

	function execute() {
		GW_Task::singleton()->add($this->name, json_decode($this->params, true));
	}

	function getByTimeMatchExecute($tm) {
		$list = $this->getByTimeMatch($tm);

		$inner_run = [];

		foreach ($list as $item) {
			if ($item->separate_process) {
				$item->execute();
				echo "SEP exec: {$item->name}\n";
				
			} else {
				$inner_run[] = $item;
			}
		}

		return $inner_run;
	}

}
