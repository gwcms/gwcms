<?php


class GW_Request_Helper
{

	static function visitorInfo()
	{	
		static $info;
		
		if($info)
			return $info;
			
		
		if(!isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$IP=$_SERVER['REMOTE_ADDR'];
		}else{
			$PROXY_IP=$IP=$_SERVER['HTTP_X_FORWARDED_FOR'];
			$PROXYH=gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
			if($PROXYH==$PROXY_IP)$PROXYH='';
	
			$PROXY_HOST=$PROXYH;
			$PROXY=$_SERVER['REMOTE_ADDR'].($PROXYH?'/'.$PROXYH:'');
		}
	
		$HOST=@gethostbyaddr($IP);
	
		if($HOST==$IP)$HOST='';
		
		
		
		$info=Array();
		$info=Array
		(
			'ip'=>$IP,
			'host'=>$HOST,
			'proxy'=>$PROXY,
			'browser'=>$_SERVER['HTTP_USER_AGENT'],
			'referer'=>$_SERVER['HTTP_REFERER']
		);
	
		return $info;
	}
}