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
		$x = & $arr;

		if (is_string($key_arr))
			$key_arr = self::strKeyToArrKey($key_arr);

		foreach ($key_arr as $key) {
			if (!isset($x[$key]))
				return false;
			else
				$x = & $x[$key];
		}

		return $x;
	}

	/**
	 * get value from array1 if is empty - array2
	 */
	static function altValue($key_arr, &$arr1, &$arr2)
	{
		if ($tmp = self::valByArrKey($arr1, $key_arr))
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

		while (count($stack) > 0) {
			list($prefix, $array) = array_pop($stack);

			foreach ($array as $key => $value) {
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
		foreach ($keys as $key)
			if (isset($source[$key]))
				$destination[$key] = $source[$key];
	}

	static function objectCopy($source, &$destination, $keys)
	{
		foreach ($keys as $key)
			$destination->$key = $source->$key;
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

		foreach ($names as $i => $key)
			$new[$key] = $values[$i];

		return $new;
	}

	static function arrArr2ArrObj($arr)
	{
		$tmp = [];
		foreach ($arr as $key => $itm)
			$arr[$key] = (object) $itm;

		return $arr;
	}
	
	static function sortByField($field, &$array)
	{
		uasort($array, function($a, $b) use ($field)
		{
		    if ($a[$field] == $b[$field]){
			return 0;
		    }else if ($a[$field] > $b[$field]){
			return -1;
		    }else {             
			return 1;
		    }
		});

		return true;
	}


	/**
	 * you give var, key array, and you get pointer to 
	 * 
	 * set
	 * GW_Array_Helper::getPointer2XlevelAssocArr($x, [a,b,c], 123);
	 * or
	 * $p =& GW_Array_Helper::getPointer2XlevelAssocArr($vals, explode('/', $id));
	 * $p = 123;
	 * 
	 * --after--
	 * print_r($x): [a=>[b=>[c=>123]]]
	 * 
	 * get
	 * echo GW_Array_Helper::getPointer2XlevelAssocArr($x, [a,b,c]);
	 * : 123
	 */
	static function &getPointer2XlevelAssocArr(&$var, $keys, $value=null)
	{

		foreach ($keys as $part)
			$var = & $var[$part];

		if ($value !== Null)
			$var = $value;

		return $var;
	}	
	
	/**
	 * transform to multilevel array
	 */
	static function restruct2MultilevelArray(&$vals, $separator='/')
	{
		foreach($vals as $id => $data)
		{
			if(strpos($id, $separator)!==false){
				
				$p =& GW_Array_Helper::getPointer2XlevelAssocArr($vals, explode($separator, $id));
				$p = $data;
				
				unset($vals[$id]);
			}
		}		
	}	
	
	
	/**
	 * @param array $arr1
	 * @param array $arr2
	 * @param string $separator - if keys posibly have / character use anoter for this operation
	 * @return array
	 */
	static function arrayTreeMerge($arr1, $arr2, $separator='/')
	{
		$arr1flat = self::arrayFlattenSep($separator, $arr1);
		$arr2flat = self::arrayFlattenSep($separator, $arr2);
		
		
		$merge = array_merge($arr1flat, $arr2flat);
		
		
		
		self::restruct2MultilevelArray($merge, $separator);
		
		return $merge;
	}
	
}
