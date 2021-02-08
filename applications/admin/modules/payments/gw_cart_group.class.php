<?php

class GW_Cart_Group extends GW_Composite_Data_Object
{
	
	public $composite_map = [
		'items' => ['gw_related_objecs', ['object'=>'GW_Cart_Item','relation_field'=>'group_id']],
	];		
	
	
	function updateTotal()
	{
		$amount = 0;
		
		foreach($this->items as $item){
			$amount+= $item->unit_price*$item->qty;
		}
		
		$this->amount = $amount;
		$this->updateChanged();
	}
	
	function addItem(GW_Cart_Item $item){
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
	
}