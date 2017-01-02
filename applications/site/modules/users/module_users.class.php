<?php

class Module_Users extends GW_Public_Module
{

	function init()
	{
		$this->model = new GW_Customer;
		
		//tol kol dirbu su siuo moduliu - reikia kad lang failus importuotu i db
		GW::$devel_debug = true;
	}

	
	function viewDefault()
	{
		if(!$this->app->user)
			$this->app->jump(GW::s('SITE/PATH_LOGIN'));
	}
	

	function viewLogin($params)
	{
		$this->tpl_vars['login']=(object)[
		    'username'=>isset($_COOKIE['login_0']) ? $_COOKIE['login_0'] : false,
		    'auto'=>isset($_GET['auto']) ? $_GET['auto'] : false,
		];
	}
	
	function viewRegister()
	{		
		if(isset($_SESSION['error_item'])) {
			$item = (object)$this->getErrorItem('reguser');
			
			$this->smarty->assign('item', $item);
		}	
	}
	
	
	function notifyAdminNewUser($user)
	{
		$admin_eml = GW_User::findStatic('username="admin"')->email;
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
		$item->prepareSave();
		
		if($item->validate())
		{		
			$item->active=0;
			$item->insert();
			$this->notifyAdminNewUser($item);
			$this->sendVerificationEmail($item);
						
			$this->app->jump(GW::s('SITE/PATH_REGISTER'), Array('success'=>1));
			
		}else{
			$this->setItemErrors($item);
			$this->setErrorItem($vals,'reguser');
							
		}
		
		$this->app->jump();
		
	}
	
	
	
	function doLogin()
	{
		$login = $_POST['login'];
		
		list($username, $pass) = $login;
		setcookie('login_0', $username, strtotime('+3 MONTH'), $this->app->sys_base);
						
		if($user = $this->app->auth->loginPass($username, $pass)) {
			
			$this->app->user = $user;
			
			if(isset($_REQUEST['login_auto']) && GW_Auth::isAutologinEnabled())
			{
				setcookie('login_7', $this->app->user->getAutologinPass(), strtotime(GW::s('GW_AUTOLOGIN_EXPIRATION')), $this->app->sys_base);
				$this->app->auth->session['autologin']=1;
			}
			
			
			//GW::$app->setMessage("/m/USERS/LOGIN_WELCOME");
			$this->app->jump(isset($_GET['returnto_url']) ? $_GET['returnto_url'] : GW::s('SITE/PATH_USERZONE'));
			
			
		} else {
			$this->setError("/M/USERS/LOGIN_FAIL");
		}
		
		
		$this->app->carry_params['returnto_url']=1;
		
		$this->app->jump(false,[
		    'error'=>1, 
		    'auto'=> isset($_REQUEST['login_0']) ? $_REQUEST['login_0'] : false
		]);
	}
	
	function viewLogout()
	{		
		$this->app->setMessage("/M/USERS/LOGOUT_MESSAGE");
		
		if($this->app->user){
			$this->app->user->onLogout();
			$this->app->auth->logout();
		}
		
		$this->app->jump('a');	
	}
	
	function doPassChange()
	{
		
		$item = $this->model->find(Array('username=? AND removed=0 AND active=1', $_POST['email']));
		
		if(!$item){
			return $this->setError("/M/USERS/NO_USER_BY_EMAIL");
		}
		
		if($item->site_passchange && !$item->passChangeExpired()){
			return $this->setMessage("/M/USERS/PASSCHANGE_ALLREADY_SENT");
		}
		
		$secret = $item->setPassChangeSecret();
		
		
		$passchange_link = $this->app->buildURI(GW::s('SITE/PATH_PASSCHANGE'),['id1'=>$item->id,'id2'=>$secret],['absolute'=>1]);
			

		$mail=[
			'to'=>$item->email,
			'subject'=>GW::ln('/M/USERS/USER_PASSCHANGE_SUBJECT'),
			'body'=>sprintf(GW::ln('/M/USERS/USER_PASSCHANGE_EML'), $passchange_link),
			'from'=>GW::s('SYS_MAIL')
		];
		
		GW_Mail::simple($mail);		
				
		
		$this->app->setMessage(sprintf(GW::ln("/M/USERS/PASS_CHANGE_LINK_SENT"), $item->email));
		
		
	
		$this->app->jump(GW::s('SITE/PATH_LOGIN'));
	}
	
	function sendVerificationEmail($item)
	{
		
		$path = $this->app->path;
		
		
		$secret = $item->setSignUpApprovalSecret();
		$verif_link = $this->app->buildURI(GW::s('SITE/PATH_LOGIN'),['act'=>'doVerifyAccount','id1'=>$item->id,'id2'=>$secret],['absolute'=>1]);
		

		$mail=[
			'to'=>$item->email,
			'subject'=>GW::ln('/M/USERS/USER_VERIFY_SUBJECT'),
			'body'=>sprintf(GW::ln('/M/USERS/USER_VERIFY_EML'), $verif_link),
			'from'=>GW::s('SYS_MAIL')
		];
		
		
		GW_Mail::simple($mail);	
	}
	
	function doVerifyAccount()
	{
		$id = $_GET['id1'];
		$key = $_GET['id2'];
		
		$item = $this->model->find(Array('id=? AND site_verif_key=? AND removed=0 AND active=0', $id, $key));
				
		if($item)
		{
			$this->app->setMessage("/M/USERS/VERIFY_SUCCESS");
			
			$item->active=1;
			$item->site_verif_key = '';
			$item->updateChanged();
				
		}else{
			$this->app->setError("/M/USERS/INVALID_VERIFY_LINK");
		}
		
		$this->app->jump(GW::s('SITE/PATH_LOGIN'));
	}
	
	
	function doEmailVerification()
	{
		d::dumpas([$_GET['userid'],$_GET['verification']]);;
	}	
	
	function viewPassChange()
	{				
		if(isset($_GET['id1']) && isset($_GET['id2'])){
			$id = $_GET['id1'];
			$key = $_GET['id2'];

			$item = $this->model->find(Array('id=? AND site_passchange=? AND removed=0 AND active=1', $id, $key));


			if(!$item){
				$this->setError("/M/USERS/INVALID_PASSCHANGE_LINK");

				$this->app->jump(GW::s('SITE/PATH_PASSCHANGE'));
			}
			
			$this->tpl_name = 'passchange';
		
		}else{
			$this->tpl_name = 'passreset';
		}
		
		
		
	}
	
	function doPassChange2()
	{
		$id = $_GET['id1'];
		$key = $_GET['id2'];
		
		$item = $this->model->find(Array('id=? AND site_passchange=? AND removed=0 AND active=1', $id, $key));
		
		$item->set('pass_new',$_POST['login_id'][0]);
		$item->set('pass_new_repeat',$_POST['login_id'][1]);
		$item->setValidators('change_pass_repeat');
	
		
		if(!$item->validate())
		{
			$this->setItemErrors($item);
		}else{
			$item->site_passchange='';
			$item->update();
			
			$this->app->setMessage('/M/USERS/PASSRESET_PASS_CHANGED');
			$this->app->jump(GW::s('SITE/PATH_LOGIN'));			
		}
	}
	

	
}
