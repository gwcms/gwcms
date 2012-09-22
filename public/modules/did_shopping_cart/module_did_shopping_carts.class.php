<?php

include GW::$dir['PUB_LIB'].'gw_public_module.class.php';

class Module_Did_Shopping_Carts extends GW_Public_Module
{
	var $shopping_cart_items;
	
	function init()
	{
		include GW::$dir['PUB_MODULES'].'did_shopping_cart/did_user_product.class.php';
		include GW::$dir['PUB_MODULES'].'did_shopping_cart/did_shopping_cart.class.php';
		
		$this->model = new DID_Shopping_Cart();
		$this->shopping_cart_items = new DID_User_Product();
		//dump('init check');
	}
	
	
	function viewDefault()
	{
		//backtrace();
		//dump('viewDefault');
		$this->viewList();
	}
	
	function viewList()
	{
		$options=Array('dump'=>'dump');
		$shopping_cart = $this->model->getShoppingCart();
		if(isset($shopping_cart)){
			$conditions = Array('1'=>$this->shopping_cart_items->table . '.status_id = ' . $shopping_cart->id . 
			' AND ' . $this->shopping_cart_items->table . '.status = \'saved\'');
			$cartItemList = $this->shopping_cart_items->findAll($conditions);	
		}		
		
		$this->model->fillInProductInfo($cartItemList);
		//$list = $this->model->findAll();
		$this->smarty->assign('cartItemList', $cartItemList);
	}
	
	function viewEdit()
	{
		
		$pid = (int)(GW::$request->path_arr[1][name]);
		$cart = $this->model->getShoppingCart();
		if(isset($cart)){
			$item = $this->shopping_cart_items->getById($cart->id, $pid);
			if (isset($item)){
				$this->smarty->assign('item', $item);
				return;
			}
		}
		//otherwis make a jump to handlekurven
		GW::$request->jump('handlekurv');
	}
	
	function doAddItem()
	{
		if(!GW::$user){
			GW::$request->setErrors(Array('/GENERAL/ACTION_RESTRICTED'));
		}
		else{
			//add to shopping cart
			$cart = $this->model->getShoppingCart();
			if (!isset($cart)){
				$cart = $this->model->saveCart();
			}
			
			if($cart->id && isset($_REQUEST['product_id']))
			{
				$this->shopping_cart_items->addItem($cart->id);
				GW::$request->setMessage($this->lang['product_added'], 0);
			}
			else
			{
				GW::$request->setErrors(Array('/GENERAL/ACTION_RESTRICTED'));
			}
		}
		exit;
	}
	
	function doFlash()
	{
		if(!GW::$user){
			GW::$request->setErrors(Array('/GENERAL/ACTION_RESTRICTED'));
		}
		else{
			$cart = $this->model->getShoppingCart();
			if (!isset($cart)){
				GW::$request->setErrors(Array('/GENERAL/ACTION_RESTRICTED'));
			}
			else{
				$this->shopping_cart_items->getFlashXMLData($cart->id);
			}
		}
	}
	
	function doRemoveItem()
	{
		if(!GW::$user){
			GW::$request->setErrors(Array('/GENERAL/ACTION_RESTRICTED'));
		}
		else{
			$cart = $this->model->getShoppingCart();
			if (!isset($cart)){
				GW::$request->setErrors(Array('/GENERAL/ACTION_RESTRICTED'));
			}
			else{
				if($this->shopping_cart_items->removeItem($cart->id))
					GW::$request->setMessage($this->lang['product_deleted'],0);
				else
					GW::$request->setErrors(Array($this->lang['delete_fail_no_product']));
			}
		}
		
	}
	
}