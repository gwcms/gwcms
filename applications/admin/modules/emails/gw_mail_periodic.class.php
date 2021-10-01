<?php

class GW_Mail_Periodic extends GW_Composite_Data_Object
{

	public $encode_fields = ['groups'=>'json'];
	
	public $validators = ['params' => 'gw_json'];
	
	
	public $composite_map = [
	    'template' => ['gw_composite_linked', ['object'=>'GW_Mail_Template','relation_field'=>'template_id']],
	];	

	function getAllTimeMatches()
	{
		$times = $this->getDB()->fetch_one_column("SELECT DISTINCT time_match FROM `{$this->table}` WHERE active =1", 'time_match');

		return $times;
	}

	function getByTimeMatch($tm)
	{
		return $this->findAll(Array('active=1 AND time_match=?', $tm));
	}

	function execute()
	{
		GW_Task::singleton()->add($this->name, json_decode($this->params, true));
	}

	function getByTimeMatchExecute($tm)
	{
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
	
	function getGroups()
	{
		return GW_NL_Group::singleton()->findAll(GW_DB::inCondition('id', $this->groups));
	}
}
