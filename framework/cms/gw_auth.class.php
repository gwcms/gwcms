<?php

class GW_Auth
{

	/**
	 * 
	 * @var GW_User or GW_Customer
	 */
	var $user0;
	var $session;
	var $error;

	function __construct($user0)
	{
		$this->user0 =  $user0;

		if (get_class($this->user0) == "GW_User")
			$this->session = & $_SESSION[AUTH_SESSION_KEY];
		else
			$this->session = & $_SESSION[PUBLIC_AUTH_SESSION_KEY];
	}

	function getUserByUserID($id)
	{
		return $this->user0->find(Array('id=?', $id));
	}

	function setError($err_str)
	{
		$this->error = $err_str;
		return false;
	}

	/**
	 * if user is logged returns user object
	 * else - false
	 */
	function isLogged()
	{
		$cookiePass = isset($_COOKIE['login_7']) ? $_COOKIE['login_7'] : false; // is autologin pass
		$cookieUsername = isset($_COOKIE['login_0']) ? $_COOKIE['login_0'] : false; // is username
		$autologin = isset($_COOKIE['login_7']) && $_COOKIE['login_7'] && self::isAutologinEnabled();


		$logedin = (isset($this->session['ip_address']) && $this->session['ip_address'] == $_SERVER['REMOTE_ADDR']) && ($user_id = (int) $this->session["user_id"]);

		if (isset($_GET['temp_access'])) {
			list($uid, $token) = explode(',', $_GET['temp_access']);

			if (GW::getInstance('GW_Temp_Access')->getTempAccess($uid, $token)) {
				$user = GW::getInstance('GW_User')->createNewObject($uid, 1);
			} else {
				die(json_encode(['error' => 16532, 'error_message' => 'Invalid token']));
			}
		} elseif ($logedin) {
			$user = $this->getUserByUserID($user_id);
		} elseif ($autologin) {
			$user = $this->loginAuto($cookieUsername, $cookiePass);
		} elseif ($tmp = @$_GET['GW_CMS_API_AUTH']) {

			$autologin = 1; //session expired kad neziuretu
			$user = $this->loginApi($tmp);

			unset($_GET['GW_CMS_API_AUTH']);
		}


		if (!isset($user) || !$user)
			return $this->setError('/G/GENERAL/NOT_LOGGEDIN');

		if (!$autologin && !$user->isSessionNotExpired()) { //jei autologin neveikia tai sesijos galiojimas yra
			$this->logout();
			$_SESSION['messages'][] = Array(1, '/G/GENERAL/SESSION_EXPIRED');
			return $this->setError('/G/GENERAL/SESSION_EXPIRED');
		}

		if (isset($this->session['autologin']) && $this->session['autologin'])
			$user->autologgedin = true;

		if ($user->banned == 1)
			return $this->setError('/G/GENERAL/USER_BANNED');
		if ($user->active == 0)
			return $this->setError('/G/GENERAL/USER_INNACTIVE');


		return $user;
	}

	function loginPass($username, $password)
	{
		if (!$user = $this->user0->getByUsernamePass($username, $password)) {
			//$this->logout();
			return $this->setError('/G/GENERAL/LOGIN_FAIL');
		}
		if ($user->banned == 1) {
			return $this->setError('/G/GENERAL/USER_BANNED');
		}
		if ($user->active == 0) {
			return $this->setError('/G/GENERAL/USER_INNACTIVE');
		}
		return $this->login($user);
	}

	function loginAuto($username, $pass)
	{
		if (!$user = $this->user0->getUserByAutologinPass($username, $pass))
			return false;

		$this->session['autologin'] = 1;

		return $this->login($user);
	}

	function loginApi($param)
	{
		list($username, $api_key) = explode(':', $param, 2);
		return $this->user0->getUserByApiKey($username, $api_key);
	}

	function login($user)
	{
		$this->session["user_id"] = $user->get('id');
		$this->session['ip_address'] = $_SERVER['REMOTE_ADDR'];

		$user->onLogin($_SERVER['REMOTE_ADDR']);
		
		$this->session['last_request'] = time();
		
		//store some login info
		$inf = GW_Request_Helper::visitorInfo();
		$msg = "ip: {$inf['ip']}" . (isset($inf['proxy']) ? " | {$inf['proxy']}" : '') . (isset($inf['referer']) ? " | {$inf['referer']}" : '');
		GW_DB_Logger::msg($msg, 'user', 'login', $this->id, $inf['browser']);		

		return $user;
	}

	function logout()
	{

		setcookie('login_7', '---', time(), GW::$context->app->sys_base);
		$_COOKIE['login_7'] = false;


		//dump("logging out");
		$this->session = array();
		//$_SESSION=Array();
	}

	static function isAutologinEnabled()
	{
		static $cache;

		if (!$cache)
			$cache = GW::getInstance('GW_Config')->get('gw_users/autologin');

		return $cache;
	}

	function switchUser($id)
	{
		if (!$this->session['switchUser'])
			$this->session['switchUser'] = $this->session['user_id'];

		$this->session['user_id'] = $id;
	}

	function switchUserReturn()
	{
		$this->session['user_id'] = $this->session['switchUser'];
		unset($this->session['switchUser']);
	}
}
