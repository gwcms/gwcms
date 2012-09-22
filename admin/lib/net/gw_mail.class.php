<?php

class GW_Mail
{

	static function remoteMail($args)
	{
		$http = new GW_Http_Agent;		
				
		$t = new GW_Timer;
		//file_get_contents(GW::$static_conf['REMOTE_SERVICES']['MAIL1']);
		$body = $http->getContents(GW::$static_conf['REMOTE_SERVICES']['MAIL1'], Array(), $args);
		
		
		if(($r=(float)$t->stop(5)) > 6)
			 trigger_error("Request lasts very long ($r s)", E_USER_WARNING);
		
		if(strpos($body, 'S.E.N.T') !== false)
			return true;
			
		if(stripos($body, 'ERROR') !== false)
			trigger_error("Remote mail service returns error: ".$body, E_USER_ERROR);
			
	}

	static function simple($msg)
	{		
		if(!isset($msg['headers']))
			$msg['headers']='';
		
		if(isset($msg['from']))
			$msg['headers'] .= "From: $msg[from]\r\n";
			
		$success = mail($msg['to'], $msg['subject'], $msg['body'], $msg['headers']);
		
		if($success)
			return 1;
		
		$success = self::remoteMail(Array('function'=>'simple') + $msg);
			
		if($success)
			return 2;
			
		return 0;
	}
}


?>