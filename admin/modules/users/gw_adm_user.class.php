<?
class GW_ADM_User extends GW_Composite_Data_Object
{
	var $table = 'gw_adm_users';
	var $min_pass_length=4;
	var $validators = Array();
	var $calculate_fields = Array('group_ids'=>1,'title'=>1, 'api_key'=>1);
	var $ignore_fields = Array('pass_old'=>1, 'pass_new'=>1, 'pass_new_repeat'=>1);
	var $encode_fields=Array('info'=>'serialize');	
	var $composite_map = Array
	(
		'link_groups' => Array('gw_links', Array('table'=>'gw_link_user_groups')),
	);
	var $autologgedin=false;
	
	
	function setValidators($set)
	{
		if(!$set)
			return $this->validators=Array(); //remove validators
		
		$validators_def = 
			Array(
				'username' => Array('gw_string', Array('min_length'=>3, 'max_length'=>20)),
				'email'=>'gw_email',
				'pass_old'=>1,
				'pass_new'=>1,
				'pass_new_repeat'=>1,
				'unique_username'=>1,
			);

		$validators_set = Array
		(
			'change_pass_check_old'=> Array('pass_old', 'pass_new', 'pass_new_repeat'),
			'change_pass' => Array('pass_new'),
			'insert' => Array('username', 'unique_username', 'email','pass_new'),
			'update' => Array('username', 'email', 'pass_new')
		);		
		
		$this->validators = Array();
		
		foreach($validators_set[$set] as $key)
			$this->validators[$key] = $validators_def[$key];
		
	}
	
	function validate()
	{
		if(!parent::validate())
			return false;
		
		if($this->validators['pass_old'])
			if(!$this->checkPass($this->get('pass_old')))
				$this->errors['pass_old']='/USER/PASS_OLD';
		
		if($this->validators['pass_new'] && $this->get('pass_new'))
			if(mb_strlen($this->get('pass_new')) < $this->min_pass_length)
				$this->errors['pass_new']='/USER/PASS_TOO_SHORT';	
		
		if($this->validators['pass_new_repeat'])
			if($this->get('pass_new')!=$this->get('pass_new_repeat'))
				$this->errors['pass_new_repeat']='/USER/PASS_REPEAT';	
		
		if($this->validators['unique_username'])
			if($this->count(Array('username=? AND !removed', $this->get('username'))))
				$this->errors['username']='/USER/USERNAME_TAKEN';		
				
		return $this->errors ? false : true;	
	}
	
	
	function logLogin()
	{
		$inf = GW_Request_Helper::visitorInfo();
		$msg="ip: $inf[ip]".($inf['proxy']?" | $inf[proxy]":'').($inf['referer']?" | $inf[referer]":'');
		GW_DB_Logger::msg($msg,'user','login',$user['id'],$inf['browser']);
	}

	function onLogin()
	{
		$this->set('login_time', date('Y-m-d H:i:s'));
		$this->set('login_count', $this->get('login_count')+1 );
		$this->set('last_ip', $_SERVER['REMOTE_ADDR']);

		$this->onRequest();
		
		$this->update(Array('login_time','login_count','last_ip','last_request_time'));
		$this->logLogin();
	}
	
	function onRequest($db_update=true)
	{
		$_SESSION[AUTH_SESSION_KEY]['last_request']=time();
		$this->set('last_request_time', date('Y-m-d H:i:s'));
		
		if($db_update)
			$this->update(Array('last_request_time'));		
	}

	function canAccess($key)
	{
		$has_keys = ';'.$this->get('rights').';';
		return (strpos($has_keys,";$key;")!==false) || (strpos($has_keys,';su;')!==false);
	}

	static function cryptPass($pass, $salt=null)
	{
		if($pass){//cant be empty
			return $salt ? crypt($pass, $salt) : crypt($pass);
		}else
			die('Password cant be empty');
	}
	
	function checkPass($pass)
	{		
		if(!$pass)
			return false;
			
		$tmp = $this->get('pass');
				
		return $tmp == self::cryptPass($pass,$tmp);
	}
	
