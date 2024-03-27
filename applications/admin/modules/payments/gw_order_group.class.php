<?php

class GW_Order_Group extends GW_Composite_Data_Object
{
	
	public $composite_map = [
		'items' => ['gw_related_objecs', ['object'=>'GW_Order_Item','relation_field'=>'group_id','readonly'=>1]],
		'user' => ['gw_composite_linked', ['object'=>'GW_Customer','relation_field'=>'user_id','readonly'=>1]],
		'seller' => ['gw_composite_linked', ['object'=>'GW_Pay_Sellers','relation_field'=>'seller_id','readonly'=>1]],
		'banktransfer_confirm'=>['gw_image', ['dimensions_resize' => '1024x1024', 'dimensions_min' => '100x100']],
		'discountcode' => ['gw_composite_linked', ['object'=>'Shop_DiscountCode','relation_field'=>'discount_id','readonly'=>1]]
	];		
	
	public $encode_fields = [
	    'extra'=>'jsono',
	];	
	
	public $calculate_fields = [
		'title'=>1,
		'keyval'=>1,
		'recipient'=>1,
		'pay_subtype_human'=>1,
		'delivery_country'=>1,
		'payd'=>1,
	];
	
	
	public $ownerkey = 'payments/ordergroups';
	public $extensions = [
	    'changetrack'=>1,
	    'keyval'=>1
	];				
	public $keyval_use_generic_table = 1;	
	
		
	function updateTotal()
	{
		$amount = 0;
		$deliverable = 0;
		$downloadable = 0;
		$discount_total = 0;
		
		if($relobj = $this->getCompositeObject('items'))
			$relobj->cleanCache();
		
	
		
		foreach($this->items as $item){
			$amount+= ($item->unit_price-$item->discount)*$item->qty;
			$deliverable = max($item->deliverable, $deliverable);
			$downloadable = max($item->downloadable, $downloadable);
			$discount_total+=$item->discount*$item->qty;
		}
		
		
		$this->deliverable = $deliverable;
		
		if(GW::s('ECOMMERCE_DOWNLOADABLE'))
			$this->downloadable = $downloadable;
		
		$this->amount_items = $amount;
		//$this->amount_total = $this->amount_items + $this->amount_shipping;
		$this->amount_total = $this->amount_shipping + $this->amount_items - $this->amount_coupon;
		$this->amount_discount = $discount_total;

		$this->itmcnt = count($this->items);
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
			
			case 'AFTER_LOAD':

				if($this->pay_type)
					$this->composite_map['pay_confirm'] = ['gw_composite_linked', ['object'=>'','relation_field'=>'pay_confirm_id','readonly'=>1]];
				
				if($this->pay_type=='paysera')
					$this->composite_map['pay_confirm'][1]['object'] = 'GW_Paysera_Log';
				
				if($this->pay_type=='montonio')
					$this->composite_map['pay_confirm'][1]['object'] = 'GW_PayUniversal_Log';
				
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
	
	function getClassItems($class)
	{
		$ids = [];
		
		if(!$this->items)
			return [];
		
		
		foreach($this->items as $citem)
			if($citem->obj_type == $class){
				$ids[$citem->obj_id] = $citem->obj_id;
			}
			
		$list = $class::singleton()->findAll(GW_DB::inCondition('id', $ids), ['key_field'=>'id']);
			
		return $list;
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
				if($this->id)
					return "#".$this->id." ".($this->payd? 'PAYD':"NOPAY").' '.$this->amount_total.' EUR';
			break;

			case 'recipient':
				return $this->user->title.' <'.$this->user->email.'>';
			break;
			case 'pay_subtype_human':
				$pm = GW_Pay_Methods::singleton()->find(['gateway=? AND `key`=?', $this->pay_type, $this->pay_subtype]);
				return $pm ? $pm->title : $this->pay_subtype;
			break;
			case 'delivery_country':
				return GW_Country::singleton()->find(['code=?', $this->country]);
			break;
			case 'payd':
				return $this->payment_status == 7;
			break;
		}
		
			
		
		
		return parent::calculateField($key);
	}
	
	

	
	function setCoupon($coupon = false, $markAsUsed=false)
	{
		if(!$coupon)
			$coupon = $this->discountcode;
		

		
		
		$this->discount_id = $coupon->id;
		$this->amount_coupon = (float)$coupon->limit_amount - (float)$coupon->used_amount;
		
		
		
		
		
		if($this->amount_coupon < 0){
			$this->amount_coupon = 0;
			//reiktu pranest adminui, tokia situacija neturetu nutikt
		}
		
		
		
		if(!$this->amount_coupon)
			return false;
		
		
		$amount_total = (float)$this->amount_shipping + (float)$this->amount_items;
		
		//panaudot ne daugiau negu krepselio suma
		if($amount_total < $this->amount_coupon)
			$this->amount_coupon = $amount_total;
		
		
		
		
		$this->updateTotal();

		
		if($markAsUsed)
		{
			$coupon->fireEvent('BEFORE_CHANGES');
			$coupon->used_amount = (float)$coupon->used_amount + (float)$this->amount_coupon;
			$coupon->last_use_order_id = $this->id;
			$coupon->updateChanged();
		}
	}
	
			
}