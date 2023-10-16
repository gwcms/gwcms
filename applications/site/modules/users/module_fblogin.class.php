<?php

class Module_FBLogin extends GW_Public_Module 
{

	function init() 
	{
		$this->user_cfg = new GW_Config('customers/');
		$this->user_cfg->preload('');	
		
		GW::$devel_debug = true;
	}

	function viewRedirect()
	{

		
		$fb = new Facebook\Facebook([
		    'app_id' => $this->user_cfg->fb_app_id,
		    'app_secret' => $this->user_cfg->fb_app_secret,
		    'default_graph_version' => 'v2.4',
		]);

		$helper = $fb->getRedirectLoginHelper();

		//,'user_birthday','user_about_me'
		$permissions = ['email']; // Optional permissions
		$args = [];
		
		if(isset($_GET['reqtoken'])){
			$_SESSION['thirdPartyReqToken'] = ['token'=>$_GET['reqtoken'], 'returnurl'=>$_GET['returnurl']];
		}
		
		
		$loginUrl = $helper->getLoginUrl($this->app->buildURI('direct/users/fblogin/login',[],['absolute'=>1]), $permissions);
			

		
		if($_SERVER['HTTP_HOST'] != GW::s('MAIN_HOST'))
		{
			$this->anotherHostAuth();
		}
		
		
		//if(isset($_GET['reqtoken']))
		//{
		//	
		//}
		
		
		session_commit();
		session_write_close();
		
		
		header('Location: '.$loginUrl);		
		exit;
	}
	
	function viewVerifyToken()
	{
		$fb = new Facebook\Facebook([
		    'app_id' => $this->user_cfg->fb_app_id,
		    'app_secret' => $this->user_cfg->fb_app_secret,
		    'default_graph_version' => 'v2.4',
		]);

		$facebook->setAccessToken($_REQUEST['access_token']);

		if (($userId = $facebook->getUser())) {
		    die('token ok');
		}else{
			die('bad token');
		}		
	}

