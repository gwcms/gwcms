<?php

class GW_Array_Helper
{
	/**
	 * access value in array by key array
	 * exmpl: $x[a][b][c] = array_key($x, [a,b,c])
	 * @return mixed
	 */
	static function valByArrKey($arr, $key_arr)
	{
		$x =& $arr;
		
		if(is_string($key_arr))
			$key_arr=self::strKeyToArrKey($key_arr);
		
		foreach($key_arr as $key)
		{	
			if(!isset($x[$key]))
				return false;
			else 
				$x =& $x[$key];
		}
				
		return $x;
	}
	
	/**
	 * get value from array1 if is empty - array2
	 */
	static function altValue($key_arr, &$arr1, &$arr2)
	{
		if($tmp = self::valByArrKey($arr1, $key_arr))
			return $tmp;
			
		return self::valByArrKey($arr2, $key_arr);
	}
	
	static function strKeyToArrKey($str)
	{
		return explode('/', $str);
	}
	
	
	static function arrayFlattenSep($sep, $array)
	{
		$result = array();
		$stack = array();
		array_push($stack, array("", $array));

		while (count($stack) > 0)
		{
			list($prefix, $array) = array_pop($stack);

			foreach ($array as $key => $value)
			{
				$new_key = $prefix . strval($key);
	
				if (is_array($value))
					array_push($stack, array($new_key . $sep, $value));
				else
					$result[$new_key] = $value;
			}
		}

		return $result;
	}

	/*
	 *  $source = Array('a'=>123,'b'=>'abc','c'=>'nothing')
	 *  $destination=Array();
	 *  Array_Helper::copy($source, $destination, Array('a','c')); 
	 *  $destination = Array('a'=>123,'c'=>'nothing')
	 * 
	 * */
	
	static function copy($source, &$destination, $keys)
	{
		foreach($keys as $key)
			$destination[$key]=$source[$key];
	}
	
	/**
	 * Build associative array
	 * $names=Array('a','b')
	 * $values=Array('test','best');
	 * 
	 * result = Array('a'=>'test','b'=>'best')
	 */
	static function buildAssociative($names, $values)
	{
		$new = Array();
		
		foreach($names as $i => $key)
			$new[$key]=$values[$i];
			
		return $new;
	}	


}