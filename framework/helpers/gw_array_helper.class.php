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
		if(is_object($source))
		{
			foreach ($keys as $key)
				if (isset($source->$key))
					$destination->$key = $source->$key;	
				
			return true;
		}
		
		foreach ($keys as $key)
			if (isset($source[$key]))
				$destination[$key] = $source[$key];
	}
	
	static function copyOut($source, $keys)
	{
		$dest = [];
		self::copy($source, $dest, $keys);
		return $dest;
	}
	
	static function objectCopy($source, &$destination, $keys)
	{
		foreach ($keys as $key)
			if(isset($source->$key))
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
		uasort($array, function($a, $b) use ($field) { return $a[$field] <=> $b[$field]; });

		return true;
	}
	
	static function objSortByField($field, &$array)
	{
		uasort($array, function($a, $b) use ($field) { return $a->$field <=> $b->$field; });

		return true;
	}
	
	static function objSortByFieldAlpabet($field, &$array, $lncode='lt_LT')
	{
		$c = new Collator($lncode);

		uasort($array,  function($a,$b) use ($field, $c)  {
			  return $c->compare($a->$field, $b->$field);
		     });		
		
		return true;
	}


	static function sortBySubitemsCount(&$array)
	{
		uasort($array, function($a, $b)
		{
		    if (count($a) == count($b)){
			return 0;
		    }else if (count($a) > count($b)){
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
	static function &getPointer2XlevelAssocArrNew(&$var, $keys, $value=null)
	{
		foreach ($keys as $part){
			if(isset($var[$part])){
				$var = & $var[$part];
			}else{
				$var = null;
			}
		}

		if ($value !== Null)
			$var = $value;

		return $var;
	}
	
	static function &getPointer2XlevelAssocArr(&$var, $keys, $value=null)
	{

		foreach ($keys as $part)
			$var = & $var[$part];

		if ($value !== Null)
			$var = $value;

		return $var;
	}
	
	/**
	 * groupArray groups array by given keys example:
	 * 
	 * $demoarr = [
	 *	['id'=>10, 'title'=>'pear', 'group'=>'eatable','group2'=>'fruits'], 
	 *	['id'=>15, 'title'=>'apple', 'group'=>'eatable','group2'=>'fruits'],
	 *	['id'=>20, 'title'=>'carrot', 'group'=>'eatable','group2'=>'vegets'] 
	 * ];
	 * groupArray([$demoarr , ['group', 'group2'])
	 * output:
	 * ['eatable'=> [
	 * 
	 *		'fruits'=> [
	 *			['id'=>10, 'title'=>'pear', 'group'=>'eatable','group2'=>'fruits'], 
	 *			['id'=>15, 'title'=>'apple', 'group'=>'eatable','group2'=>'fruits']]
	 *		],
	 *		'vegets'=> [
	 *			['id'=>20, 'title'=>'carrot', 'group'=>'eatable','group2'=>'vegets'] 
	 *		]
	 *  ]]
	 */
	static function groupArray(array $arr, array $keys)
	{
		$newlist = [];
		
		foreach($arr as $row){
			$keys1=[];
			foreach($keys as $key)
				$keys1[] = $row[$key];
			
			GW_Array_Helper::getPointer2XlevelAssocArr($newlist, $keys1, $row);
		}
		
		return $newlist;
	}
	
	static function groupObjects(array $arr, $key)
	{
		$groupedlist = [];
		
		foreach($arr as $itm)
			$groupedlist[$itm->$key][] = $itm;
		
		
		return $groupedlist;
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
	
	/**
	 * objExtractOneKey([ ['id'=>'10', 'title'=>'apple'], ['id'=>'15', 'title'=>'pear'] ], 'title') => ['apple','pear']
	 */
	static function objExtractOneKey($arr, $key)
	{
		$new = [];
		foreach($arr as $itm)
			$new[] = $itm->$key;
		
		return $new;
	}
	
	/**
	 * arrExtractOneKey([ ['id'=>'10', 'title'=>'apple'], ['id'=>'15', 'title'=>'pear'] ], 'title') => ['apple','pear']
	 */
	static function arrExtractOneKey($arr, $key)
	{
		$new = [];
		foreach($arr as $itm)
			$new[] = $itm[$key];
		
		return $new;
	}
	
	/**
	 * buildOpts([0=>'a', 1=>'b', 2=>'c']) => ['a'=>'a','b'=>'b','c'=>'c']
	 */
	static function buildOpts($array_values)
	{
		$new = [];
		foreach($array_values as $key)
			$new[$key] = $key;
		
		return $new;
	}
	
	
	static function isIndexesNumeric($arr)
	{
		foreach($arr as $key => $x)
			if(!is_numeric($key))
				return false;
			
		return true;
	}
	
	
	
	static function get1stIndex($list)
	{
		foreach($list as $idx => $x)
			return $idx;
	}
	
	static function moveToTop(&$array, $key)
	{
		$temp = array($key => $array[$key]);
		unset($array[$key]);
		$array = $temp + $array;
	}
	
	
	/**
	 * kad atiduot i JSON.parse nepakintanti indeksa, jei skaicius pakinta rikiavimo 
	 */

	static function idValueArray(array $input) 
	{
		$return = array();
		foreach ($input as $key => $value) {


			if (is_array($value))
				$value = self::idValueArray($value);

			$return[] = ['id' => $key, 'value' => $value];
		}

		return $return;
	}
}
