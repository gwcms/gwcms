<?php

include GW::$dir['PUB_LIB'].'gw_public_module.class.php';

class Module_Did_Orders extends GW_Public_Module
{
	var $order_items;
	var $filters;
	
	function init()
	{
		include GW::$dir['PUB_MODULES'].'did_shopping_cart/did_user_product.class.php';
		include GW::$dir['MODULES'].'dropindesign/did_order.class.php';
		
		$this->model = new DID_Order();
		$this->order_items = new DID_User_Product();
		//dump('init check');
	}
	
	function viewDefault()
	{
		//backtrace();
		//dump('viewDefault');
		
		if(!GW::$user){
			GW::$request->jump("handlekurv");
		}
		
		$this->smarty->assign('user', GW::$user);
		
		//get all cart items
		include_once GW::$dir['PUB_MODULES'].'did_shopping_cart/did_shopping_cart.class.php';
		$data = new DID_Shopping_Cart();
		$shopping_cart = $data->getShoppingCart();
		if(isset($shopping_cart)){
			$conditions = Array('1'=>$this->order_items->table . '.status_id = ' . $shopping_cart->id . 
			' AND ' . $this->order_items->table . '.status = \'saved\'');
			$cartItemList = $this->order_items->findAll($conditions);	
		}
		$data->fillInProductInfo($cartItemList);
		$ammount = 0;
		foreach ($cartItemList as $cartItem){
			if ($cartItem->display){
				$ammount++;
			}
		}
		if ($ammount <= 0){
			GW::$request->jump("handlekurv");
		}
		//$list = $this->model->findAll();
		$this->smarty->assign('cartItemList', $cartItemList);
		//GW::$request->setErrors(Array('Feil Melding, oi oi oi...'));
	}
	
	function doSave()
	{
		if(!GW::$user){
			GW::$request->jump("handlekurv");
		}
		
		if($_REQUEST['pay_method'] != 'free' && $_REQUEST['pay_method'] != 'none')
		{
			//dump($_REQUEST);
			return;
		}
		$tempVals = $_REQUEST['item'];
		$vals['delivery_first_name'] = $tempVals['delivery_first_name'];
		$vals['delivery_second_name'] = $tempVals['delivery_second_name'];
		$vals['delivery_address'] = $tempVals['delivery_address'];
		$vals['delivery_post_index'] = $tempVals['delivery_post_index'];
		$vals['delivery_city'] = $tempVals['delivery_city'];
		//$vals+=$this->filters;
		
		if($vals['delivery_first_name'] == '' || $vals['delivery_second_name'] == '' || $vals['delivery_address'] == '' ||
		$vals['delivery_post_index'] == '' || $vals['delivery_city'] == ''){
			GW::$request->setMessage($this->lang['missing_delivery_fields']);
		}
		
		include_once GW::$dir['PUB_MODULES'].'did_shopping_cart/did_shopping_cart.class.php';
		$data = new DID_Shopping_Cart();
		$shopping_cart = $data->getShoppingCart();
		if(isset($shopping_cart)){
			$conditions = Array('1'=>$this->order_items->table . '.status_id = ' . $shopping_cart->id . 
			' AND ' . $this->order_items->table . '.status = \'saved\'');
			$cartItemList = $this->order_items->findAll($conditions);	
		}
		$data->fillInProductInfo($cartItemList);
		$sum = 0;
		$number = 0;
		foreach ($cartItemList as $cartItem){
			if ($cartItem->display){
				$sum += $cartItem->quantity * ($cartItem->product->price+$cartItem->product->mod_price-$cartItem->product->red_price);
				$number ++;
			}
		}
		if($number == 0) //nothing to order, send user back to handlekurv
			GW::$request->jump("handlekurv");
		//TODO: Calculate tickets prize
		if ($sum < 100)
			$sum = 100;
		$vals['user_id'] = GW::$user->id;
		$vals['status'] = "ordered";
		$vals['total_cost'] = $sum;
		
		
		
		if($_REQUEST['pay_method'] == 'free'){
			$vals['payed'] = $sum;
			$vals['pay_time'] = date('Y-m-d H:i:s');
			$vals['status'] = "payed";
		}
		
		$item = $this->model->createNewObject($vals);
		
		if(!$item->validate())
		{
			$this->setErrors($item->errors);
			
			$this->processView('default');
			exit;
		}
		
		
		$item->fireEvent('BEFORE_SAVE', $item);
		$item->save();
		
		//for each item in shopping cart list change it to ordered and set its propper status_id
		foreach ($cartItemList as $cartItem){
			if ($cartItem->display){
				$cartItem->status_id = $item->id;
				$cartItem->status = "ordered";
				$cartItem->update(Array('status_id', 'status'));
			}
		}
		//jeigu saugome tai reiskia kad validacija praejo
		GW::$request->setMessage($this->lang['order_accepted'], 0);		
		//jump to paypal and submit all the info needed.
		//$this->jumpAfterSave($item);
		//$this->jumpAfterSave(GW::$request->ln . "/" . $this->lang.);
		if($_REQUEST['pay_method'] != 'free')
		{
		}
		else
		{	
		}
		GW::$request->jump("bruker/orders");
			
		exit;
	}
}