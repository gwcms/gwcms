<?php

class GW_Model_Template extends GW_Data_Object
{
	public $table = 'gw_model_table';
	
	public $i18n_fields = [];	
	
	public $calculate_fields = [];


	

	
	
	function calculateField($name) {
		
		switch ($name)
		{
			case "fieldname";
			break;
		}
		
		parent::calculateField($name);
	}	
	
	function eventHandler($event, &$context_data = [])
	{
		switch ($event) {
			case 'BEFORE_SOMETHING':
			break;
		}
		
		parent::eventHandler($event);
	}	
	

}