<?php

class Shop_ExecPrice extends GW_Data_Object
{
	public $table = 'shop_execprice';	
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