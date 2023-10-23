<?php

class Shop_Executors extends GW_i18n_Data_Object
{
	public $calculate_fields = [];


	public $composite_map = [
		'execprice' => ['gw_related_objecs', ['object'=>'shop_execprice','relation_field'=>'owner_id']],
		'shipprice' => ['gw_related_objecs', ['object'=>'shop_shipprice','relation_field'=>'owner_id']],
	];		

	
	function calculateField($name) {
		
		switch ($name)
		{
			case "fieldname";
			break;
		}
		
		parent::calculateField($name);
	}	
	
	function getExecPrice($prodid, $qty)
	{		
		return shop_execprice::singleton()->find(['owner_id=? AND (product_id=? OR product_id=0) AND qty_min<=? AND qty_max>=?', $this->id, $prodid, $qty, $qty]);
	}
	
	function getShipPrice($prodid, $qty)
	{		
		return Shop_ShipPrice::singleton()->find(['owner_id=? AND (product_id=? OR product_id=0) AND qty_min<=? AND qty_max>=?', $this->id, $prodid, $qty, $qty]);
	}	

}