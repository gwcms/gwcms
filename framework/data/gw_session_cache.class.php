<?php

class GW_Session_Cache
{

	static function get($key)
	{
		$var =& $_SESSION['GW_SESSION_CACHE'][$key];
				
		if($var[1] > time())
			return $var[0];
	} 
	
	static function set($key, $value, $expires='10 seconds')
	{
		$var =& $_SESSION['GW_SESSION_CACHE'][$key];
		
		$var=Array($value, strtotime($expires));
	}
}