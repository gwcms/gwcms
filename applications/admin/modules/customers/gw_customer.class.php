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
	    'license'=>1, // perskaiciuojamas
	    'group_ids_cached'=>1,
	    'ext'=>1,
	    'parent_user'=>1,
	    'short_title'=>1,
	];
	
	public $composite_map_ext = [
	];
	
	public $ignore_fields = ['pass_old' => 1, 'pass_new' => 1, 'pass_new_repeat' => 1];
	public $autologgedin = false;
	
	
	public $ownerkey = 'customers/users';
	public $extensions = [
	    'keyval'=>1,
	    'changetrack'=>1
	];	
	
	public $ignored_change_track = ['update_time'=>1];
	
	

	function setValidators($set, $single=false) 
	{
		if ($set===null)
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
			'country' => ['gw_string', ['required' => 1]],
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
			'register' => ['name', 'surname', 'unique_email', 'email', 'pass_new', 'pass_new_repeat', 'phone','agreetc','country'],
			'profile' => ['name', 'surname', 'phone','agreetc','country', 'city', 'birthdate','gender'],
			'update' =>  [],//['name', 'surname', 'phone', 'email', 'username'],
			'insert' => ['name', 'surname', 'phone', 'email', 'username'],
			'quick_insert' => ['name', 'surname', 'country','birthdate', 'gender'],
			'quick_insert_foreigner' => ['name', 'surname', 'country','gender'],		    
			'register_fb' => ['name', 'surname', 'unique_username'],		    
		);

		$this->validators = [];

		if(isset($validators_set[$set]))
			foreach ($validators_set[$set] as $key)
				$this->validators[$key] = $validators_def[$key];
		
		if($single)
			$this->validators[$single] = $validators_def[$single];
			
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
				//$this->calculate_fields += $this->calculate_fields_ext;
				
				//d::dumpas('test');
			break;
			case 'BEFORE_SAVE':
				
				if(!$this->username && $this->email)
					$this->username = $this->email;
				
				if($this->birthdate){
					$birthyear = explode('-',$this->birthdate)[0];
					$this->age = date('Y')-$birthyear;
				}else{
					$this->age = 0;
				}
				
				//auto susiejimas su amziaus grupe
				
			break;
				
		}

		parent::EventHandler($event, $context_data);
	}


	
	function realDelete() 
	{
		return GW_Data_Object::delete();

	}
	
	
	
	
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
			case 'parent_user':
				return GW_User::singleton()->find(['id=?',$this->parent_user_id]);
			break;
			case 'short_title':
				return mb_substr($this->name, 0,1).'. '.mb_substr($this->surname, 0,10);
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
				return $this->country=='LT' ? GW::s('PROJECT_SHORTNAME')."-".sprintf("%04s",$this->lic_id) : '';
			break;
			case 'license':
				return $this->lic_id2;
			break;		
			//case 'age_group_title':
			//	return $this->ageGroupObj->title;
			
				
		}
		return parent::calculateField($key);
	}


	//max vienas pass change per diena - apsaugot vartotojus nuo uzdooldinimo
	function passChangeExpired()
	{
		$passchangeset = substr($this->site_passchange, 0, 8);
	
		return date('Ymd') != $passchangeset;
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
	
	

	



}