	function EventHandler($event)
	{
		switch($event)
		{
			case 'BEFORE_SAVE':
				if($this->content_base['pass_new'])
					$this->set('pass', self::cryptPass($this->get('pass_new')));

			break;	
		}
		
		parent::EventHandler($event);
	}
	
	function isRoot()
	{
		return GW_ADM_Permissions::isRoot($this->group_ids);
	}
	
	function delete()
	{
		$this->fireEvent('BEFORE_DELETE');
		$this->set('removed',1);
		$this->set('active', 0);
		$this->update(Array('removed','active'));
		
		$this->fireEvent('AFTER_DELETE');
	}
	
	function isParent($parent_id)
	{
		if($parent_id<1)
			return false;
			
		if($this->get('user_id') == $parent_id)
			return true;
			
		if(! $parent = $this->find(Array('id=?',$this->get('user_id'))))
			return false;
		
		return $parent->isParent($parent_id);
	}
	
	/**
	 * can user view,edit,this item
	 * @param GW_ADM_User
	 */
	function canBeAccessedByUser($user)
	{
		if($user->isRoot())
			return true;
			
		if($this->isRoot())
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
		return $this->find(Array('username=? AND active',$username));
	}
	
	function getByUsernamePass($username, $pass)
	{
		$user = $this->getByUsername($username);
		if($user && $user->checkPass($pass))
			return $user;	
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
		$session_validity = (int)$this->get('session_validity');
		
		if($session_validity == -1 || $this->autologgedin)
			return -1;
		
		//$last_request = strtotime($this->get('last_request_time'))
		$last_request = $_SESSION[AUTH_SESSION_KEY]['last_request'];
			
		return $last_request - strtotime("-$session_validity minute");
	}

	function calculateField($key)
	{
		$cache =& $this->cache['calcf'];
		
		if(isset($cache[$key]))
			return $cache[$key];
		
		switch($key)
		{
			case 'title':
				$val=$this->get('username');
			break;
			case 'group_ids':
				if($lg=$this->get('link_groups'))
				{
					$val = $lg->getBinds();
					
					/////////////////////////////////////////////////////////////////
					///REIKIA TAISYTI COMPOSITE CONTENT BASE MODELI
					///////////////////////////////////////////////////////////////////
					unset($this->composite_content_base['link_groups']);
				}
			break;
			case 'api_key':
				$val=md5($this->get('password'));
			break;			
		}
		
		return $cache[$key]=$val;
	}
	
	/**
	 * Delete expired keys
	 */
	function __autologinExpired(&$autologin)
	{
		$current_time=date(GW_DB::$datetime_format);
		
		foreach((array)$autologin as $key => $expires)
			if($current_time > $expires)
				unset($autologin[$key]);
	}
	
	function getAutologinPass()
	{
		$pass=md5(rand(1,99999999)).md5($this->get('username'));
		
		$info=$this->get('info');
		
		self::__autologinExpired($info['autologin']);
		
		$info['autologin'][md5($pass)] = GW_DB::timeString(strtotime(GW::$static_conf['GW_AUTOLOGIN_EXPIRATION']));
		
		$this->set('info', $info);
		$this->update(Array('info'));
		
		return $pass;
	}
	
	function getUserByAutologinPass($username,$pass)
	{
		if(! $user=$this->getByUsername($username) )
			return false;
			
		$info=$user->get('info');
		$exp=$info['autologin'][md5($pass)];
		
		if($exp && strtotime($exp) > time() )
			return $user;
	}
	
	function getUserByApiKey($username, $api_key)
	{
		$user = $this->find(Array('username=?', $username));
		

		if(!$user->api_key == $api_key)
			die("BAD API KEY");
		
		
			return $user;
	}
	
	function onLogout()
	{
		$info=$this->get('info');
		unset($info['autologin'][md5($_COOKIE['login_7'])]);
		
		$this->set('info', $info);
		$this->update(Array('info'));
	}
	
	
	function getOptions($active=true)
	{
		$cond = $active ? 'active AND !removed' : '';
		
		return $this->getAssoc(Array('id','username'), $cond);
	}
}