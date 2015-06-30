<?php

class GW_String_Helper
{
	static $encoding = "UTF-8";
	
	function ucfirst($str, $lower_str_end = false) 
	{
		$first_letter = mb_strtoupper(mb_substr($str, 0, 1, self::$encoding), self::$encoding);
		
		if ($lower_str_end) {
			$str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, self::$encoding), self::$encoding), self::$encoding);
		} else {
			$str_end = mb_substr($str, 1, mb_strlen($str, self::$encoding), self::$encoding);
		}
		
		
		return $first_letter . $str_end;
	}

}