<?php

class GW_WebSocket_Helper 
{
	static $client=false;
	
	static function initControlUserWsc($connect=true)
	{
		if(self::$client)
			return self::$client;
		
		$wss = GW::s('WSS');
		$user = $wss['CONTROL_USER'];
		$pass = $wss['CONTROL_USER_PASS'];
		$host = $wss['HOST'];
		$port = $wss['PORT'];

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
	
	static function getFrontConfig(GW_User $user, $updateTempPass=false)
	{
		$username = GW::s('WSS/USER_PREFIX').$user->username;
		$pass_info = $user->get('ext')->get('wss_pass', false, true);
		
		$pass = $pass_info ? $pass_info['value'] : false;
		$time = $pass_info ? time()-strtotime($pass_info['update_time']) : false;
		
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

				$user->get('ext')->replace('wss_pass', $pass);
			}else{
				// 'nera rysioooo!!'
			}
		}
		
		return ['host'=>GW::s('WSS/HOST'), 'port'=>GW::s('WSS/PORT'), 'user'=>$username, 'apikey'=>$pass];
		
	}
	
	//perduodama json uzkoduota zinute su parametrais title,text
	//i message gali buti paduodama tiek masyvas su nurodytais parametrais tiek paprastas tekstas
	//kuris bus paverciamas i masyva
	
	function notifyUser($username, $message)
	{
		$control = self::initControlUserWsc();
		
		$username = GW::s('WSS/USER_PREFIX').$username;
		
		if(!is_array($message))
			$message = ['text'=>$message];
		
		$control->messagePrivate($username, json_encode($message));		
	}
}
