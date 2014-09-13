<?php

class GW_Lang_XML
{
	static function getCacheFileName($file, $ln)
	{
		$name = str_replace(GW::$dir['ROOT'],'',$file);
		$name = str_replace(Array('/','\\'),'_',$name);
		
		$name = GW::$dir['LANG_CACHE']. $name.'_'.$ln.'.dat';
		
		//dump($name);
		
		return $name;
	}
	
	static function loadCached($file)
	{
		return unserialize(file_get_contents($file));
	}
	
	static function saveCached($file, $data)
	{
		file_put_contents($file, serialize($data));
	}
	
	static function load($file, $ln)
	{
		if(!is_file($file))
			return Array();
			
		$cache_file = self::getCacheFileName($file, $ln);
		
		//load cached
		if(filemtime($file) <= @filemtime($cache_file))
			return self::loadCached($cache_file);

		//parse, cache
		$data = self::parse($file, $ln);
		self::saveCached($cache_file, $data);
		
		return $data;
	}	
	
	static function parse($file, $ln)
	{
		return self::___multiLangStruct(self::parseXML($file), $ln);
	}

	static function getAllLn($file)
	{
		$rez = Array();
		
		foreach(GW::$static_conf['LANGS'] as $ln_code)
			$rez[$ln_code]=self::load($file, $ln_code);
		
		return $rez;
	}

	
    //------- GW lang file standart---------

	static function ___getLn(&$list, $ln)
	{
		foreach($list as $item)
			if($item['tag']==strtoupper($ln))
				return $item['value'];
				
		return "%NOT SPECIFIED%";
	}
	
	static function ___multiLangStruct($in_tree, $ln)
	{
		$tree = Array();
		
		foreach($in_tree as $item)
		{

			$tree[ $item['attributes']['ID'] ] = 
				isset($item['childs']) ?
				(
					$item['childs'][0]['tag']=='I' ? 
					self::___multiLangStruct($item['childs'], $ln) :
					self::___getLn($item['childs'], $ln)
				) :
				$item['value'];

		}
		
		return $tree;
	}    
    //-----------------------
	//xml parse -------------
	
	static function &parseXML($file)
	{
		$resource = xml_parser_create();
		xml_parse_into_struct($resource, file_get_contents($file), $out);
		xml_parser_free($resource);	

		$level=0;

		$tree = self::__getXMLChildren($out, $level);

		if(!$tree)
			trigger_error("XML file '$file' parsing failed",E_USER_NOTICE);
        
        return $tree;
	}
	
    static function &__getXMLChildren($vals, &$i)
    {
    	$children = array();
 
    	while (++$i < count($vals))
    	{
    		$item =& $vals[$i];
    		
    		$type=$item['type'];

    		unset($item['type']);
    		unset($item['level']);
    		
    		switch ($type)
    		{
    			case 'complete':    				
    				if (isset($item['value']))
    					$children[]=$item;
    			break;

    			case 'open':
    				$item['childs']=self::__getXMLChildren($vals, $i);
    				$children[]=$item;
    			break;

    			case 'close':
    				return $children;
    		}
    	}
    }	
    //-------------
        
}