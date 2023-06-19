<?php

class GW_Customer extends GW_User 
{


	public $min_pass_length = 6;
	public $max_pass_length = 16;
	public $validators = [
		'email' => 'gw_email',
		
	];
	public $calculate_fields = [
	    'title'=>1,
	    'title_player_opt'=>1,
	    'licence_id'=>1,
	    'ext'=>1,
	    'approvedgroups'=>1,
	    'emailx'=>1,
	    'parent_user'=>1,	    
	];
	
	public $composite_map_ext = [
		'passportscan' => ['gw_image', ['dimensions_resize' => '1024x1024', 'dimensions_min' => '400x400']],
		'medicalpermit' => ['gw_image', ['dimensions_resize' => '1600x1600', 'dimensions_min' => '400x400']],
		//'coachObj' => ['gw_composite_linked', ['object'=>'LTF_Coaches','relation_field'=>'coach']],
		//'clubObj' => ['gw_composite_linked', ['object'=>'LTF_Clubs','relation_field'=>'club']],
	];
	
	public $ignore_fields = ['pass_old' => 1, 'pass_new' => 1, 'pass_new_repeat' => 1];
	public $autologgedin = false;
	
	
	

	function setValidators($set) 
	{
		if (!$set)
			return $this->validators = []; //remove validators

		$validators_def = [
			'username' => ['gw_string', ['min_length' => 2, 'max_length' => 120, 'required' => 1]],
			'name' => ['gw_string', ['required' => 1]],
			'surname' => ['gw_string', ['required' => 1]],
			'phone' => ['gw_phone', ['min_length' => 6, 'max_length' => 20, 'required' => 1]],
			'email' => ['gw_email', ['required' => 1]],
			'country' => ['gw_string', ['required' => 1]],
			'gender' => ['gw_string', ['required' => 1]],
			'birthdate' => ['gw_date', ['required' => 1]],
			'pass_old' => 1,
			'pass_new' => ['gw_string', ['min_length' => 6, 'max_length' => 120,'required' => 1]],
			'pass_new_repeat' => ['gw_string', ['min_length' => 6, 'max_length' => 120, 'required' => 1]],
			'unique_email' => 1,
			'unique_person_id' => 1,
			'agreetc' => ['gw_string', ['required' => 1]],
			'unique_username'=>1,		    
		];

		$validators_set = Array
		(
			'change_pass_check_old' => ['pass_old', 'pass_new', 'pass_new_repeat'],
			'update_pass' => ['pass'],
			'change_pass' => ['pass_new'],
			'change_pass_repeat' => ['pass_new','pass_new_repeat'],
			'birthdate'=> ['birthdate'],
			'register' => ['name', 'surname', 'unique_email', 'email', 'pass_new', 'pass_new_repeat', 'phone','agreetc'],
			'profile' => ['name', 'surname', 'phone','agreetc'],
			'update' => ['name', 'surname', 'phone', 'email', 'username'],
			'update_admin' => ['name','surname'],
			'insert' => ['name', 'surname', 'phone', 'email', 'username'],
			'register_fb' => ['name', 'surname', 'unique_username'],
			'addchild' => ['name', 'surname', 'email', 'phone'],
		);

		$this->validators = [];

		foreach ($validators_set[$set] as $key)
			$this->validators[$key] = $validators_def[$key];
	}

	function validate() 
	{	
		
		if (isset($this->validators['license']))
			if (!$this->content_base['license'])
				$this->errors['license'] = '/M/USER/LICENSE';

		if (isset($this->validators['pass_old']))
			if (!$this->checkPass($this->get('pass_old')))
				$this->errors['pass_old'] = '/M/USER/PASS_OLD';

		if (isset($this->validators['pass_new']) && $this->get('pass_new'))
			if (mb_strlen($this->get('pass_new')) < $this->min_pass_length)
				$this->errors['pass_new'] = '/M/USER/PASS_TOO_SHORT';

		if (isset($this->validators['pass_new_repeat']))
			if ($this->get('pass_new') != $this->get('pass_new_repeat'))
				$this->errors['pass_new_repeat'] = '/M/USER/PASS_REPEAT';

		if (isset($this->validators['unique_email']))
			if ($this->count(['email=? AND removed=0 AND id!=?', $this->get('email'), $this->id]))
				$this->errors['email'] = '/M/USER/EMAIL_ALREADY_REGISTERED';
				
		if (isset($this->validators['unique_username']))
			if ($this->count(['email=? AND removed=0', $this->get('email')]))
				$this->errors['email'] = '/M/USERS/ERRORS/EMAIL_TAKEN';			
		
	
		return parent::validate();
	}







