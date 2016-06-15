<?php

class GW_Validation_Helper
{

	/**
	 * removes not expected keys
	 */
	static function removeUnexpected(&$arr, $expected_keys = Array())
	{
		foreach ($arr as $key => $val)
			if (!in_array($key, $expected_keys))
				unset($arr[$key]);
	}

	static function className($str)
	{
		$str = preg_replace('/^\d+/', '', $str);
		$str = preg_replace('/[^a-z0-9]$/', '', $str);
		return preg_replace('/[^a-z0-9-_\/]/i', '', $str);
	}

	static function classFileName($str)
	{
		return strtolower(self::className($str));
	}

	static function pagePathName($str)
	{
		$str = mb_strtolower($str);
		$current = Array('ą', 'č', 'ę', 'ė', 'į', 'š', 'ų', 'ū', 'ž');
		//$current = array_merge($current, array_map('mb_strtoupper',$current));

		$replace = Array('a', 'c', 'e', 'e', 'i', 's', 'u', 'u', 'z');
		//$replace = array_merge($replace, array_map('mb_strtoupper',$replace));	

		$str = str_replace($current, $replace, $str);
		$str = preg_replace('/[^a-z0-9_-]/i', '-', $str);

		return $str;
	}
}
