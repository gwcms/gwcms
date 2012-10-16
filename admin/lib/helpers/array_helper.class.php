<?php

class Array_Helper
{
	/**
	 * access value in array by key array
	 * exmpl: $x[a][b][c] = array_key($x, [a,b,c])
	 * @return mixed
	 */
	function valByArrKey($arr, $key_arr)
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
	function altValue($key_arr, &$arr1, &$arr2)
	{
		if($tmp = self::valByArrKey($arr1, $key_arr))
			return $tmp;
			
		return self::valByArrKey($arr2, $key_arr);
	}
	
	function strKeyToArrKey($str)
	{
		return explode('/', $str);
	}
	
	
	function arrayFlattenSep($sep, $array)
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

	/**
	 * Build associative array
	 * $names=Array('a','b')
	 * $values=Array('test','best');
	 * 
	 * result = Array('a'=>'test','b'=>'best')
	 */
	function buildAssociative($names, $values)
	{
		$new = Array();
		
		foreach($names as $i => $key)
			$new[$key]=$values[$i];
			
		return $new;
	}


}