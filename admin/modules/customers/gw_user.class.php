<?php

class GW_User extends GW_Data_Object 
{

	var $table = 'gw_users';
	var $min_pass_length = 6;
	var $max_pass_length = 16;
	var $validators = Array();
	var $calculate_fields = Array('title' => 1, 'name' => 1/* did project specified */);
	var $ignore_fields = Array('pass_old' => 1, 'pass_new' => 1, 'pass_new_repeat' => 1);
	var $autologgedin = false;

	function setValidators($set) 
	{
		if (!$set)
			return $this->validators = Array(); //remove validators

		$validators_def = Array(
			'username' => Array('gw_string', Array('min_length' => 2, 'max_length' => 120, 'required' => 1)),
			'first_name' => Array('gw_name', Array('min_length' => 2, 'max_length' => 120, 'required' => 1)),
			'second_name' => Array('gw_name', Array('min_length' => 2, 'max_length' => 120, 'required' => 1)),
			'phone' => Array('gw_phone', Array('min_length' => 6, 'max_length' => 20, 'required' => 1)),
			'email' => Array('gw_email', Array('required' => 1)),
			'pass_old' => 1,
			'pass_new' => 1,
			'pass_new_repeat' => 1,
			'unique_email' => 1,
			'license' => 1,
		);

		$validators_set = Array
		(
			'change_pass_check_old' => Array('pass_old', 'pass_new', 'pass_new_repeat'),
			'update_pass' => Array('pass'),
			'change_pass' => Array('pass_new'),
			'insert' => Array('username', 'first_name', 'second_name', 'unique_email', 'email', 'pass_new', 'pass_new_repat', 'phone'),
			'update' => Array('first_name', 'second_name', 'phone', 'email', 'username'),
		);

		$this->validators = Array();

		foreach ($validators_set[$set] as $key)
			$this->validators[$key] = $validators_def[$key];
	}

	function validate() 
	{
		if (!parent::validate())
			return false;

		if (isset($this->validators['license']))
			if (!$_REQUEST['license'])
				$this->errors['license'] = '/USER/LICENSE';

		if (isset($this->validators['pass_old']))
			if (!$this->checkPass($this->get('pass_old')))
				$this->errors['pass_old'] = '/USER/PASS_OLD';

		if (isset($this->validators['pass_new']) && $this->get('pass_new'))
			if (mb_strlen($this->get('pass_new')) < $this->min_pass_length)
				$this->errors['pass_new'] = '/USER/PASS_TOO_SHORT';

		if (isset($this->validators['pass_new_repeat']))
			if ($this->get('pass_new') != $this->get('pass_new_repeat'))
				$this->errors['pass_new_repeat'] = '/USER/PASS_REPEAT';

		if (isset($this->validators['unique_username']))
			if ($this->count(Array('email=? AND !removed', $this->get('email'))))
				$this->errors['email'] = '/USER/EMAIL_TAKEN';

		if (isset($this->validators['unique_email']))
			if ($this->count(Array('email=? AND !removed', $this->get('email'))))
				$this->errors['email'] = '/USER/EMAIL_TAKEN';

		return $this->errors ? false : true;
	}

	function logLogin() 
	{
		$inf = GW_Request_Helper::visitorInfo();
		$msg = "ip: $inf[ip]" . ($inf['proxy'] ? " | $inf[proxy]" : '') . ($inf['referer'] ? " | $inf[referer]" : '');
		GW_DB_Logger::msg($msg, 'user', 'login', $user['id'], $inf['browser']);
	}

	function onLogin() 
	{
		$this->set('login_time', date('Y-m-d H:i:s'));
		$this->set('login_count', $this->get('login_count') + 1);
		$this->set('last_ip', $_SERVER['REMOTE_ADDR']);

		$this->onRequest();

		$this->update(Array('login_time', 'login_count', 'last_ip', 'last_request_time'));
		$this->logLogin();
	}

	function onRequest($db_update = true) 
	{
		$_SESSION[AUTH_SESSION_KEY]['last_request'] = time();
		$this->set('last_request_time', date('Y-m-d H:i:s'));

		if ($db_update)
			$this->update(Array('last_request_time'));
	}

	static function cryptPass($pass, $salt = null) 
	{
		if ($pass) {//cant be empty
			return $salt ? crypt($pass, $salt) : crypt($pass);
		} else
			die('Password cant be empty');
	}

	function checkPass($pass) 
	{
		if (!$pass)
			return false;

		$tmp = $this->get('pass');

		return $tmp == self::cryptPass($pass, $tmp);
	}

	function cryptPassword() 
	{
		$this->set('pass', self::cryptPass($this->get('pass')));
	}

	function eventHandler($event) 
	{
		switch ($event) {
			case 'BEFORE_SAVE':
				if (isset($this->content_base['pass_new']))
					$this->set('pass', self::cryptPass($this->get('pass_new')));

				break;
		}

		parent::EventHandler($event);
	}

	function isRoot() 
	{
		return GW_ADM_Permissions::isRoot($this->group_ids);
	}

	/*
	function delete() 
	{
		$this->fireEvent('BEFORE_DELETE');
		$this->set('removed', 1);
		$this->set('active', 0);
		$this->update(Array('removed', 'active'));

		$this->fireEvent('AFTER_DELETE');
	}
	*/
	
	function getById($id) 
	{
		return $this->find(Array('id=?', $id));
	}

	function getByUsername($username) 
	{
		return $this->find(Array('email=?', $username));
	}

	function getForActivationById($id) 
	{
		return $this->find(Array('id=? AND ! banned AND ! removed', $id));
	}

	function getByUsernamePass($username, $pass) 
	{
		$user = $this->getByUsername($username);
		if ($user && $user->checkPass($pass))
			return $user;
	}

	function generateKey() 
	{
		$this->key = md5(time() . rand(0, 1000) . $this->name);
	}

	function calculateField($key) {
		$cache = & $this->cache['calcf'];

		if (isset($cache[$key]))
			return $cache[$key];

		switch ($key) {
			case 'title':
				$val = $this->get('email');
				break;
			case 'name':
				$val = $this->get('first_name').' '.$this->get('second_name');
				break;
		}

		return $cache[$key] = $val;
	}

	function onLogout() 
	{
		//dump("Logging out");
		//exit;
	}

	function isSessionNotExpired() 
	{
		$tmp = $this->remainingSessionTime();
		return $tmp > -2;
	}

	/**
	 * returns seconds
	 */
	function remainingSessionTime() 
	{
		$session_validity = (int) $this->get('session_validity');

		if ($session_validity == -1 || $this->autologgedin)
			return -1;

		//$last_request = strtotime($this->get('last_request_time'))
		$last_request = $_SESSION[AUTH_SESSION_KEY]['last_request'];

		return $last_request - strtotime("-$session_validity minute");
	}

}
