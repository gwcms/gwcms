<?php

//call me example: http://site.url.and.or.base.path/service/test/public/datetime
//call me example: http://site.url.and.or.base.path/service/test/public/echo?test=best&abc=123
// or class call
// $rpc = new GW_General_RPC('http://site.url.and.or.base.path/service/test')
// $rpc->call('public/echo',['test'=>'best','abc'=>123, $post_args]);

class GW_User_service extends GW_Common_Service
{

	public $username = 'aaa';
	public $pass = 'bbb';

	
	function init()
	{
		parent::init();
		
		list($this->username, $this->pass) = explode(':', GW_Config::singleton()->get('gw_users/userapi_userpass'));
	}
	
	function checkAuth()
	{
		if ($this->checkBasicHTTPAuth())
			return true;
	}
	
	

	
	function unsetUserAdminFields(&$user, $update=false)
	{
		foreach(['pass','admin_access','site_verif_key','site_passchange','removed','session_validity','parent_user_id','is_blocked'] as $fld)
			unset($user[$fld]);		
		
		//fields cant be updated
		if($update)
			foreach(['username','person_id','token'] as $fld)
				unset($user[$fld]);
	}

	function actLogin()
	{
		$username = $_POST['user'];
		$password = $_POST['pass'];
		$ip = $_POST['ip'] ?? false;
		$user_agent = $_POST['user_agent'] ?? false;
			
		
		if (!$user = GW_Customer::singleton()->getByUsernamePass($username, $password)){
			$resp['error_msgid'] = '/G/GENERAL/LOGIN_FAIL';
		}else{
			if ($user->is_blocked == 1){
				$resp['error_msgid'] = '/G/GENERAL/USER_BLOCKED';
			}elseif ($user->active == 0) {
				$resp['error_msgid'] = '/G/GENERAL/USER_NOT_ACTIVE';
			}else{
				//user is ok
		//		$user->setRandToken();
				$user->onLogin($ip, $user_agent);
				
				$resp['token']=$user->token;
			}
			
		}
		
		if(!isset($resp['error_msgid']))
		{
			$resp['user'] = $user->toArray();
			$this->unsetUserAdminFields($resp['user']);			
		}else{
			$resp['error']=1;
		}
		
		return $resp;
		
	}
	
	function actInfo()
	{
		if ($user = $this->__getUser($resp)){
			
			$resp['user'] = $user->toArray();
			$this->unsetUserAdminFields($resp['user']);
		}
		
		return $resp;
	}
	
	function __getUser(&$resp)
	{
		$userid = $_POST['userid'];
		$token = $_POST['token'];
		
		if(!($user = GW_Customer::singleton()->getByToken($userid, $token))){
			$resp['error_msgid'] = '/G/GENERAL/TOKEN_FAIL';
			$resp['error']=1;
		}
		
		return $user;
	}
	
	function actUpdate()
	{
		$vals = $_POST['user'];
		$resp = [];
		//return [$userid, $token, GW_Customer::singleton()->getByToken($userid, $token)];
		
		
		if ($user = $this->__getUser($resp))
		{
			$this->unsetUserAdminFields($vals);
			
			unset($vals['id']);
			$user->setValues($vals);
			$user->prepareSave();
			
			$resp['vals']=$vals;
			
			$resp['changed_fields']=array_keys($user->changed_fields);
			
			
			
			if(!$user->validate())
			{
				$resp['errors'] = $user->errors;
				$resp['updateuser']='FAIL';
			}else{
				$user->updateChanged();
				$resp['updateuser']='OK';
			}
			
			
			
		}	
		return $resp;
	}
	
	
	function actRegister()
	{
		$vals = $_POST['user'];
		$this->unsetUserAdminFields($vals);	
		
		$user = GW_Customer::singleton()->createNewObject($vals);
		$user->setValidators('register');
		$user->prepareSave();
		
		$resp = [];
		
		if(!$user->validate())
		{
			$resp['errors'] = $user->errors;
			$resp['register']='FAIL';
		}else{
			$resp['user_id']=$user->insert();
			
			$user->setRandToken();
		
			$resp['token']=$user->token;
			$resp['register']='OK';
		}
		
		return $resp;
	}
	
	function actLogout()
	{
		
		$resp=[];
		
		if($user = $this->__getUser($resp))
		{
			$user->saveValues(['token'=>'-']);
			
			$resp['logout']='OK';
		}

		
		return $resp;		
	}



	function actTestCall()
	{

		$rpc->debug = true;

		$response = $rpc->sysUserCall('sysinfo');
		$response->meta = $rpc->debug_data;

		return (array) $response;
	}
}
