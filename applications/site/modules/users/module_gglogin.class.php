<?php

class Module_GGLogin extends GW_Public_Module 
{

	function init() 
	{
		$this->user_cfg = new GW_Config('customers/');
		$this->user_cfg->preload('');	
		
		GW::$devel_debug = true;
		
		$this->tpl_vars['page_title'] = GW::ln("/m/LOGIN_WITH_GG");
	}

	function viewRedirect()
	{
		
		
		if(isset($_GET['after_auth_nav'])){
			$this->app->sess('after_auth_nav', $_GET['after_auth_nav']);
		}
		

		
	
		
		if($this->user_cfg->gg_use_auth_gw){
			
			$comebackurlAuthgw = $this->app->buildURI('direct/users/gglogin/loginAuthGw',[],['absolute'=>1]);
			
			$req_id = GW_String_Helper::getRandString(25);
			$_SESSION['auth_gw_req_id']=$req_id;
			
			session_commit();
			session_write_close();
			$auth_gw_url = GW::s('GW_GG_SERVICE')."?request_id=".$req_id."&redirect2=". urlencode($comebackurlAuthgw);
			
			
			header('Location: '.$auth_gw_url);	
			exit;
		}
		
		//removed autonomous login processing
	}

	function viewLogin() 
	{
		//removed autonomous login processing
	}
	
	function viewLoginAuthGw()
	{
		$req_id = $_SESSION['auth_gw_req_id'];
		$dat = file_get_contents(GW::s('GW_GG_SERVICE').'?get_response='.$req_id);
		$dat = json_decode($dat);
		
		if($dat->error){
			$this->setError(GW::ln("/M/users/LOGIN_FAILED"). ': '. $dat->error);
			unset($_SESSION['3rdAuthUser']);
			
			$this->app->jump('direct/users/users/login');
		}else{
			$dat->type='google';
			$_SESSION['3rdAuthUser'] = $dat;
		
		
			$this->app->jump('direct/users/users/signInOrRegister');
		}
	}



	//gryzta jau turim informacija apie vartotoja, vartotojo id, email, varda pavarde
	
				


}
