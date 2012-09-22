<?php

class Module_Did_Users extends GW_Public_Module
{
	
	function init()
	{
		
	}
	
	function viewDefault()
	{
		//GW::$request->setErrors(Array('feil her'));
		//GW::$request->setMessage('SUCCESS', 0);
		//GW::$request->setMessage('WARNING', 1);
		//GW::$request->setMessage('ERROR', 2);
		//GW::$request->setMessage('INFO', 3);
		$this->smarty->assign('user', GW::$user);
		
		include GW::$dir['MODULES'].'dropindesign/did_order.class.php';
		$order_0 = new Did_Order();
		$list = $order_0->findAll(Array('user_id = ?', GW::$user->id));
		
		$order_info['total'] = count($list);
		$order_info['ordered'] = 0;
		$order_info['payed'] = 0;
		$order_info['processed'] = 0;
		$order_info['sent'] = 0;
		$order_info['total_varer'] = 0;
		$order_info['total_designs'] = 0;
		
		/*
	<i id="ORDER_STATES_OPT">
		<i id="5">Canceled</i>
		<i id="10">Ordered</i>		
		<i id="20">Payed</i>
		<i id="25">Processing</i>
		<i id="30">Processed</i>
		<i id="40">Sent</i>
	</i>
	*/	
		
		foreach ($list as $v)
		{
			if($v->status == 10){
				$order_info['ordered'] += 1;
			}elseif($v->status == 20){
				$order_info['payed'] +=1;
			}elseif($v->status == 30){
				$order_info['processed'] += 1;
			}elseif($v->status == 40){
				$order_info['sent'] += 1;
			}
			
		}
		$this->smarty->assign('order_info', $order_info);
	}
	
	function viewOrders()
	{
		include GW::$dir['MODULES'].'dropindesign/did_order.class.php';
		$data = new Did_Order();
		$list = $data->findAll(Array('user_id = ?', GW::$user->id));
		$this->smarty->assign('ordersList', $list);
	}
	
	function viewInnstillinger()
	{
		$this->smarty->assign('user', GW::$user);
	}
	
	function viewPassord()
	{
		
	}
	
	function viewLogin()
	{
		
	}
	
	function doSave()
	{
		
		$tempVals = $_REQUEST['item'];
		$vals['first_name'] = $tempVals['first_name'];
		$vals['second_name'] = $tempVals['second_name'];
		$vals['phone'] = $tempVals['phone'];
		$vals['mob_phone'] = $tempVals['mob_phone'];
		$vals['address'] = $tempVals['address'];
		$vals['post_index'] = $tempVals['post_index'];
		$vals['city'] = $tempVals['city'];
		$vals['news'] = $tempVals['news'];
		$vals['id'] = GW::$user->id;
		$user = new GW_User($vals);
		if (!$user){
			//GW::$request->setMessage('Error');
			return;
		}
		
		$user->setValidators('update_info');
		
		//$this->canBeAccessed($item, true);
		$user->setValues($vals);
		if(!$user->validate())
		{
			GW::$request->setErrors($user->errors);
			return;
		}
		
		$user->setValidators(false); //remove validators
		$user->update(Array('first_name', 'second_name', 'phone', 'mob_phone', 'address', 'post_index', 'city', 'news'));
		GW::$request->setMessage($this->lang['information_updated'], 0);
		//update current user with what we just saved in database
		GW::$user->setValues($vals);
                GW::$request->jump();
		//dump($this->lang['information_updated']);
		//exit;
		//$this->sendMail($item);
	}
	
	function doChangePass()
	{
		if(!isset($_REQUEST['pass']) || !isset($_REQUEST['pass2']) || !isset($_REQUEST['old_pass'])){
			return;
		}
		if ($_REQUEST['pass'] == '' || $_REQUEST['pass2'] == '' || $_REQUEST['old_pass'] == '' ){
			GW::$request->setErrors(Array('fill_out_all_fields'));
			return;
		}
		if($_REQUEST['pass'] != $_REQUEST['pass2']){
			GW::$request->setErrors(Array('pass_does_not_match'));
			return;
		}
		if(!GW::$user->checkPass($_REQUEST['old_pass']))
		{
			GW::$request->setErrors(Array('old_pass_incorrect'));
			return;
		}
		GW::$user->setValidators('update_pass');
		GW::$user->pass = $_REQUEST['pass'];
		GW::$user->cryptPassword();
		GW::$user->update(Array('pass'));
		GW::$request->setMessage("success", 0);
	}
	
	function process()
	{
		$this->init();
		
		if (!GW::$user){
			$view_name = self::__funcVN('login');
		}
		else{
			$act_name = self::__funcVN($_REQUEST['act']);
			if (isset(GW::$request->path_arr[1]) ){
				$view_name = self::__funcVN(GW::$request->path_arr[1]['name']);
			}
			if(!method_exists($this, 'view'.$view_name)){
				$view_name = self::__funcVN('default'); //perspektyvoj kad padaryti kitus viewsus
			}
			
			
			if($act_name)
				$this->processAction($act_name);
		}

		$this->processView($view_name);
	}
}