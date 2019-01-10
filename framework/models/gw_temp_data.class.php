<?php

class GW_Temp_Data extends GW_Data_Object
{

	public $table = 'gw_temp_data';

	function store($user_id, $group, $name, $value, $expires = '24 hour')
	{
		$item = $this->read($user_id, $group, $name);

		$vals = [
		    'user_id' => $user_id,
		    'group' => $group,
		    'name' => $name,
		    'value' => $value,
		    'expires' => date('Y-m-d H:i:s', strtotime('+' . $expires))
		];

		if (!$item) {
			$item = $this->createNewObject($vals);
			$item->insert();
		} else {
			$item->setValues($vals);
			$item->updateChanged();
		}

		return $item;
	}

	function cleanup()
	{
		$this->getDB()->query("DELETE FROM `$this->table` WHERE expires < '" . date('Y-m-d H:i:s') . "'");
	}

	function read($user_id, $group, $name)
	{
		return $this->find(GW_DB::buidConditions(['user_id' => $user_id, 'group' => $group, 'name' => $name]));
	}

	function readValue($user_id, $group, $name)
	{
		$item = $this->read($user_id, $group, $name);
		return $item ? $item->value : false;
	}
}
