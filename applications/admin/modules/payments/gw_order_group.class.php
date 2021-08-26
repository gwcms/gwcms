<?php

class GW_Order_Group extends GW_Composite_Data_Object
{
	
	public $composite_map = [
		'items' => ['gw_related_objecs', ['object'=>'GW_Order_Item','relation_field'=>'group_id']],
		'pay_confirm' => ['gw_composite_linked', ['object'=>'GW_Paysera_Log','relation_field'=>'pay_confirm_id']],
		'user' => ['gw_composite_linked', ['object'=>'GW_Customer','relation_field'=>'user_id']],
	];		
	
	public $encode_fields = [
	    'extra'=>'jsono',
	];	
	
	public $calculate_fields = [
	    'banktransfer_allow'=>1
	];
	
	
	function updateTotal()
	{
		$amount = 0;
		$deliverable = 0;
		
		if($relobj = $this->getCompositeObject('items'))
			$relobj->cleanCache();
		
	
		
		foreach($this->items as $item){
			$amount+= $item->unit_price*$item->qty;
			$deliverable = max($item->deliverable, $deliverable);
		}
		
		
		$this->deliverable = $deliverable;
		$this->amount_items = $amount;
		$this->amount_total = $this->amount_items + $this->amount_shipping;
		$this->updateChanged();
	}
	
	function addItem(GW_Order_Item $item){
		$item->group_id = $this->id;
		$item->save();
		
		$this->updateTotal();
		
	}
	
	function eventHandler($event, &$context_data=[]) {
		
		switch($event){
			case 'BEFORE_DELETE';
				foreach($this->items as $item)
					$item->delete();
			break;
		}
		
		return parent::eventHandler($event, $context);
	}	
	
	
	function getItem($item)
	{
		$class = strtolower(get_class($item));
		if($this->items)
		foreach($this->items as $citem)
			if($citem->obj_type == $class && $citem->obj_id==$item->id){
				return $citem;
			}
	}
	
	
	function calculateField($name) 
	{
		
		if($name=='banktransfer_allow'){
			
			
			$ret = true;
			
			foreach($this->items as $item){
				//d::ldump($item);
				if(!$item->banktransfer_allow){	$ret = false; break; }
			}
				
			
			
			return $ret;
		}
		
		return parent::calculateField($name);
	}
	
}