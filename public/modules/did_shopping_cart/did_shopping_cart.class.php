<?php
class DID_Shopping_Cart extends GW_Data_Object{
	
	var $table = 'did_shopping_carts';
	
	function findAllTable($params)
	{
			return isset($params['tables']) ? $params['tables'] : $this->table;
	}
	
	function getLastInsertTime($cartItemList)
	{
		$lastInsert = 0;
		foreach ($cartItemList as $item)
		{
			if ($cartItemList->insert_time > $lastInsert)
			{
				$lastInsert = $cartItemList->insert_time;
			}
		}
		return $lastInsert;
	}
	
	function getMiniCartInfo()
	{
		$shopping_cart = $this->getShoppingCart();
		if(isset($shopping_cart)){
			include_once GW::$dir['PUB_MODULES'].'did_shopping_cart/did_user_product.class.php';
			$data = new DID_User_Product();
			$conditions = Array('1'=>$data->table . '.status_id = ' . $shopping_cart->id . 
			' AND ' . $data->table . '.status = \'saved\'');
			$cartItemList = $data->findAll($conditions);	
		}		
		
		$this->fillInProductInfo($cartItemList);
		$sum = 0;
		$nrProducts = 0;
		$nrItems = 0;
		if(isset($cartItemList[0]))
		{
			foreach ($cartItemList as $cartItem){
				if($cartItem->display == '1'){
					$nrProducts++;
					$nrItems += $cartItem->quantity;
					$sum += $cartItem->quantity * ($cartItem->product->price + $cartItem->product->mod_price - $cartItem->product->red_price);
				}
			}
		}
		
		//if ($nrProducts != 0 && $sum < 100){
		//	$sum = 100;
		//}
		return Array('sum'=>$sum, 'nr_products'=>$nrProducts, 'nr_items'=>$nrItems);
	}
	
	/**
	 * 
	 * @return DID_Shopping_Cart object, if one exists for this user.
	 */
	function getShoppingCart()
	{
		//dump(GW::$customer->id);
		//dump($_COOKIE);
		if(GW::$user)//check if user is logged in
		{
			//get shoppingcart
			$conditions = Array('1'=>$this->table.'.user_id = ' . (int)GW::$user->id);
			$cart = $this->findAll($conditions);
		}
		/*
		if(!isset($cart[0])){ //if user did not had a shopping cart
			//try to find it with key in the cookie
			if(isset($_COOKIE['shopping_cart'])){
				$conditions = Array('1'=>$this->model->table.'.key = ' . mysql_real_escape_string($_COOKIE['shopping_cart']));
				$cart = $this->model->findAll($conditions);
			}
		}
		else{
			//set cookie to represent the cart.
			$_COOKIE['shopping_cart'] = $cart[0]->key;
		}
		*/
		return $cart[0];
	}
	
	function fillInProductInfo($cartItemList)
	{
		if ($cartItemList == null)
			return;
		
		include_once GW::$dir['MODULES'].'dropindesign/did_product.class.php';
		$did_prod = new DID_Product();
		foreach ($cartItemList as $cartItem){
			$products = $did_prod->getById($cartItem->product_id);
			if (isset($products[0])){
				$cartItem->display = "1";
				$info = $products[0]->getInfo();
				$cartItem->product = $info;
				$cartItem->image = $info->image;
			}
			else{
				$cartItem->display = "0";
			}
		}
	}
	
	function saveCart()
	{
		if(GW::$user){
			$vals = Array();
			$vals['user_id'] = GW::$user->id;
			$this->setValues($vals);
			return $this->insert();
		}
	}
}
