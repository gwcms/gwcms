<?php

class GW_User extends GW_Composite_Data_Object
{

	public $table = 'gw_users';
	public $min_pass_length = 4;
	public $max_pass_length = 200;
	public $validators = Array();
	public $calculate_fields = Array('group_ids' => 1, 'title' => 1, 'api_key' => 1, 'online' => 1);
	public $ignore_fields = Array('pass_old' => 1, 'pass_new' => 1, 'pass_new_repeat' => 1);
	public $encode_fields = Array('info' => 'serialize');
	public $composite_map = [
		'group_ids' => ['gw_links', ['table' => 'gw_link_user_groups']],
		'image' => ['gw_image', ['dimensions_resize' => '800x600', 'dimensions_min' => '100x100']],
	];
	public $autologgedin = false;
	public $validators_def;
	public $validators_set;

	function loadValidators()
	{
		$this->validators_def = Array(
			'username' => Array('gw_string', Array('min_length' => 3, 'max_length' => 20, 'required' => 1)),
			'email' => Array('gw_email', Array('required' => 1)),
			'pass_old' => 1,
			'pass_new' => Array('gw_string', Array('min_length' => 6, 'max_length' => 200)),
			'pass_new_repeat' => 1,
			'unique_username' => 1,
		);

		$this->validators_set = Array
			(
			'change_pass_check_old' => Array('pass_old', 'pass_new', 'pass_new_repeat'),
			'change_pass' => Array('pass_new'),
			'insert' => Array('username', 'unique_username', 'email', 'pass_new'),
			'update' => Array('username', 'email', 'pass_new')
		);
	}

	function setValidators($set)
	{
		if (!$set)
			return $this->validators = Array(); //remove validators

		$this->loadValidators();

		$this->validators = Array();

		foreach ($this->validators_set[$set] as $key)
			$this->validators[$key] = $this->validators_def[$key];


		if ($set == 'insert')
			$this->validators['pass_new'][1]['required'] = 1;
	}

	function validate()
	{
		parent::validate();

		if (isset($this->validators['pass_old']))
			if (!$this->checkPass($this->get('pass_old')))
				$this->errors['pass_old'] = '/G/USER/PASS_OLD';

		if (isset($this->validators['pass_new_repeat']))
			if ($this->get('pass_new') != $this->get('pass_new_repeat'))
				$this->errors['pass_new_repeat'] = '/G/USER/PASS_REPEAT';

		if (isset($this->validators['unique_username']))
			if ($this->count(Array('username=? AND removed!=0', $this->get('username'))))
				$this->errors['username'] = '/G/USER/USERNAME_TAKEN';

		return $this->errors ? false : true;
	}

	function onLogin($ip, $user_agent)
	{
		$this->set('login_time', date('Y-m-d H:i:s'));
		$this->set('login_count', $this->get('login_count') + 1);
		
		
		if($this->last_ip!=$ip || $this->last_user_agent!=$user_agent)
		{
			GW_User_Ip_Log::singleton()->createNewObject(['user_id'=>$this->id, 'ip'=>$ip, 'user_agent'=>$user_agent])->insert();
		}
			
		$this->set('last_ip', $ip);
		$this->set('last_user_agent', $user_agent);

		$this->onRequest();

		$this->update(Array('login_time', 'login_count', 'last_ip', 'last_request_time', 'last_user_agent'));
		
		
	}

	function onRequest($db_update = true)
	{

		$this->set('last_request_time', date('Y-m-d H:i:s'));

		if ($db_update){
			$this->auto_fields = false;
			$this->update(['last_request_time']);
			$this->auto_fields = true;
		}
	}

	function canAccess($key)
	{
		$has_keys = ';' . $this->get('rights') . ';';
		return (strpos($has_keys, ";$key;") !== false) || (strpos($has_keys, ';su;') !== false);
	}

	function cryptPass($pass, $salt = null)
	{
		if ($pass) {//cant be empty
			return $salt ? crypt($pass, $salt) : crypt($pass, 'salt');
		} else {
			//d::dumpas('Password cant be empty');
			die('Password cant be empty');
		}
	}

	function checkPass($pass)
	{
		if (!$pass)
			return false;

		$tmp = $this->get('pass');

		return $tmp == $this->cryptPass($pass, $tmp);
	}

	function eventHandler($event, &$context_data = [])
	{
		switch ($event) {
			case 'PREPARE_SAVE':
				if (isset($this->content_base['pass_new']) && $this->content_base['pass_new'])
					$this->set('pass', $this->cryptPass($this->get('pass_new')));

				break;
		}

		parent::eventHandler($event, $context_data);
	}

	function isRoot()
	{
		return $this->id == GW_USER_SYSTEM_ID || GW_Permissions::isRoot($this->group_ids);
	}

	function delete()
	{
		$this->fireEvent('BEFORE_DELETE');
		$this->set('removed', 1);
		$this->set('active', 0);
		$this->update(Array('removed', 'active'));

		$this->fireEvent('AFTER_DELETE');
	}
	
