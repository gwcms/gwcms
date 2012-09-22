<?php
class DID_User_Product extends GW_Data_Object{
	
	var $table = 'did_user_products';
	var $default_order = 'insert_time ASC';
	var $product;
	var $image;
	//if 1 then it will be displayed in the shopping cart item list
	//if 0 then it means that the product this item is referencing does not exist, and item should not be displayed or calculated.
	var $display;
	
	function findAllTable($params)
	{
			return isset($params['tables']) ? $params['tables'] : $this->table;
	}
	
	/**
	 * 
	 * No validation checks are performed, so please valdiate all the needed data
	 * such as ((int)$_REQUEST['product_id'])
	 * and $cartId, both must exists and both must be numbers.
	 * @param int $cartId
	 */
	function addItem($cartId)
	{
		$values = Array();
		//product_id
		$values['product_id'] = ((int)$_REQUEST['product_id']);
		//set status to saved
		$values['status'] = 'saved';
		//design (validate xml?)
		$values['design'] = $_REQUEST['design'];
		$values['quantity'] = ((int)$_REQUEST['quantity']);
		//$cartId
		$values['status_id'] = $cartId;
		$values['id'] = (int)$_REQUEST['id'];
		$this->setValues($values);
		$this->save();
	}
	
	function getFlashXMLData($cartId){
		if(!$cartId)
		{
			GW::$request->setErrors(Array('/GENERAL/ACTION_RESTRICTED'));
			return;
		}

		if(isset($_REQUEST['product_id']))
		{
			$prod_id = ((int)$_REQUEST['product_id']);
			$options = Array('1'=>
			$this->table . '.id = ' . $prod_id . " AND " .
			$this->table . ".status = 'saved' AND " .
			$this->table . ".status_id = " . $cartId,);
			$items = $this->findAll($options);
			//dump($items);
			if(isset($items[0])){
				echo $items[0]->design;
				exit;
			}
			echo "Error";
			exit;
		}
		else
		{
			GW::$request->setErrors(Array('/GENERAL/BAD_ARGUMENTS'));
			return;
		}
	}
	
	function removeItem($cartId){
		$itemId = (int)$_REQUEST['item_id'];
		if($this->isOwner($cartId, $itemId)){
			$this->set('id', $itemId);
			$this->delete();
			return true;
		}
		else{
			return false;
		}
	}
	
	function isOwner($cartId, $itemId)
	{
		$conditions = Array('1'=>$this->table . '.status_id = ' . $cartId . 
			' AND ' . $this->table . '.status = \'saved\'' .
			' AND ' . $this->table . '.id = ' . $itemId);
		$cartItemList = $this->findAll($conditions);
		return isset($cartItemList[0]);
	}
	function getById($cartId, $itemId)
	{
		$conditions = Array('1'=>$this->table . '.status_id = ' . $cartId . 
			' AND ' . $this->table . '.status = \'saved\'' .
			' AND ' . $this->table . '.id = ' . $itemId);
		$cartItemList = $this->findAll($conditions);
		return $cartItemList[0];
	}
}