	function eventHandler($event, &$context_data = []) 
	{
		switch ($event) {
			case 'AFTER_CONSTRUCT':
				$this->composite_map += $this->composite_map_ext;
				
				//d::dumpas('test');
			break;
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
		$this->update(['removed', 'active']);

		$this->fireEvent('AFTER_DELETE');
	}
	*/
	
	function getById($id) 
	{
		return $this->find(['id=?', $id]);
	}

	function getByUsername($username) 
	{
		return $this->find(['(username=? OR email=? OR person_id=?) AND active=1 AND removed=0', $username,$username,$username]);
	}

	function getForActivationById($id) 
	{
		return $this->find(['id=? AND removed=0', $id]);
	}

	function generateKey() 
	{
		$this->key = md5(time() . rand(0, 1000) . $this->name);
	}

	
	static function __genSecret($length)
	{
		$set="ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		$secret = date('Ymd').'000';
		
		for($i=0;$i<$length;$i++)
			$secret.=$set[rand(0,34)];
		
		return $secret;	
	}
			
	function setPassChangeSecret()
	{
		$secret = self::__genSecret(40);
		$this->saveValues(['site_passchange'=>$secret]);
		
		return $secret;
	}
		
	function setSignUpApprovalSecret()
	{
		$secret = self::__genSecret(40);
		$this->saveValues(['site_verif_key'=>$secret]);
		
		return $secret;
	}	
	
	
	function calculateField($key) {

		switch ($key) {
			case 'title':
				if($this->id)
					return $this->name.' '.$this->surname;
			break;
			case 'title_player_opt':
				return $this->title." / ".$this->country.' '.$this->licence_id;
			break;
			case 'name':
				return $this->get('first_name').' '.$this->get('second_name');
			break;
			case 'birthdate_year':
				list($y,$m,$d) = explode('-',$this->birthdate);
				return (int)$y;
			break;	
			case 'birthdate_month':
				list($y,$m,$d) = explode('-',$this->birthdate);
				return (int)$m;
			break;
			case 'birthdate_day':
				list($y,$m,$d) = explode('-',$this->birthdate);
				return (int)$d;
			break;
			case 'licence_id':
				return $this->country=='LT' ? "LTF-".sprintf("%04s",$this->lic_id) : '';
			break;
			case 'approvedgroups':
				return array_flip((array)json_decode($this->get("ext/approvedgroups"), true));
			break;
			case 'emailx':
				return !$this->email && $this->parent_user_id ? $this->parent_user->email : $this->email;
			break;
			case 'parent_user':
				return $this->find(['id=?', $this->get('parent_user_id')]);
			break;		
				
		}
		
		return parent::calculateField($key);
	}


	//max vienas pass change per diena - apsaugot vartotojus nuo uzdooldinimo
	function passChangeExpired()
	{
		$passchangeset = substr($this->site_passchange, 0, 8);
	
		return date('Ymd') != $passchangeset;
	}	
	
	
	function getActiveMembership($time=false)
	{
		if($this->country!='' && $this->country != "LT" )
		{
			return GW_Membership::singleton()->find(['user_id=0 AND active=1']);;
		}
		
		if($time==false)
			$time = date('Y-m-d H:i:s');
		
		$license = GW_Membership::singleton()->find(['user_id=? AND active=1 AND validfrom <= ? AND expires >= ?', $this->id, $time, $time]);
		
		return $license;
	}
	
	function getAge()
	{
		if(!$this->birthdate || $this->birthdate=='0000-00-00')
		{
			return false;
		}

		$date = new DateTime($this->birthdate);
		$now = new DateTime();
		$interval = $now->diff($date);
		return $interval->y;
	}
	
	
	function getCart($create=false)
	{
		$cartid = $this->get('ext/cart_id');
		if($cartid)
			$cart = GW_Order_Group::singleton()->find(['id=? AND payment_status!=7 AND open=1', $cartid]);
	
		
		
		if($create && (!isset($cart) || !$cart)){
			$cart = GW_Order_Group::singleton()->createNewObject(['user_id'=>$this->id]);
			$cart->open = 1;
			$cart->payment_status = 0;
			$cart->active = 1;
			$cart->insert();
			$this->set('ext/cart_id', $cart->id);
		}
		
		return $cart ?? false;
	}	
	
	
	
	function isGroupApproved($group)
	{
		//d::ldump([$this->approvedgroups, $group->id]);
		
		return isset($this->approvedgroups[$group->id]);
	}


}
