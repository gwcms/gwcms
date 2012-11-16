<?php

class GW_Error_Message
{
	static $cache;
	static $ln;
	static $langf_dir;
	
	
	function getLangFile($file_id)
	{
		return (self::$langf_dir ? self::$langf_dir : GW::$dir['ADMIN'].'lang/') ."{$file_id}_errors.lang.xml";
	}
	
	
	function loadFile($file_id)
	{
		//$file_id=strtolower($file_id);
		
		if(isset(self::$cache[$file_id]))
			return true;
		
		$filename=self::getLangFile($file_id);

		if(!is_file($filename))
		{
			trigger_error('lang file "'.$filename.'" do not exist',E_USER_NOTICE);
			return false;
		}
		
		self::$cache[$file_id] = GW_Lang_XML::load($filename, self::$ln);
		
		return true;
	}
	
	function getFromCache($file_id, $path)
	{
		$var =& self::$cache[$file_id]; 
		
		foreach($path as $key)
		{
			if(!is_array($var) || !isset($var[$key]))
				return false;
							
			$var =& $var[$key];
		}

		return $var;
	}
		
	
	/**
	 * Code example: /ERROR_FILE/PATH/TO/TEXT
	 * Note: code should start with "/"
	 * 
	 * file stored in:
	 * ADMIN_DIR/lang/strtolower(ERROR_FILE)_error.lang.Error_Message::$ln.xml
	 */
	function read($key)
	{	
			if($key[0]!='/')
				return $key;
							
			list(,$file_id,$path) = explode('/',$key,3);
			
			$file_id=strtolower($file_id);
			
			if(!self::loadFile($file_id))
				return $key;
				
			$tmp=self::getFromCache($file_id, explode('/',$path));
			
			return $tmp ? $tmp : $key;
	}
	
}
