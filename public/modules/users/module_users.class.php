<?php

class Module_Users extends GW_Public_Module
{

	function init()
	{
		
		$this->model = new GW_User;
		
	}


	function viewDefault($params)
	{
		//dump($this->lang);
		//exit;		
		
	}
	
	function viewRegister()
	{		
		if(isset($_SESSION['error_item'])) {
			$item = (object)$_SESSION['error_item'];
			unset($_SESSION['error_item']);
		}	
				
		$this->smarty->assign('item', $item);
	}
	
	
	function notifyAdminNewUser($user)
	{
		$admin_eml = GW_ADM_User::findStatic('username="admin"')->email;
		//$admin_eml = "laiskonoriu@gmail.com";
		
		$user_short = 
			"Company: ".$user->company_name."\n".
			"Name: ".$user->name."\n".
			"Email: ".$user->email."\n".
			"Phone: ".$user->phone."\n\n";
		
		$link = GW::$static_conf['SITE_URL']."admin/lt/customers/users/".$user->id."/form";
		
		$mail=Array(
			'to'=>$admin_eml,
			'subject'=>"Sukurtas naujas vartotojas",
			'body'=>$user_short."Redagavimo forma admin sistemoje: ".$link,
			'from'=>GW::$static_conf['SYS_MAIL']
		);
		
		GW_Mail::simple($mail);		
	}
	
	function doRegister()
	{
		$vals = $_POST['item'];

		$item = $this->model->createNewObject($vals);
		
		
		$item->setValidators('register');
				
		
		if($item->validate())
		{
			$item->insert();
			
			$this->notifyAdminNewUser($item);
						
			GW::$request->jump(false, Array('success'=>1));
			
		}else{
			$this->setErrors($item->errors);
					
			$_SESSION['error_item']=$vals;		
		}
		
		GW::$request->jump();
		
	}
	
	
	function doLogin()
	{
		$login = $_POST['login'];
		
		list($user, $pass) = $login;
		
		

		
		if(GW::$auth->loginPass($user, $pass)) {
			 GW::$request->setMessage("/VALIDATION/USER/LOGIN_WELCOME");
			 GW::$request->jump('userzone');
		} else {
			GW::$request->setErrors("/VALIDATION/USER/LOGIN_FAIL");
		}
		
		GW::$request->jump();
	}
	
	function viewLogout()
	{		
		GW::$request->setMessage("/VALIDATION/USER/LOGOUT_MESSAGE");
		GW::$user->onLogout();
		GW::$auth->logout();
		GW::$request->jump();	
	}
	
	function doPassChange()
	{
		
		$item = $this->model->find(Array('email=? AND removed=0 AND active=1', $_POST['email']));
		
		if(!$item){
			return GW::$request->setMessage("/VALIDATION/USER/NO_USER_BY_EMAIL");
		}
		
		if($item->passchange){
			return GW::$request->setMessage("/VALIDATION/USER/PASSCHANGE_ALLREADY_SENT");
		}		
		
		$secret = $item->setPassChangeSecret();
		
		$passchange_link = GW::$static_conf['SITE_URL'].GW::$request->ln.'/'.sprintf(GW::$static_conf['USER_PASS_CHANGE_PAGE'],$item->id, $secret);
		

		$mail=Array(
			'to'=>$_POST['email'],
			'subject'=>"Slaptažodžio keitimas",
			'body'=>sprintf(GW::$lang['USER_PASSCHANGE_EML'], $passchange_link),
			'from'=>GW::$static_conf['SYS_MAIL']
			);
		
		GW_Mail::simple($mail);		
				
		
		GW::$request->setMessage("/VALIDATION/USER/PASS_CHANGE_LINK_SENT");
		
		GW::$request->jump();
	}
	
	function viewPassChange()
	{		
		$id = $_GET['id1'];
		$key = $_GET['id2'];
		
		$item = $this->model->find(Array('id=? AND passchange=? AND removed=0 AND active=1', $id, $key));
		
		
		if(!$item){
			GW::$request->setErrors("/VALIDATION/USER/INVALID_PASSCHANGE_LINK");
			
			GW::$request->jump(dirname(GW::$request->path));
		}
		
		
		if(!isset($_POST['login']))
			return;
		
		list($pass_new, $pass_new_repeat) = $_POST['login'];
		
		dump($pass_new, $pass_new_repeat);
		exit;
		
		
		
		
	}
	

	
}
