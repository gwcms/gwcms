<?php



class Module_Login extends GW_Module
{
	public $default_view='login';

	function init()
	{
		parent::init();
	}

	function viewLogin()
	{
		
		return ['autologin'=>GW_Auth::isAutologinEnabled()];	
	}

	function doLogin()
	{
		
		$keep_username=strtotime(GW::s('GW_LOGIN_NAME_EXPIRATION'));
		
		list($user,$pass) = $_POST['login'];
		setcookie('login_0', $user, $keep_username, $this->app->sys_base);
		
		//is request from dialog
		$dialog = basename($this->app->path) == 'dialog';
		
		$params=[];
		
		
		if(!$this->app->user = $this->app->auth->loginPass($user,$pass)){
			$this->setError($this->app->auth->error);
			$params['login_fail']=1;
			$path = false;
			
		}else{
			$this->tpl_vars['success']=1;
			$success=true;
			$path = "";
			
			//autologin
			if($_REQUEST['login_auto'] && GW_Auth::isAutologinEnabled())
			{
				setcookie('login_7', $this->app->user->getAutologinPass(), strtotime(GW::s('GW_AUTOLOGIN_EXPIRATION')), $this->app->sys_base);
				$this->app->auth->session['autologin']=1;
			}
			
			//exit;
			
		}
		

		
		if(!$dialog)
			$this->app->jump($path,$params);	
		
	}

	function viewLogout()
	{
		$this->app->user->onLogout();
		$this->app->auth->logout();
		$this->app->jump(GW::s('ADMIN/PATH_LOGIN'));		
	}
	
	function viewDialog()
	{
		//empty
	}
}