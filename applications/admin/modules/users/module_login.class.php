<?php



class Module_Login extends GW_Module
{
	public $default_view='login';

	function init()
	{
		parent::init();
		$this->config = new GW_Config('gw_'.$this->module_path[0].'/');		
	}

	function viewLogin()
	{		
		$this->tpl_vars['autologin'] = GW_Auth::isAutologinEnabled();
		
		
		

		
		
		//1. esame slave site ir peradresuojame tada i main site autorizacija
		if(GW::s('PROJECT_ENVIRONMENT') != GW_ENV_DEV && GW::s("MULTISITE") && $_SERVER['HTTP_HOST'] !=GW::s('MAIN_HOST')  && !isset($_GET['avoidmainsiteauth'])){
					
			if(!$this->app->user){
				$returnurl = "https://".$_SERVER['HTTP_HOST'].'/admin/'.$this->app->ln."/users/login/acceptmainsite";
				//d::Dumpas("peradresuojame i main host ir paprasome kad grazintu atgal: ".$returnurl);
				header('Location: https://'.GW::s('MAIN_HOST').'/admin/'.$this->app->ln.'/users/login?return_url=' . urlencode($returnurl));
			}else{
				// jau prisijunge tai nieko ir daryt nebereikia
				$this->app->jump('/');
				//$token = $this->storeToken();
				//$uri = Navigator::buildURI($url,['mainsitetoken'=>$token]);
			}
		}
		
		
		//2. esame mainhoste jei prisilogine iskart grazinam su tokenu jei ne tada i sessija issisaugom gryzimo adresa
		if(isset($_GET['return_url'])){
			
			//jei jau prisilogines tai iskart sukurt tokena ir grazint i slavesite
			if($this->app->user){
				$uri = $_GET['return_url'];
				
				if(strpos($uri, 'acceptmainsite') !==false){
					$this->multisiteAuthRedirect($uri);
				}
				
				//d::dumpas($uri);
				
				header("Location: ".$uri);
				exit;
			}
			
			
			$this->app->sess('after_auth_nav', $_GET['return_url']);
			
			unset($_GET['return_url']);
			$this->app->jump();			
		}
		
	
	}
	
	//esame slave site tikriname gauta token
	function viewAcceptMainsite()
	{
		
		$dataraw= file_get_contents($url='https://'.GW::s('MAIN_HOST').'/admin/'.$this->app->ln.'/users/login/getbytoken?token='.$_GET['mainsitetoken'].'&temp_access='.$_GET['mainsitepass']);
		
		
		
		$data = json_decode($dataraw);
		/*
		d::dumpas([
		    'inner_Request_to_main_site_response'=>$dataraw,
		    'token'=>$_GET['mainsitetoken']
		]);
		 * 
		 */
		
		if($user = GW_User::singleton()->find(['id=? AND username=? AND active=1', $data->id, $data->username])){
			$this->setMessage('Authorisation approved by '.GW::s('MAIN_HOST').'. Welcome!');
			$this->app->auth->login($user);
			
			$this->app->jump('/');
		}else{
			$this->setMessage('Authorisation rejected by '.GW::s('MAIN_HOST').' <pre>'.json_encode($dataraw).'</pre>');
			$this->app->jump('users/login', ['avoidmainsiteauth'=>1]);
		}
	}
	
	//main site response stored token
	function viewGetByToken()
	{
		//if(!in_array(GW::ip(), ['82.135.242.67','127.0.0.1']) ){ die("ip not permitted ".GW::ip());}
		GW_Temp_Data::singleton()->cleanup();
		$data = GW_Temp_Data::singleton()->readAndDelete(GW_USER_SYSTEM_ID, 'authtoken', $_GET['token']);
		echo $data;
		exit;
	
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
		
		if(isset($_POST['link_with_3rd']) && $this->app->sess('temp_link_with3rd') && $this->app->user)
		{
			$map = ['fb'=>'adminfbid','gg'=>'adminggid'];
			$map1 = ['fb'=>'Facebook','gg'=>'Google'];			
			
			list($gw,$remoteid) = explode('|',$this->app->sess('temp_link_with3rd'));
			
			$this->setMessage('Link success, now you can login using '.$map1[$gw]);
			$this->app->user->ext->{"admin{$gw}id"} = $remoteid;
			
			 $this->app->sess('temp_link_with3rd','');
		}	

		
		if(!$dialog)
			if($this->app->sess('after_auth_nav')){
				
				
				
				
				
				$uri = $this->app->sess('after_auth_nav');
				$this->app->sess('after_auth_nav', "");
				
				//jei esame multisite pagrindiniam hoste patikri ar redirect url yra i slave site, jei taip dadeti tokena
				if(strpos($uri, 'acceptmainsite') !==false){
					$this->multisiteAuthRedirect($uri);
				}
				
				
				
				
				header("Location: ".$uri);
				exit;
			}else{
				$this->app->jump($path,$params);	
			}
		
	}
	
