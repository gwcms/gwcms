<?php

include GW::$dir['PUB_LIB'].'gw_public_module.class.php';

class Module_Customers extends GW_Public_Module
{

	function init()
	{
		//include GW::$dir['MODULES'].'/customers/gw_user.class.php';
		
		$this->model = new GW_User();
		//dump('init check');
	}
	
	
	function viewDefault()
	{
		//backtrace();
		//dump('viewDefault');

		if ($_REQUEST['send']){
			
			$headers = "From: noreply@dropindesign.no\r\n";
			$subject = "Test Subject";
			$message = "Ahoy biatch, ur key is lost 4 eva \r\n";
			$sent = mail("norwayv@gmail.com", "My Subject", $message);
			dump("SENT:" . $sent);
			exit;
		}
		
		if($_REQUEST['success'] == 1){
			$user->email = "lol@test.com";
			$user->id = 1;
			$this->smarty->assign('user', $user);
			GW::$request->setMessage($this->lang['account_created'], 0);
		}
		
	}
	
	function viewActivate()
	{
		$user_id = (int)GW::$request->path_arr[1]['name'];
		$key = $_REQUEST['key'];
		//dump($key);
		//dump($user_id);
		if($user_id != 0){
			$user = $this->model->getForActivationById($user_id);
		}
		
		
		//dump($user);
		if(isset($user) && $key == $user->key){
			if ($user->active == 1){
				$this->smarty->assign('answer', 0);
				return;
			}
			$user->active = 1;
			//$user->generateKey();
			$user->update();
			$this->smarty->assign('answer', 1);
			return;
		}
		$this->smarty->assign('answer', -1);
	}
	

	function doRegister()
	{
		
		//not safe, this way badppl can pass values to save to database by adding inputs with name item[blabla]
		//$vals = $_REQUEST['item'];
		//instead get each value one by one
		$tempVals = $_REQUEST['item'];
		
		$vals['first_name'] = $tempVals['first_name'];
		$vals['second_name'] = $tempVals['second_name'];
		$vals['email'] = $tempVals['email'];
		$vals['phone'] = $tempVals['phone'];
		$vals['pass'] = $tempVals['pass'];
		$vals['address'] = $tempVals['address'];
		$vals['post_index'] = $tempVals['post_index'];
		$vals['city'] = $tempVals['city'];
		$vals['news'] = $tempVals['news'];
		$vals['mob_phone'] = $tempVals['mob_phone'];
		
		$item = $this->model->createNewObject();
		
		$item->setValidators('insert');
		
		//$this->canBeAccessed($item, true);
		$item->setValues($vals);
		if(!$item->validate())
		{
			//dump($item->errors);
			GW::$request->setErrors($item->errors);
			$this->processView('default');
			exit;
		}
		
		$item->setValidators(false); //remove validators
		$item->generateKey();
		$item->cryptPassword();
		$item->save();
		$this->smarty->assign('success', 1);
		GW::$request->setMessage($this->lang['account_created'], 0);
		$this->sendMail($item);
		$this->smarty->assign('user', $item);
		
	}
	
	function sendMail($item)
	{
		$subject = $this->lang.ACTIVATION_EMAIL.subject;
		$body = "";
		$body .= $this->lang.ACTIVATION_EMAIL.top_body;
		$body .= "LINK COMES HERE";
		$body .= $this->lang.ACTIVATION_EMAIL.bot_body;
		$headers = "From: noreply@dropindesign.no <noreply@dropindesign.no>\r\n";
		
		$subject = "Activation for dropindesign.no account";
		$body = "Hello, " . $item->first_name . " " . $item->second_name . ",\r\nthank you for registering at www.dropindesign.no\r\n\r\n" . 
		"This is activation email for your account, if you have not requested this, please ignore this email.\r\n\r\n" .
		"Your activation code is: " . $item->key . "\r\n\r\n" .
		"To activate your account you can follow this link:\r\n\r\n" . "http://www.dalisra.com/no/registrer/" .
		$item->id . "/activate?key=" . $item->key . "\r\n\r\n" . 
		"Please do not reply to this email, if you have any questions contact us at support@dropindesign.com\r\n\r\n" . 
		"Best Regards,\r\nDropindesign.no";
		if(mail($item->email . " <" . $item->email . ">", $subject, $body, $headers)){
			return true;
		}
		return false;
	}
	
	function doLogin()
	{
	}

	
}