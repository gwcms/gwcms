<?php

class Shop_ShipPrice extends GW_Data_Object
{
	public $table = 'shop_shipprice';	
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