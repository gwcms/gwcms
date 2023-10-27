<?php

class Shop_DiscountCode extends GW_i18n_Data_Object
{	
	public $calculate_fields = [
	    'title'=>1, 
	    'product_ids'=>1
	];

	public $ownerkey = 'payments/discountcode';
	public $extensions = [
	    'changetrack'=>1,
	    //'keyval'=>1
	];				
	public $keyval_use_generic_table = 1;	
	

	
	function calculateField($name) {
		
		switch ($name)
		{
			case "title";
				return $this->code;
			break;
			case 'product_ids':
				return json_decode($this->products, true);
			break;
		}
		
		parent::calculateField($name);
	}	
	
	function getPrintPrice($prodid, $qty)
	{		
		return shop_execprice::singleton()->find(['owner_id=? AND product_id=? AND qty_min<=? AND qty_max>=?', $this->id, $prodid, $qty, $qty]);
	}
	
	
	function getUniqueCode($length=6)
	{
		$retry = 10000;
		while($retry>0){
			$code = strtoupper(GW_String_Helper::getRandString($length));
			$retry--;
			
			if(!$this->count(['code=?',$code])){
				return $code;
			}
		}
	}
	
	function eventHandler($event, &$context_data = array()) {
		
		switch($event){
			case "BEFORE_SAVE":
				if(!$this->code)
					$this->code = $this->getUniqueCode();
			break;
		}
		
		parent::eventHandler($event, $context_data);
	}
	
	function getTypes()
	{
		return  $this->getDB()->getColumnOptions($this->table, 'obj_type');
	}
}