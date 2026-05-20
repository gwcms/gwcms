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
		$req_id = $_SESSION['auth_gw_req_id'] ?? '';
		if (!$req_id)
			return $this->authGwUnexpectedReply('missing request id', $req_id, '');

		$url = GW::s('GW_GG_SERVICE').'?get_response='.$req_id;
		$raw = @file_get_contents($url);
		if ($raw === false)
			return $this->authGwUnexpectedReply('empty or unreadable response', $req_id, '', $url);

		$dat = json_decode($raw);
		if (!$dat || !is_object($dat))
			return $this->authGwUnexpectedReply('invalid json: '.json_last_error_msg(), $req_id, $raw, $url);
		
		if($dat->error ?? false){
			$this->setError(GW::ln("/M/users/LOGIN_FAILED"). ': '. $dat->error);
			unset($_SESSION['3rdAuthUser']);
			
			$this->app->jump('direct/users/users/login');
		}

		if (empty($dat->id))
			return $this->authGwUnexpectedReply('missing user id in response', $req_id, $raw, $url);

		$dat->type='google';
		$_SESSION['3rdAuthUser'] = $dat;
	
	
		$this->app->jump('direct/users/users/signInOrRegister');
	}

	function authGwUnexpectedReply($reason, $req_id, $raw, $url='')
	{
		unset($_SESSION['3rdAuthUser']);

		$mail = [
			'subject' => GW::s('PROJECT_NAME').' google authorisation reply unexpected format',
			'body' => print_r([
				'reason' => $reason,
				'request_id' => $req_id,
				'url' => $url,
				'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
				'referer' => $_SERVER['HTTP_REFERER'] ?? '',
				'ip' => GW::ip(),
				'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
				'raw_response' => mb_substr((string)$raw, 0, 20000),
			], true),
			'noAdminCopy' => 1,
			'noStoreDB' => 1,
		];
		GW_Mail_Helper::sendMailDeveloper($mail);

		$this->setError(GW::ln("/M/users/LOGIN_FAILED"));
		$this->app->jump('direct/users/users/login');
	}



	//gryzta jau turim informacija apie vartotoja, vartotojo id, email, varda pavarde
	
				


}
