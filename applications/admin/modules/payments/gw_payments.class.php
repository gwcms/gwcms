<?php

class GW_Payments extends GW_Data_Object
{

	
	public $calculate_fields = [
	    'admin_username'=>1,
	    'admin'=>1,
	];
	
	
	function calculateField($name) {
		
		switch ($name)
		{
			case "admin":
				return GW_User::singleton()->createNewObject($this->admin_id, 1);
			break;			
			case "admin_username":
				return $this->admin->username;
			break;
		}
		
		parent::calculateField($name);
	}	
	
	function eventHandler($event, &$context_data = [])
	{
		switch ($event) {
			case 'BEFORE_INSERT':
				
			break;
		}
		
		parent::eventHandler($event);
	}

	
	function getByIdCached($key)
	{
		static $optscache = [];
		
		if(!$optscache)
			$optscache = $this->findAll(false, ['key_field'=>'key']);
		
		return $optscache[$key];
	}
	
}