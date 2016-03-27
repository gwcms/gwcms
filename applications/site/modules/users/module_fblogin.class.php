<?php

class Module_FBLogin extends GW_Public_Module {

	function init() {
		
	}

	function viewLogin() {

		$fb = new Facebook\Facebook($test = [
			'app_id' => $this->app->user_cfg->fb_app_id,
			'app_secret' => $this->app->user_cfg->fb_app_secret,
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

// Logged in  
		echo '<h3>Access Token</h3>';
		var_dump($accessToken->getValue());

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

		$user = $response->getGraphUser();


		$_SESSION['fb_user'] = $user;
		$_SESSION['fb_access_token'] = $accessToken;


		$this->app->jump('direct/users/fblogin/signInOrRegister');
	}

	function viewSignInOrRegister() {
		/*
		  d::dumpas($_SESSION);
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

		  [fb_access_token] => Facebook\Authentication\AccessToken Object
		  (
		  [value:protected] => CAAMlXpgZByg4BADwB8chhHZAyZCtYVDyqCrQumwueO5iIoHwBVaHcrqeiZC1PCbV2KLGQgeIBHvfZCZB3KRAviRXGITLhVtpDsDPSJyST5hVhNKH7mdz1k4EINGL6ecYGYtEhTnVyZBe0qPDyYyZB5Cfum4fEgd4Xo8F9PN9rHNJmvzV0AXXrOYOKSzePFOjm8ZACrGgdTWLeqQZDZD
		  [expiresAt:protected] => DateTime Object
		  (
		  [date] => 2015-12-19 16:38:34
		  [timezone_type] => 3
		  [timezone] => Europe/Helsinki
		  )

		  )
		 * 
		 */

		if (!$_SESSION['fb_user'] instanceof Facebook\GraphNodes\GraphUser || !$_SESSION['fb_user']->getId()) {
			$this->app->setErrors("/m/LOGIN_FAIL");
			$this->app->jump('');
		}

		$fbusr = $_SESSION['fb_user'];

		//https://developers.facebook.com/docs/reference/android/3.0/interface/GraphUser/

		if ($this->app->user->id && !$this->app->user->fbid) {
			$this->app->user->fbid = $fbusr->getId();

			$this->app->user->updateChanged();

			$this->app->setMessage(sprintf(gw::l("/M/USERS/PROFILE_WAS_LINKED_WITH_FB_ACCOOUNT"), $fbusr->getName()));

			$this->app->jump(GW::s('SITE/PATH_USERZONE'));
		} else {


			if ($user = GW_Customer::singleton()->find(['fbid=? AND active=1', $fbusr->getId()])) {
				$this->app->user = $user;
				$this->app->auth->login($user);

				//GW::$app->setMessage("/m/USERS/LOGIN_WELCOME");
				$this->app->jump(GW::s('SITE/PATH_USERZONE'));
			} else {
				$this->app->setMessage("Create account then press [F] button to link accout with fb profile - then you can use quick login feature");
				$this->app->jump(GW::s('SITE/PATH_TRANS/users/users/register/_'));
			}
		}
	}

}
