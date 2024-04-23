<?php


class GW_Anonymous_User extends GW_Data_Object
{
	
	public $ownerkey = 'datasources/anonymoususers';
	
	public $keyval_use_generic_table = 1;
	public $extensions = [
	    'keyval'=>1, 
	    'changetrack'=>1
	];
	
	function getCart($create=false)
	{
		$cartid = $this->get('keyval/cart_id');
		if($cartid)
			$cart = GW_Order_Group::singleton()->find(['id=? AND payment_status!=7 AND open=1', $cartid]);
	
		
		
		if($create && (!isset($cart) || !$cart)){
			$cart = GW_Order_Group::singleton()->createNewObject(['auser_id'=>$this->id]);
			$cart->active = 1; 
			$cart->open = 1;
			$cart->payment_status = 0;
			$cart->setSecret();
			$cart->insert();
			
			$this->set('keyval/cart_id', $cart->id);
		}
		
		return $cart ?? false;
	}	
	
	
}