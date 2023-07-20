<?php

class Module_FBLogin extends GW_Public_Module 
{

	function init() 
	{
		$this->user_cfg = new GW_Config('customers/');
		$this->user_cfg->preload('');	
		
		GW::$devel_debug = true;
		
		$this->tpl_vars['page_title'] = GW::ln("/m/LOGIN_WITH_FB");
	}

	function viewRedirect()
	{
		
		
		if(isset($_GET['after_auth_nav'])){
			$this->app->sess('after_auth_nav', $_GET['after_auth_nav']);
		}
		
		$comebackurl = $this->app->buildURI('direct/users/fblogin/login',[],['absolute'=>1]);
		
	
		
		if($this->user_cfg->fb_use_auth_gw){
			
			$comebackurlAuthgw = $this->app->buildURI('direct/users/fblogin/loginAuthGw',[],['absolute'=>1]);
			
			$req_id = GW_String_Helper::getRandString(25);
			$_SESSION['auth_gw_lt_req_id']=$req_id;
			
			session_commit();
			session_write_close();
			$auth_gw_url = GW::s('GW_FB_SERVICE')."?request_id=".$req_id."&redirect2=". urlencode($comebackurlAuthgw);
			
			
			header('Location: '.$auth_gw_url);	
			exit;
		}
		
		
		if($this->user_cfg->fb_app_id){
			$app_id = $this->user_cfg->fb_app_id;
			$app_secret = $this->user_cfg->fb_app_secret;
		}else{
			list($app_id, $app_secret) = explode('|',GW::s('FB_LOGIN'));
		}
		
		
		
		$fb = new Facebook\Facebook([
		    'app_id' => $app_id,
		    'app_secret' => $app_secret,
		    'default_graph_version' => 'v2.4',
		]);

		$helper = $fb->getRedirectLoginHelper();

		//,'user_birthday','user_about_me'
		$permissions = ['email']; // Optional permissions
		$loginUrl = $helper->getLoginUrl($comebackurl, $permissions);
			
		
		
		
		session_commit();
		session_write_close();
		
		
		header('Location: '.$loginUrl);		
		exit;
	}

	function viewLogin() 
	{
		if($this->user_cfg->fb_app_id){
			$app_id = $this->user_cfg->fb_app_id;
			$app_secret = $this->user_cfg->fb_app_secret;
		}else{
			list($app_id, $app_secret) = explode('|',GW::s('FB_LOGIN'));
		}
		
		$fb = new Facebook\Facebook($test = [
		    'app_id' => $app_id,
		    'app_secret' => $app_secret,
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
		
		
		$_SESSION['3rdAuthUser'] = (object)[
		    'id'=>$fbusr->getId(),
		    'title'=>$fbusr->getName(),
		    'email'=>$fbusr->getEmail(), 
		    'name'=>$fbusr->getFirstName(),
		    'surname'=>trim(str_ireplace($fbusr->getFirstName(), '', $fbusr->getName())),
		    'gender'=>$fbusr->getgender(),
		    'type'=>'facebook',
		    'picture'=>'https://graph.facebook.com/'.$fbusr->getId().'/picture?type=small'
		];		
		


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


		
		

		$this->app->jump('direct/users/users/signInOrRegister');
	}
	
	function viewLoginAuthGw()
	{
		$req_id = $_SESSION['auth_gw_lt_req_id'];
		$dat = file_get_contents(GW::s('GW_FB_SERVICE').'?get_response='.$req_id);
		$dat = json_decode($dat);
		$dat->type='facebook';
		$dat->picture='https://graph.facebook.com/'.$dat->id.'/picture?type=small';
		$_SESSION['3rdAuthUser'] = $dat;
		
		$this->app->jump('direct/users/users/signInOrRegister');
	}




			

}
