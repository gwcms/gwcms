<?php

class GW_Auth
{

	/**
	 * 
	 * @var GW_User or GW_Customer
	 */
	public $user0;
	public $session;
	public $error;

	function __construct($user0, &$session)
	{
		$this->user0 =  $user0;

		$this->session =& $session;
		
		//d::dumpas($this->session);
	}

	function getUserByUserID($id)
	{
		
		//d::dumpas($this->user0->find(Array('id=?', $id)));
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


		//pasalinu featura kad galetu background requestus daryt
		//request must be from same ip
		//$sameip = (isset($this->session['ip_address']) && $this->session['ip_address'] == $_SERVER['REMOTE_ADDR']);
		
		$logedin =  ($user_id = intval($this->session["user_id"] ?? 0));
		//d::dumpas($_GET);
		

		if (isset($_GET['temp_access'])) {
			list($uid, $token) = explode(',', $_GET['temp_access']);

			if (GW::getInstance('GW_Temp_Access')->getTempAccess($uid, $token)) {
				$autologin = 1; 
				$user = GW::getInstance('GW_User')->createNewObject($uid, 1);
			} else {
				die(json_encode(['error' => 16532, 'error_message' => 'Invalid token']));
			}
		} elseif (isset($_GET['GW_CMS_API_AUTH']) && $_GET['GW_CMS_API_AUTH']) {
			
			$autologin = 1; //session expired kad neziuretu
			$user = $this->loginApi($_GET['GW_CMS_API_AUTH']);
			
			if(!$user)
				$this->setError('/G/GENERAL/API_AUTH_FAIL');
			
			//prikurs logu ir nereikalingu sesiju
			if(isset($_GET['auth_init_session']))
				$this->login($user);

			unset($_GET['GW_CMS_API_AUTH']);
		}elseif ($logedin) {
			$user = $this->getUserByUserID($user_id);
		} elseif ($autologin) {
			$user = $this->loginAuto($cookieUsername, $cookiePass);
		}elseif(isset($_GET['REMOTE_AUTH_USER'])) {
			
			//remote authentification, user has predefined url, which is endpoint where
			//gwcms asks wheather authorise user or not
			$tmpuser = $this->user0->getByUsername($_GET['REMOTE_AUTH_USER']);
			
			if($tmpuser && $tmpuser->remote_auth_url)
			{
				
				//d::dumpas($_GET['REMOTE_AUTH_USER']);
				$url = Navigator::buildURI($tmpuser->remote_auth_url, ['SESSID'=>@$_GET['SESSID'],'REMOTE_AUTH_USER'=>$_GET['REMOTE_AUTH_USER']]);
				$resp = file_get_contents($url);
			
				if($resp == md5($tmpuser->username))
				{
					$user = $tmpuser;
					$autologin = 1;
					
					$this->login($user);
				}else{
					$this->setError('/G/GENERAL/REMOTE_AUTH_FAILED');
				}
			}
			
		}
		
		
		if (!isset($user) || !$user)
			return false;
//			return $this->setError('/G/GENERAL/NOT_LOGGEDIN');
		
		if (!$autologin &&  !$user->isSessionNotExpired(isset($this->session['last_request']) ? $this->session['last_request']: -1) ) { //jei autologin neveikia tai sesijos galiojimas yra
			$this->logout();
			
			return $this->setError('/G/GENERAL/SESSION_EXPIRED');
		}

		if (isset($this->session['autologin']) && $this->session['autologin'])
			$user->autologgedin = true;

		if ($user->banned == 1)
			return $this->setError('/G/GENERAL/USER_BANNED');
		if ($user->active == 0)
			return $this->setError('/G/GENERAL/USER_INNACTIVE');
		
		
		if($user)
			$this->session['last_request'] = time();

		
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
		
		$user->onLogin($_SERVER['REMOTE_ADDR'], @$_SERVER['HTTP_USER_AGENT']);
		
		$this->session['last_request'] = time();
		
		//store some login info
		$inf = GW_Request_Helper::visitorInfo();
		$msg = "ip: {$inf['ip']}" . (isset($inf['proxy']) ? " | {$inf['proxy']}" : '') . (isset($inf['referer']) ? " | {$inf['referer']}" : '');
		GW_DB_Logger::msg($msg, 'user', 'login', $user->id, $inf['browser']);

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
		//if (!$this->session['switchUser'])
		//	$this->session['switchUser'] = $this->session['user_id'];
		
		$this->session['switchUser'] = $this->session['user_id'];
		$this->session['user_id'] = $id;
	}

	function switchUserReturn()
	{
		$this->session['user_id'] = $this->session['switchUser'];
		unset($this->session['switchUser']);
	}
	
	function isUserSwitched()
	{
		return isset($this->session['switchUser']);
	}
	
	function getOrigUser()
	{
		return $this->session['switchUser'];
	}
	
	static function adminLoginToSite($userid, $adminid)
	{
		$_SESSION[GW::s('SITE/AUTH_SESSION_KEY')] = [
		    'user_id' => $userid, 
		    'ip_address' => $_SERVER['REMOTE_ADDR'], 
		    'admin_user_id' => $adminid,
		    'last_request' => time()
		];
	}
	
}
