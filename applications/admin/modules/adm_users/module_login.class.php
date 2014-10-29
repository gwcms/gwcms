<?php



class Module_Login extends GW_Module
{

	function init()
	{
		parent::init();
	}

	function viewDefault()
	{
		$this->smarty->assign('autologin', GW_Auth::isAutologinEnabled());
		// rodyt login forma	
	}

	function doLogin()
	{
		
		$keep_username=strtotime(GW::s('GW_LOGIN_NAME_EXPIRATION'));
		
		list($user,$pass) = $_POST['login'];
		setcookie('login_0', $user, $keep_username, $this->app->sys_base);
		
		//is request from dialog
		$dialog = basename($this->app->path) == 'dialog';
		
		
		if(!$this->app->user = $this->app->auth->loginPass($user,$pass)){
			$this->setErrors($this->app->auth->error);
		}else{
			$this->smarty->assign('success', 1);
			$success=true;
			
			//autologin
			if($_REQUEST['login_auto'] && GW_Auth::isAutologinEnabled())
			{
				setcookie('login_7', $this->app->user->getAutologinPass(), strtotime(GW::s('GW_AUTOLOGIN_EXPIRATION')), $this->app->sys_base);
				$this->app->auth->session['autologin']=1;
			}
			
			//exit;
			
		}
		
		$ln=$_REQUEST['ln'] ? $_REQUEST['ln'] : Null;
		
		if($ln)
			setcookie('login_ln', $ln, $keep_username, $this->app->sys_base);

		if(!$dialog)
			$this->app->jump('', Array('ln'=>$ln));	
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