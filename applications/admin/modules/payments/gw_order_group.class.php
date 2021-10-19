<?php

class GW_Order_Group extends GW_Composite_Data_Object
{
	
	public $composite_map = [
		'items' => ['gw_related_objecs', ['object'=>'GW_Order_Item','relation_field'=>'group_id']],
		'pay_confirm' => ['gw_composite_linked', ['object'=>'GW_Paysera_Log','relation_field'=>'pay_confirm_id']],
		'user' => ['gw_composite_linked', ['object'=>'GW_Customer','relation_field'=>'user_id']],
		'banktransfer_confirm'=>['gw_image', ['dimensions_resize' => '1024x1024', 'dimensions_min' => '100x100']],
	];		
	
	public $encode_fields = [
	    'extra'=>'jsono',
	];	
	
	public $calculate_fields = [
	    'title'=>1,
	    'keyval'=>1
	];
	
	
	public $ownerkey = 'payments/ordergroups';
	public $extensions = [
	    'changetrack'=>1,
	    'keyval'=>1
	];				
	public $keyval_use_generic_table = 1;	
	
	public $ignore_fields = [
		'keyval' => 1
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
	
	
	function setSecretIfNotSet()
	{
		if(!$this->secret){
			$this->secret = GW_String_Helper::getRandString(8,GW_String_Helper::$simple);
			$this->updateChanged();
		}		
	}
	
	
	function calculateField($key) 
	{
		

		switch ($key) {
			case 'keyval':
				return $this->extensions['keyval'];
			break;	
			case 'title':
				return "#".$this->id." ".($this->payment_status==7? 'PAYD':"NOPAY").' '.$this->amount_total.' EUR';
			break;
		}
		
			
		
		
		return parent::calculateField($name);
	}
	
}