	//multisiteauth isaugoti main hoste
	function storeToken()
	{
		$token = GW_String_Helper::getRandString(50);
		$data = ['id'=>$this->app->user->id, 'username'=>$this->app->user->username];
		GW_Temp_Data::singleton()->store(GW_USER_SYSTEM_ID,'authtoken', $token, json_encode($data),  '1 minute');
		return $token;
	}
	
	function multisiteAuthRedirect($uri)
	{
		
		$token = GW::getInstance('gw_temp_access')->getToken($this->app->user->id);
		$to_avoidcaptcha = $this->app->user->id . ',' . $token;
		
		$token = $this->storeToken();
		$uri = Navigator::buildURI($uri,['mainsitetoken'=>$token,'mainsitepass'=>$to_avoidcaptcha]);
		header("Location: ".$uri);
		exit;
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
	
	function doAuthWith3rd()
	{
		$comebackurlAuthgw = $this->app->buildURI(false,['act'=>'doAuthFinishWith3rd','gw'=>$_GET['gw']],['absolute'=>1]);
		$req_id = GW_String_Helper::getRandString(25);
		$_SESSION['adm_auth_gw_lt_req_id']=$req_id;
			
		session_commit();
		session_write_close();
		$auth_gw_url = GW::s('GW_'. strtoupper($_GET['gw']).'_SERVICE')."?request_id=".$req_id."&redirect2=". urlencode($comebackurlAuthgw);
		header('Location: '.$auth_gw_url);		
		exit;			
	}

	
	
	function doAuthFinishWith3rd()
	{	
		$gw = $_GET['gw'];
		$req_id = $_SESSION['adm_auth_gw_lt_req_id'];
		$dat = file_get_contents(GW::s('GW_'.strtoupper($gw).'_SERVICE').'?get_response='.$req_id);
		$dat = json_decode($dat);
		
		
		$map = ['fb'=>'adminfbid','gg'=>'adminggid'];
		$map1 = ['fb'=>'Facebook','gg'=>'Google'];
		$field = $map[$gw];
		
		if(!isset($dat->id)){
			$this->setError("{$map1[$gw]} auth failed");
			$this->jump();
		}
			
			
		$remoteid = $dat->id;
		
		
		$list = GW_User::singleton()->extensions['keyval']->findOwner(['`key`=? AND value=?',$field,$remoteid]);
		
		if(count($list)>1){
			$this->setError("This {$map1[$gw]} user linked with more than one account please unlink others");
			$this->jump();
		}elseif(count($list)==0){
			$link = $this->app->buildUri('users/profile');
			$profpage="<a href='$link'>profile page</a>";
			$this->app->sess('temp_link_with3rd', $gw.'|'.$remoteid);
			
			$this->setPlainMessage("Please login now as usual", GW_MSG_INFO);
			$this->jump();
		}else{
			
			$user= GW_User::singleton()->find(['(id=?) AND active=1', $list[0]]);
			
			if($user){
				$this->app->user = $user;
				$this->app->auth->login($user);	

				$this->setMessageEx(["text"=>"Auth using {$map1[$gw]} ok", "type"=>GW_MSG_SUCC, 'float'=>1, 'time'=>1000]);				
				
				$this->app->jump('/');
			}else{
				$this->setError('Bad news: User might be either removed or deactivated');
			}
		}

		$this->jump();
	}	
	
	

	
}