	function viewLogin() 
	{
		
		file_put_contents('/tmp/testFb_login', json_encode(['get'=>$_GET,'post'=>$_POST, 'session'=>$_SESSION],JSON_PRETTY_PRINT));
		
		$fb = new Facebook\Facebook($test = [
		    'app_id' => $this->user_cfg->fb_app_id,
		    'app_secret' => $this->user_cfg->fb_app_secret,
			'default_graph_version' => 'v2.4',
		]);


		$helper = $fb->getRedirectLoginHelper();

		try {
			$accessToken = $helper->getAccessToken();
		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error  
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues  
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		if (!isset($accessToken)) {
			if ($helper->getError()) {
				header('HTTP/1.0 401 Unauthorized');
				echo "Error: " . $helper->getError() . "\n";
				echo "Error Code: " . $helper->getErrorCode() . "\n";
				echo "Error Reason: " . $helper->getErrorReason() . "\n";
				echo "Error Description: " . $helper->getErrorDescription() . "\n";
			} else {
				header('HTTP/1.0 400 Bad Request');
				echo 'Bad request';
			}
			exit;
		}



		try {
			// Returns a `Facebook\FacebookResponse` object
			$response = $fb->get('/me?fields=id,name,email,first_name,gender,middle_name,birthday,education,languages', $accessToken->getValue());
		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		$fbusr = $response->getGraphUser();
		
		if(!$fbusr->getEmail())
		{
			$this->app->setMessage('Facebook did not gave email address so its not possible continue with facebook, please register regular way with email address');
			$this->app->jump(GW::s('SITE/PATH_REGISTER'));
		}



	/*

		  [fb_user] => Facebook\GraphNodes\GraphUser Object
		  (
		  [items:protected] => Array
		  (
		  [id] => 414849898720939
		  [name] => Vidmantas Work
		  [email] => vidmantas.work@gmail.com
		  [first_name] => Vidmantas
		  [gender] => male
		  [birthday] => DateTime Object
		  (
		  [date] => 1984-04-05 00:00:00
		  [timezone_type] => 3
		  [timezone] => Europe/Helsinki
		  )

		  )

		  )

    [0] => Facebook\GraphNodes\GraphUser Object
		  (
            [items:protected] => Array
		  (
                    [id] => 10201184672096426
                    [name] => Vidmantas Norkus
                    [email] => vidmantas.norkus@gw.lt
                    [first_name] => Vidmantas
                    [gender] => male
		  )

		  )


        )
		 * 
		 */

		$_SESSION['fb_user'] = (object)[
		    'id'=>$fbusr->getId(),
		    'title'=>$fbusr->getName(),
		    'email'=>$fbusr->getEmail(), 
		    'name'=>$fbusr->getFirstName(),
		    'surname'=>trim(str_ireplace($fbusr->getFirstName(), '', $fbusr->getName())),
		    'gender'=>$fbusr->getgender()
		];
		
		$this->app->jump('direct/users/fblogin/signInOrRegister');
	}




	function viewSignInOrRegister() 
	{

		$fbusr = $_SESSION['fb_user'];
		
		if(! $fbusr->id)
		{
			if($_SESSION['thirdPartyReqToken'])
				$this->reply3rdparty(['result'=>'fail']);
			
			$this->setError("/M/USERS/LOGIN_FAIL");
			$this->app->jump('');
		}

		if($user=GW_Customer::singleton()->find(['(fbid=? OR email=? OR username=?) AND active=1', $fbusr->id, $fbusr->email, $fbusr->email])) 
		{			
			if(!$user->fbid)
				$user->saveValues(['fbid'=>$fbusr->id]);

				$this->app->user = $user;
				$this->app->auth->login($user);
				
				
				if($_SESSION['thirdPartyReqToken'] ?? false){
							
					$token = $_SESSION['thirdPartyReqToken']['token'];
					$tokenenc = GW::db()->aesCrypt($token, $user->insert_time.$user->pass);
					
					GW_Temp_Data::singleton()->store($user->id,'3rdhostauth','token', $tokenenc);
					
					
					//;
					
					$this->reply3rdparty(['result'=>'ok','userid'=>$user->id]);
					
				}
				
				$this->testIfJumpRequest();

				//GW::$app->setMessage("/m/USERS/LOGIN_WELCOME");
				$this->app->jump(GW::s('SITE/PATH_USERZONE'));
		}else{
			
				//nerastas, useris, tures pasirinkt viena is triju ar susilinkint, ar susikurt nauja su nurodytu vardu ir emailu ar suskurt nauja su kitais kredencialais
				if($_SESSION['thirdPartyReqToken'] ?? false){
							
					$token = $_SESSION['thirdPartyReqToken']['token'];
					$tokenenc = GW::db()->aesCrypt($token, $user->insert_time.$user->pass);
					
					GW_Temp_Data::singleton()->store($user->id,'3rdhostauth','token', $tokenenc);
					
					
					//;
					$crypt = GW::db()->aesCrypt(json_encode($_SESSION['fb_user']), GW::s('DB/UPHD'));
					$this->reply3rdparty(['result'=>'solve','data'=>$crypt]);
					
				}			
			
		}
				
		$this->tpl_vars['fbuser'] = $fbusr;
		
	}
	
	function testIfJumpRequest()
	{
		if($tmp = $this->app->sess('navigate_after_auth')){
			$this->app->sess('navigate_after_auth', null);
			header("Location: ".$tmp);
			exit;
		}		
	}
				
	function doSignupOrLink()
	{
		$fbusr = $_SESSION['fb_user'];
		
		if($_POST['action']=='link'){
			
			if($this->app->user->id && !$this->app->user->fbid)
			{
				$this->app->user->saveValues(['fbid'=>$fbusr->id]);

				$this->app->setMessage( sprintf(GW::ln("/M/USERS/PROFILE_WAS_LINKED_WITH_FB_ACCOOUNT"), $fbusr->getName()));

				$this->app->jump(GW::s('SITE/PATH_USERZONE'));
			}else{			
				$_SESSION['user_link_with_fb'] = $fbusr;
				$this->app->jump(GW::s('SITE/PATH_LOGIN'));
			}
			
		}elseif($_POST['action']=='register'){
			
			$this->app->jump(GW::s('SITE/PATH_REGISTER'),['act'=>'doRegisterFBacc']);
			
		}elseif($_POST['action']=='register_custom'){
			$_SESSION['user_link_with_fb'] = $fbusr;
			
			$this->setErrorItem((array)$fbusr,'reguser');	
			$this->app->jump(GW::s('SITE/PATH_REGISTER'));
		}
		
	}
	
	
	function anotherHostAuth()
	{	
		if(!isset($_SESSION['fbauhtreq']))
			$_SESSION['fbauhtreq'] = GW_String_Helper::getRandString(25);
		
		session_commit();
		session_write_close();

		$returnurl = Navigator::getBase(true).strtolower($this->app->ln).'/direct/users/fblogin/returnfrommainsite';
		

		$url = Navigator::buildURI("https://".GW::s('MAIN_HOST').$_SERVER['REQUEST_URI'], ['reqtoken'=>$_SESSION['fbauhtreq'], 'returnurl'=>$returnurl]);
		
		//d::dumpas($url);
		//d::dumpas($url);

		header('Location: '.$url);
		exit;		
	}
	
	function reply3rdparty($resp)
	{
		//file_put_contents('/tmp/artistb_reply3rdparty', );
		
		header('Location: '. Navigator::buildURI($_SESSION['thirdPartyReqToken']['returnurl'], $resp));
		unset($_SESSION['thirdPartyReqToken']);
		exit;
	}
	
	
	function viewVerifyIsConnected()
	{
		$token = GW_Temp_Data::singleton()->readValue($_GET['userid'],'3rdhostauth','token');
		
		//$resp = GW::db()->aesCrypt($passenc, GW::s('CC_ENC_STR'), true);	
		echo json_encode(['req'=>$_GET,'token'=>$token]);
		exit;
	}
		
	function viewReturnFromMainSite()
	{
		if($_GET['result']=='ok'){
			initEnviroment(GW_ENV_PROD);

			$verifyLink = GW::s('SITE_URL').'/'.strtolower($this->app->ln).'/direct/users/fblogin/verifyisconnected';
			$verifyLink = Navigator::buildURI($verifyLink, ['userid'=>$_GET['userid']]);
			$resp = file_get_contents($verifyLink);
			
			$reqsecret = $_SESSION['fbauhtreq'];
			
			$respdec = json_decode($resp, true);
			$tokenenc = $respdec['token'];
			
			$user = GW_User::singleton()->createNewObject($_GET['userid'], true);
			$decrypted = GW::db()->aesCrypt($tokenenc, $user->insert_time.$user->pass, true);
			
			/*
			 * debug
			d::dumpas([
			    '$verifyLink'=>$verifyLink,
			    'reqsecret'=>$reqsecret, 
			    'responsesecret'=>$resp, 
			    'respdec'=>$respdec, 
			    'decryped'=>$decrypted
			]);*/
			
			if($decrypted==$reqsecret){
				$this->app->user = $user;
				$this->app->auth->login($user);
				$this->app->jump(GW::s('SITE/PATH_USERZONE'));	
				exit;
			}else{
				$this->setError("Unknown 3rdparty authentification error");
			}
				
		}elseif($_GET['result']=='solve'){
			
			
			
			unset($_SESSION['thirdPartyReqToken']);
			

			$data = GW::db()->aesCrypt($_GET['data'], GW::s('DB/UPHD'), true);
			$_SESSION['fb_user'] = json_decode($data);

			$this->app->jump('direct/users/fblogin/signInOrRegister');

			//$this->reply3rdparty(['result'=>'solve','data'=>$crypt]);
			
		}
		
		$this->setError("Auth failed");
		$this->jump('/');
		
	}

}
