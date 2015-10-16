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
			
			$this->smarty->assign('item', $item);
		}	
				
		
		
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
		
		$link = GW::s('SITE_URL')."admin/lt/customers/users/".$user->id."/form";
		
		$mail=Array(
			'to'=>$admin_eml,
			'subject'=>"Sukurtas naujas vartotojas",
			'body'=>$user_short."Redagavimo forma admin sistemoje: ".$link,
			'from'=>GW::s('SYS_MAIL')
		);
		
		GW_Mail::simple($mail);		
	}
	
	function doRegister()
	{
		$vals = $_POST['item'];

		$item = $this->model->createNewObject($vals);
		
		
		$item->setValidators('register');
		GW_Customer::singleton();
			
		$item->username = $item->email;
		
		if($item->validate())
		{		
			$item->active=1;
			$item->insert();
			$this->notifyAdminNewUser($item);
						
			$this->app->jump(false, Array('success'=>1));
			
		}else{
			$this->setErrors($item->errors);
					
			$_SESSION['error_item']=$vals;		
		}
		
		$this->app->jump();
		
	}
	
	function viewAccountBalance()
	{
		if(!$this->app->user)
			return false;
		
		$this->smarty->assign('funds', $this->app->user->funds);
	}
	
	
	function doLogin()
	{
		$login = $_POST['login'];
		
		list($user, $pass) = $login;
				
		if($this->app->auth->loginPass($user, $pass)) {
			//GW::$app->setMessage("/VALIDATION/USER/LOGIN_WELCOME");
			$this->app->jump(GW::s('USERZONE_PATH'));
		} else {
			$this->app->setErrors("/VALIDATION/USER/LOGIN_FAIL");
		}
		
		$this->app->jump();
	}
	
	function viewLogout()
	{		
		$this->app->setMessage("/VALIDATION/USER/LOGOUT_MESSAGE");
		$this->app->user->onLogout();
		$this->app->auth->logout();
		$this->app->jump();	
	}
	
	function doPassChange()
	{
		
		$item = $this->model->find(Array('email=? AND removed=0 AND active=1', $_POST['email']));
		
		if(!$item){
			return $this->app->setMessage("/VALIDATION/USER/NO_USER_BY_EMAIL");
		}
		
		if($item->passchange){
			return $this->app->setMessage("/VALIDATION/USER/PASSCHANGE_ALLREADY_SENT");
		}		
		
		$secret = $item->setPassChangeSecret();
		
		$passchange_link = GW::s('SITE_URL').$this->app->ln.'/'.sprintf(GW::s('SITE/USER_PASS_CHANGE_PAGE'),$item->id, $secret);
		

		$mail=Array(
			'to'=>$_POST['email'],
			'subject'=>"Slaptažodžio keitimas",
			'body'=>sprintf($this->app->lang['USER_PASSCHANGE_EML'], $passchange_link),
			'from'=>GW::s('SYS_MAIL')
			);
		
		GW_Mail::simple($mail);		
				
		
		$this->app->setMessage("/VALIDATION/USER/PASS_CHANGE_LINK_SENT");
		
		$this->app->jump();
	}
	
	function viewPassChange()
	{		
		$id = $_GET['id1'];
		$key = $_GET['id2'];
		
		$item = $this->model->find(Array('id=? AND passchange=? AND removed=0 AND active=1', $id, $key));
		
		
		if(!$item){
			$this->app->setErrors("/VALIDATION/USER/INVALID_PASSCHANGE_LINK");
			
			$this->app->jump(dirname($this->app->path));
		}
		
		
		
	}
	
	function doPassChange2()
	{
		$id = $_GET['id1'];
		$key = $_GET['id2'];
		
		$item = $this->model->find(Array('id=? AND passchange=? AND removed=0 AND active=1', $id, $key));
		
		
		
		

		
		$item->set('pass_new',$_POST['login_id'][0]);
		$item->set('pass_new_repeat',$_POST['login_id'][1]);
		$item->setValidators('change_pass_repeat');
		
		
		
		
		if(!$item->validate())
		{
			$this->app->setErrors($item->errors);
			Navigator::jump();
		}
		
		$item->update();
		
		$this->app->setMessage('/USER/PASS_CHANGED');
		
		$this->app->jump();
	}
	

	
}
