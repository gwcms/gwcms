<?php

class GW_Customer extends GW_User 
{


	public $min_pass_length = 6;
	public $max_pass_length = 16;
	public $validators = [
		'email' => 'gw_email',
	];
	public $calculate_fields = Array('title' => 1/* did project specified */);
	public $ignore_fields = Array('pass_old' => 1, 'pass_new' => 1, 'pass_new_repeat' => 1);
	public $autologgedin = false;
	

	function setValidators($set) 
	{
		if (!$set)
			return $this->validators = Array(); //remove validators

		$validators_def = Array(
			'username' => Array('gw_string', Array('min_length' => 2, 'max_length' => 120, 'required' => 1)),
			'name' => Array('gw_string', Array('required' => 1)),
			'surname' => Array('gw_string', Array('required' => 1)),
			'phone' => Array('gw_phone', Array('min_length' => 6, 'max_length' => 20, 'required' => 1)),
			'email' => Array('gw_email', Array('required' => 1)),
			'pass_old' => 1,
			'pass_new' => Array('gw_string', Array('min_length' => 6, 'max_length' => 120,'required' => 1)),
			'pass_new_repeat' => Array('gw_string', Array('min_length' => 6, 'max_length' => 120, 'required' => 1)),
			'unique_email' => 1,
			'unique_person_id' => 1,
			'license' => 1,
			'person_id' => ['gw_string', ['required'=>1]],
		);

		$validators_set = Array
		(
			'change_pass_check_old' => Array('pass_old', 'pass_new', 'pass_new_repeat'),
			'update_pass' => Array('pass'),
			'change_pass' => Array('pass_new'),
			'change_pass_repeat' => Array('pass_new','pass_new_repeat'),
			'register' => Array('name', 'surname', 'unique_email', 'email', 'pass_new', 'pass_new_repeat', 'phone', 'person_id'),
			'update' => Array('name', 'surname', 'phone', 'email', 'username'),
		);

		$this->validators = Array();

		foreach ($validators_set[$set] as $key)
			$this->validators[$key] = $validators_def[$key];
	}

	function validate() 
	{	
		
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

		if (isset($this->validators['unique_personid']) && $this->get('person_id'))
			if ($this->count(Array('person_id=? AND !removed', $this->get('person_id'))))
				$this->errors['person_id'] = '/USER/PERSONID_ALREADY_REGISTERED';

		if (isset($this->validators['unique_email']))
			if ($this->count(Array('email=? AND !removed', $this->get('email'))))
				$this->errors['email'] = '/USER/EMAIL_ALREADY_REGISTERED';
			
		
		

		return parent::validate();
	}






	function cryptPassword() 
	{
		$this->set('pass', self::cryptPass($this->get('pass')));
	}

	function eventHandler($event, &$context_data = []) 
	{
		switch ($event) {
			case 'BEFORE_SAVE':
				
				if(!$this->username && $this->email)
					$this->username = $this->email;

				break;
		}

		parent::EventHandler($event, $context_data);
	}

	function isRoot() 
	{
		return GW_Permissions::isRoot($this->group_ids);
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
		return $this->find(Array('username=? OR email=? OR person_id=?', $username,$username,$username));
	}

	function getForActivationById($id) 
	{
		return $this->find(Array('id=? AND removed=0', $id));
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
	
	
	function setPassChangeSecret()
	{
		$set="ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		$secret = date('Ymd').'000';
		
		for($i=0;$i<40;$i++)
			$secret.=$set[rand(0,34)];
		
			
		$this->saveValues(Array('passchange'=>$secret));
		
		
		
		return $secret;
	}
	
	
	
	function setRandToken()
	{
		$set="ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		$secret = date('YmdHi').'00';
		
		for($i=0;$i<16;$i++)
			$secret.=$set[rand(0,34)];
		
			
		$this->saveValues(Array('token'=>$secret));			
	}
	
	function checkIfExpiredToken($token, $validperiod='12 hour')
	{
		$expiretime  = date('YmdHi',strtotime('+'.$validperiod));
		
		return $token < $expiretime;
	}
	
	function getByToken($userid, $token)
	{
		$user = $this->find(['id=? AND token=? AND active=1 AND removed=0', $userid, $token]);

		return $user;		
	}

}