	function forceDelete()
	{
		return parent::delete();
	}

	function isParent($parent_id)
	{
		if ($parent_id < 1)
			return false;

		if ($this->get('user_id') == $parent_id)
			return true;

		if (!$parent = $this->find(Array('id=?', $this->get('user_id'))))
			return false;

		return $parent->isParent($parent_id);
	}

	/**
	 * can user view,edit,this item
	 * @param GW_User
	 */
	function canBeAccessedByUser($user)
	{
		if ($user->isRoot())
			return true;

		if ($this->isRoot())
			return false;


		/*
		 * Su šiuo kodu be admin juzerio kiti negali turėti priėjimo prie users modulio
		 * taip pat vienas adminas gali redaguoti ir pereiti prie kito admino paskyros
		 * */
		return true;
	}

	function inGroup($group_id)
	{
		return in_array($group_id, $this->group_ids);
	}

	function getByUsername($username)
	{
		return $this->find(Array('username=? AND active=1 AND removed=0', $username));
	}

	function getByUsernamePass($username, $pass)
	{
		$user = $this->getByUsername($username);

		if ($user && $user->checkPass($pass))
			return $user;
	}

	function isSessionNotExpired($last_request=false)
	{
		if ($this->id == GW_USER_SYSTEM_ID)
			return true;

		$tmp = $this->remainingSessionTime($last_request);
		return $tmp > -2;
	}

	/**
	 * returns seconds
	 */
	function remainingSessionTime($last_request=false)
	{
		$session_validity = (int) $this->get('session_validity');
		
		if ($session_validity == -1 || $this->autologgedin)
			return -1;

		if($last_request===false)
			$last_request = strtotime($this->get('last_request_time'));
		
		return $last_request - strtotime("-$session_validity minute");
	}

	function calculateField($key)
	{
		switch ($key) {
			case 'title':
				return ($this->name || $this->surname ? $this->name . ' ' . $this->surname : $this->username);
			case 'api_key':
				return md5($this->get('password'));
			case 'online':
				return $this->last_request_time > date('Y-m-d H:i:s', strtotime('-10 minute'));
		}
	}

	/**
	 * 
	 * @return GW_User_Extended
	 */
	function getExt()
	{
		$cache = & $this->cache['gw_user_extended'];

		if (!$cache)
			$cache = new GW_User_Extended($this->id);

		return $cache;
	}

	/**
	 * Delete expired keys
	 */
	function __autologinExpired()
	{
		$how_old = str_replace('+', '-', GW::s('GW_AUTOLOGIN_EXPIRATION'));
		$this->getExt()->deleteOld('autologin', $how_old);
	}

	function getAutologinPass()
	{
		$pass = md5(rand(1, 99999999)) . md5($this->get('username'));

		$this->getExt()->insert("autologin", $pass);
		$this->__autologinExpired();

		return $pass;
	}

	function getUserByAutologinPass($username, $pass)
	{

		if (!($item = $this->getByUsername($username)))
			return false;


		if ($item->getExt()->exists("autologin", $pass)) {
			$item->getExt()->touch("autologin", $pass);
			return $item;
		}
	}

	function getUserByApiKey($username, $api_key)
	{
		$user = $this->find(Array('username=?', $username));


		if (!$user->api_key == $api_key)
			die("BAD API KEY");


		return $user;
	}

	function onLogout()
	{
		$info = $this->get('info');
		unset($info['autologin'][md5($_COOKIE['login_7'])]);

		$this->set('info', $info);
		$this->update(Array('info'));
	}

	function getOptions($active = true, $other_cond = '')
	{
		$cond = $active ? 'active!=0 AND removed=0' : '';

		$cond .= ($other_cond && $cond ? ' AND ' : '') . $other_cond;

		return $this->getAssoc(Array('id', 'username'), $cond);
	}
	
	function getOptionsTitle($active = true, $other_cond = '')
	{
		$cond = $active ? 'active!=0 AND removed=0' : '';

		$cond .= ($other_cond && $cond ? ' AND ' : '') . $other_cond;

		$list0 = $this->findAll($cond, ['select'=>'id, name, surname, email, username']);
		$list = [];
		
		foreach($list0 as $item)
			$list[$item->id] = $item->title;
		
		return $list;
	}		

	function countNewMessages()
	{
		return GW_Message::countStatic(Array('user_id=? AND seen=0', $this->id));
	}
	
	//allowed ips can be separated in comma
	//you should add allowed_ips varchar(255) to gw_user table
	function checkAllowedIp($ip)
	{
		return strpos($this->allowed_ips,$ip)!==false;
	}
	
	function getUserIdsByGroupId($group_id)
	{
		return $this->getDB()->fetch_one_column("SELECT id FROM gw_link_user_groups WHERE id1=".(int)$group_id);
	}	
	
	function getByGroupId($group_id)
	{
		$ids = $this->getUserIdsByGroupId($group_id);
		
		if(!$ids)
			return [];
		
		return $this->findAll(GW_DB::inCondition('id', $ids));
	}
	
}
