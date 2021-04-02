<?php

class Shop_Executers extends GW_i18n_Data_Object
{
	public $table = 'shop_executers';	
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
	
	function getPrintPrice($prodid, $qty)
	{		
		return shop_execprice::singleton()->find(['owner_id=? AND product_id=? AND qty_min<=? AND qty_max>=?', $this->id, $prodid, $qty, $qty]);
	}
	

}