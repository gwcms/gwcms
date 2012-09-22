<?php
	include_once GW::$dir['MODULES'].'/dropindesign/did_category.class.php';
	include_once GW::$dir['MODULES'].'/dropindesign/did_type.class.php';
	
	
	$searchString = Array('limit'=>'8', 'orderby'=>'priority');
	$data = new DID_Category();
	$itemList = $data->findAll(Array('active'=>'active=1'), $searchString);
	
	
	
	include_once GW::$dir['PUB_MODULES'].'did_shopping_cart/did_shopping_cart.class.php';
	$data = new DID_Shopping_Cart();
	$miniCartInfo = $data->getMiniCartInfo();
	$miniCartInfo['last_insert_time'] = $data->getLastInsertTime($miniCartInfo);
	GW::$smarty->assign('miniCartInfo', $miniCartInfo);
	GW::$smarty->assign('list', $itemList);