<?



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
		
		$keep_username=strtotime(GW::$static_conf['GW_LOGIN_NAME_EXPIRATION']);
		
		list($user,$pass) = $_POST['login'];
		setcookie('login_0', $user, $keep_username, Navigator::getBase());
		
		//is request from dialog
		$dialog = basename(GW::$request->path) == 'dialog';
		
		
		if(!GW::$user = GW::$auth->loginPass($user,$pass)){
			$this->setErrors(GW::$auth->error);
		}else{
			$this->smarty->assign('success', 1);
			$success=true;
			
			//autologin
			if($_REQUEST['login_auto'] && GW_Auth::isAutologinEnabled())
			{
				setcookie('login_7', GW::$user->getAutologinPass(), strtotime(GW::$static_conf['GW_AUTOLOGIN_EXPIRATION']), Navigator::getBase());
				GW::$auth->session['autologin']=1;
			}
			
			//exit;
			
		}
		
		$ln=$_REQUEST['ln'] ? $_REQUEST['ln'] : Null;
		
		if($ln)
			setcookie('login_ln', $ln, $keep_username, Navigator::getBase());

		if(!$dialog)
			GW::$request->jump('', Array('ln'=>$ln));	
	}

	function viewLogout()
	{
		GW::$user->onLogout();
		GW::$auth->logout();
		GW::$request->jump(GW::$static_conf['GW_SITE_PATH_LOGIN']);		
	}
	
	function viewDialog()
	{
		//empty
	}
}