<?php


class ttlock_api 
{
	private $apikey;
	public $last_request_header;
	public $last_request_body;
	
	private $accesstoken;
	private $clientid;
	
	use Singleton;
	
	
	
	function initConfig()
	{
		$this->config = new GW_Config('itax/');		
		$this->db = GW::db();
	}
	
	
	
	function addPasscode($lockid, $passcode,$startdate,$enddate)
	{

		if($lockid===false)
			$lockid = $this->cfg->default_lock_id;
		
		
		
		$resp = $this->request("v3/keyboardPwd/add",[
		    "lockId"=> $lockid,
			"keyboardPwd"=>$passcode,
			"keyboardPwdName"=>'voro-api',
			"startDate"=> $startdate*1000,
			"endDate"=> $enddate*1000,
			"addType"=> "2"
		],['date'=>1]);
		

		return $resp;			
	}
	
	function addPasscodeRandom($lockid, &$passcode,$startdate,$enddate)
	{
		$passcode = (string)$passcode;
		$repeat=0;
		
		while(1){
			$resp = $this->addPasscode($lockid, $passcode,$startdate,$enddate);
			
			$repeat++;
			
			
			if(isset($resp->errcode) && $resp->errcode==-3007){
				//The same passcode already exists. Please use another one.
				$passcode = GW_String_Helper::getRandString(strlen($passcode), $chars='0123456789');
			}else{
				$resp->repeat = 1;
				return $resp;
			}
		}
	}
	
	function addRandomPasscodeStore($start,$end,$ref){
		
		$code = rand(10000,99999);
		//po to kai gautu errora pasitikrintu ir dar karta sugeneruotu atsitiktini, pasikeitimas perduodamas per $code argumenta
		$resp = ttlock_api::singleton()->init()->addPasscodeRandom(false,$code,$start,$end);
		
		
		$code = gw_ttlock_codes::singleton()->createNewObject([
		    'code'=>$code,
		    'remote_id'=>$resp->keyboardPwdId,
		    'start'=>date('Y-m-d H:i:s',$start), 
		    'end'=>date('Y-m-d H:i:s',$end),
		    'ref'=>$ref
		]);
		
		$code->insert();
		return $code;
	}
	
	
	function deletePasscode($lockid, $keyboardPwdId)
	{
		if($lockid===false)
			$lockid = $this->cfg->default_lock_id;
		
		$resp = $this->request("v3/keyboardPwd/delete",[
		    "lockId"=> $lockid,
			"keyboardPwdId"=>$keyboardPwdId,
			"deleteType"=> "2"
		],['date'=>1]);	
		
		return $resp;	
	}
	
	function unlock($lockid)
	{
		if($lockid===false)
			$lockid = $this->cfg->default_lock_id;
	
		$resp = $this->request("v3/lock/unlock",[
		    "lockId"=> $lockid,
		],['date'=>1]);	
		
		return $resp;	
	}	

	function lock($lockid)
	{
		if($lockid===false)
			$lockid = $this->cfg->default_lock_id;
		
		$resp = $this->request("v3/lock/lock",[
		    "lockId"=> $lockid,
		],['date'=>1]);	
		
		return $resp;	
	}	
	
	function listPasscode($lockid=false, $page=1)
	{
		if($lockid===false)
			$lockid = $this->cfg->default_lock_id;
		
		$resp = $this->request("v3/lock/listKeyboardPwd",[
		    "lockId"=> $lockid,
		    'pageSize'=>100,
		    'pageNo'=>$page
		],['date'=>1]);	
		
		return $resp;	
	}

	function listAllPasscode($lockid=false)
	{
		$resp = $this->listPasscode($lockid);
		$list = $resp->list;
		if($resp->pages > 1){
			for($i=2;$i<=$resp->pages;$i++){
				$resp = $this->listPasscode($lockid, $i);
				$list = array_merge($list, $resp->list);
			}
		}
		
		foreach($list as $idx => $item){
			$item->startDate = date('Y-m-d H:i',$item->startDate/1000);
			$item->endDate = date('Y-m-d H:i',$item->endDate/1000);
			$item->sendDate = date('Y-m-d H:i',$item->sendDate/1000);
				
		}		
		
		return $list;
	}	
	
	
	function getAccessToken($clientsecret, $username, $pass)
	{
		//https://api.ttlock.com/oauth2/token		
			
/*
client_id	Y		The app_id which is assigned by system when you create an application
client_secret	Y		The app_secret which is assigned by system when you create an application
username	Y		User account of TTLock APP, or the username returned by User registerAPI, do not use the developer account.
password	Y		Password(32 chars, low case, md5 encrypted)
 * 
 */		
		$url = "https://api.ttlock.com/oauth2/token";
		$url = "https://api.sciener.com/oauth2/token";
		
		
		$r=GW_Http_Agent::singleton();
		$resp = $r->postRequest($url, [
		    'client_id'=>$this->clientid,
		    'client_secret'=>$clientsecret,
		    'username'=>$username,
		    // //md5("Krabas64")
		    'password'=>$pass
		]);
		
		
		//return example:
		//{"access_token":"665cabac8f9d9dfcc9f59c3e3b49a817","uid":5522838,"refresh_token":"20688bcd3a40d585fd7fa3d3ac37fd6f","openid":1457107684,"scope":"user,key,room","token_type":"Bearer","expires_in":7776000}
		$resp = json_decode($resp);
		
		
		
		$this->accesstoken = $resp->access_token;
		
		
		return $resp;
	}
	
	function getLockList()
	{
		//https://api.ttlock.com/v3/lock/list

		
		$date = 	round(microtime(true) * 1000);
		
		$resp = $this->request("v3/lock/list",[
		    'pageNo'=>1,
		    'pageSize'=>10,
		    //787264a2f93a92ae1b6197d251384388 //md5("Krabas64")
		    'date'=>$date
		]);
		

		return $resp;		
	}
	
	function request($path,$addargs,$opts=[])
	{
		$r=GW_Http_Agent::singleton();
		$url="https://api.ttlock.com/$path";
		$url = "https://api.sciener.com/$path";
		
		//$url = "http://requestlog/v3/lock/list";
		$args=[
		    'clientId'=>$this->clientid,
		    'accessToken'=>$this->accesstoken
		]+$addargs;
		
		if(isset($opts['date'])){
			$date = round(microtime(true) * 1000);
			$args["date"] = $date;
		}
		    
		$resp = $r->postRequest($url, $args);	
		//$resp = file_get_contents($url.'?'. http_build_query($args));
		
		
		$resp = json_decode($resp);
		return $resp;		
	}
	
	
	function init()
	{
		if(!$this->accesstoken){
			$cfg = new GW_Config("system__ttlock/");	
			$cfg->preload('');
			$this->cfg = $cfg;
			
			$this->clientid = $cfg->client_id;
			
			$this->getAccessToken($cfg->clientsecret, $cfg->username, $cfg->pass);
		}
		
		return $this;
	}
	

}