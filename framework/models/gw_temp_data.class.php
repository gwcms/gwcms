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

	function readValue($user_id, $group, $name, &$expires=false)
	{
		
		$item = $this->read($user_id, $group, $name);
		$expires = $item ? $item->expires : false;
		
		return $item ? $item->value : false;
	}
	
	
	//required every_5_minute add
	//GW_Temp_Data::singleton()->cleanup();
	
	function rwCallback($opts=[], $callback){
		$user_id = $opts['user_id'] ?? GW_USER_SYSTEM_ID;
		$group = $opts['group'] ?? 'SYS';
		$name = $opts['name'];
		$expires = $opts['expires'] ?? '24 hour';
		$format = $opts['format'] ?? 'json';
		$renew = $opts['renew'] ?? false;
		
		if(!$renew && ($tmp = $this->read($user_id, $group, $name)) && $tmp->expires > date('Y-m-d H:i:s'))
		{
			$val = $tmp->value;
						
			if($format == 'json'){
				return json_decode($val, true);
			}elseif($format == 'serialize'){
				return unserialize($val);
			}
			
			return $val;
		}else{
			$dataraw = $callback();
				
			if($format == 'json'){
				$data =  json_encode($dataraw);	
			}elseif($format == 'serialize'){
				$data = serialize($dataraw);		
			}
			
			$this->store($user_id, $group, $name, isset($data) ? $data : $dataraw, $expires);
			
			return $dataraw;
		}
	}
}
