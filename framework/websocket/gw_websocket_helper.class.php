<?php

class GW_WebSocket_Helper 
{
	static $client=false;
	static $cfg_cache = false;
	
	static function initControlUserWsc($connect=true)
	{
		if(self::$client)
			return self::$client;
		
		$cfg = self::loadCfg();
		
		//$wss = GW::s('WSS');
		
		list($user, $pass) = explode(':', $cfg->WSS_CONTROL_USER_PASS);
		//$user = $wss['CONTROL_USER'];
		//$pass = $wss['CONTROL_USER_PASS'];
		
		list($host, $port) = explode(':', $cfg->WSS_HOST_PORT);


		$client = new WebSocket\Client($uri="wss://$user:$pass@$host:$port/irc");
		$client->messages_enabled = false;
			
		
		if($client->__fastConnect([]))
			return false;
		
		
		//$client->ping();
		self::$client = $client;
		return $client;
	}	
	
	function createUser()
	{
		
	}
	
	static function loadCfg()
	{
		if(!self::$cfg_cache){
			$cfg = new GW_Config('sys/');
			$cfg->preload('WSS_');	
			self::$cfg_cache = $cfg;	
		}
				
		return self::$cfg_cache;
	}	
		
	
	
	static function getFrontConfig(GW_User $user, $updateTempPass=false)
	{
		$cfg = self::loadCfg();
		$prefix = $cfg->WSS_CONTROL_USER_PREFIX;
		
		$username = $prefix.$user->username;
		list($pass,$updtime) = explode('||',$user->get('keyval/wss_pass'));
		
		$time = time()-(int)$updtime;
		
		if($time > 3600)
			$updateTempPass = true;
		
		$newuser = false;
		$client = false;
				
		if(!$pass)
		{
			$t = new GW_Timer;
			$client = self::initControlUserWsc();
			
			
			
			if(!$client){
				GW_Message::singleton()->message(['to'=>9,'subject'=>"Websocket server fail",'message'=>"Server not reachable",'level'=>15]);
				return false;
			}
				
			
			$pass = GW_String_Helper::getRandString(30);
						
			
			//patikrinti ar pavyko sukurti
			$response = $client->createUser($username, $pass);
			
			
			
			$newuser = true;
						
		}
		
		if($updateTempPass || $newuser)
		{
			if(!$client)
				$client = self::initControlUserWsc();
			
			if($client){
				$client->setTempPass($pass=GW_String_Helper::getRandString(50), '2 hour', $username);

				$user->set('keyval/wss_pass', $pass.'||'.time());
			}else{
				// 'nera rysioooo!!'
			}
		}
		$cfg = self::loadCfg();
	
		list($host, $port) = explode(':', $cfg->WSS_HOST_PORT);		
		
		return ['host'=>$host, 'port'=>$port, 'user'=>$username, 'apikey'=>$pass];
		
	}
	
	//perduodama json uzkoduota zinute su parametrais title,text
	//i message gali buti paduodama tiek masyvas su nurodytais parametrais tiek paprastas tekstas
	//kuris bus paverciamas i masyva
	
	static function notifyUser($username, $message)
	{
		$control = self::initControlUserWsc();
		
		$cfg = self::loadCfg();
		$prefix = $cfg->WSS_CONTROL_USER_PREFIX;
		
		$username = $prefix.$username;
		
		if(!is_array($message))
			$message = ['text'=>$message];
		
		if($control){
			$control->messagePrivate($username, json_encode($message));	
		}else{
			throw new Exception("control user wsc fail", E_USER_ERROR); 
		}
		
	}
}
