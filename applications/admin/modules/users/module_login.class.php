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
		$this->tpl_vars['autologin'] = GW_Auth::isAutologinEnabled();
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
		
		if(isset($_POST['link_with_fb']) && $this->app->sess('temp_link_withfb') && $this->app->user)
		{
			$fbid = $this->app->sess('temp_link_withfb');
			$this->setMessage('Link success, now you can login using Facebook');
			$this->app->user->ext->adminfbid = $fbid;
			
			 $this->app->sess('temp_link_withfb','');
		}	

		
		if(!$dialog)
			if($this->app->sess('after_auth_nav')){
				$uri = $this->app->sess('after_auth_nav');
				$this->app->sess('after_auth_nav', "");
				header("Location: ".$uri);
				exit;
			}else{
				$this->app->jump($path,$params);	
			}
		
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
		$this->tpl_vars['dialog']=1;
		$this->tpl_file_name=$this->tpl_dir.'login';		
		
		$this->viewLogin();

	}
	
	function doAuthWithFb()
	{
		$comebackurlAuthgw = $this->app->buildURI(false,['act'=>'doAuthFinishWithFb'],['absolute'=>1]);
		$req_id = GW_String_Helper::getRandString(25);
		$_SESSION['adm_auth_gw_lt_req_id']=$req_id;
			
		session_commit();
		session_write_close();
		$auth_gw_url = "https://auth.gw.lt/fblogin.php?request_id=".$req_id."&redirect2=". urlencode($comebackurlAuthgw);
		header('Location: '.$auth_gw_url);		
		exit;			
	}
	
	
	function doAuthFinishWithFb()
	{	
		$req_id = $_SESSION['adm_auth_gw_lt_req_id'];
		$dat = file_get_contents('https://auth.gw.lt/fblogin.php?get_response='.$req_id);
		$dat = json_decode($dat);
		
		

		
		if(!isset($dat->id)){
			$this->setError('Fb auth failed');
			$this->jump();
		}
			
			
		$fbid = $dat->id;
		
		
		$list = GW_User::singleton()->extensions['keyval']->findOwner(['`key`="adminfbid" AND value=?',$fbid]);
		if(count($list)>1){
			$this->setError('This facebook user linked with more than one account please unlink others');
			$this->jump();
		}elseif(count($list)==0){
			$link = $this->app->buildUri('users/profile');
			$profpage="<a href='$link'>profile page</a>";
			$this->app->sess('temp_link_withfb', $fbid);
			
			$this->setPlainMessage("Please login now as usual", GW_MSG_INFO);
			$this->jump();
		}else{
			
			$user= GW_User::singleton()->find(['(id=?) AND active=1', $list[0]]);
			
			if($user){
				$this->app->user = $user;
				$this->app->auth->login($user);	

				$this->setMessageEx(["text"=>'Auth using Facebook success', "type"=>GW_MSG_SUCC, 'float'=>1, 'time'=>1000]);				
				
				$this->app->jump('/');
			}else{
				$this->setError('Bad news: User might be either removed or deactivated');
			}
				

		}
		

		$this->jump();
	}	
	
}