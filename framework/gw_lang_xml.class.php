<?php

class GW_Lang_XML
{

	static function getCacheFileName($file, $ln)
	{
		$name = str_replace(GW::s('DIR/ROOT'), '', $file);
		$name = str_replace(Array('/', '\\'), '_', $name);

		$name = GW::s('DIR/LANG_CACHE') . $name . '_' . $ln . '.dat';

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
		if (!is_file($file))
			return Array();

		$cache_file = self::getCacheFileName($file, $ln);

		//load cached
		if (filemtime($file) <= @filemtime($cache_file))
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

	static function getAllLn($langs, $file)
	{
		$rez = Array();

		foreach ($langs as $ln_code)
			$rez[$ln_code] = self::load($file, $ln_code);

		return $rez;
	}

	//------- GW lang file standart---------

	static function ___getLn(&$list, $ln)
	{
		//d::ldump($list);
		
		foreach ($list as $item)
			if ($item['tag'] == strtoupper($ln))
				return $item['value'];
			
		
		//d::dumpas($list);

		return "%404-".$list[0]['tag'].":".$list[0]['value'].'%';
	}

	static function ___multiLangStruct($in_tree, $ln)
	{
		$tree = Array();

		foreach ($in_tree as $item) {

			$tree[$item['attributes']['ID']] = isset($item['childs']) ?
			    (
			    $item['childs'][0]['tag'] == 'I' ?
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

		$level = 0;

		$tree = self::__getXMLChildren($out, $level);

		if (!$tree)
			trigger_error("XML file '$file' parsing failed", E_USER_NOTICE);

		return $tree;
	}

	static function __getXMLChildren($vals, &$i)
	{
		$children = array();

		while (++$i < count($vals)) {
			$item = & $vals[$i];

			$type = $item['type'];

			unset($item['type']);
			unset($item['level']);

			switch ($type) {
				case 'complete':
					if (isset($item['value']))
						$children[] = $item;
					break;

				case 'open':
					$item['childs'] = self::__getXMLChildren($vals, $i);
					$children[] = $item;
					break;

				case 'close':
					return $children;
			}
		}
	}
	//-------------
	function __addChildsRecursive($parent, $struct)
	{
		foreach($struct as $key => $substruct)
		{			
			if(is_array($substruct)){
				$child=$parent->addChild("i");
				$child->addAttribute('id', $key);
				self::__addChildsRecursive($child, $substruct);
			}else{
				$parent->addChild("i", $substruct)->addAttribute('id', $key);
			}
		}
	}
	
	function modify($xml, $path, $lang_data)
	{
		$sxml = new SimpleXMLElement($xml, 0, false);
		
		$path_to_xpath = 'i[@id = "'.implode('"]/i[@id = "',explode('/', $path)).'"]';
		
		$test = $sxml->xpath($path_to_xpath);
		
		if(isset($test[0]))
			self::__addChildsRecursive($test[0], $lang_data);
		
		$out = $sxml->asXML();
		
		//take off cdatas
		preg_match_all("/<!\[\CDATA\[(.*?)\]\]>/is", $out, $cdatas);
		$cnt=-1;
		$out = preg_replace_callback("/<!\[\CDATA\[(.*?)\]\]>/is", function($mathc) use (&$cnt){ $cnt++; return "///cdata///$cnt///";  }, $out);		

		
		//human readable output
		$out = tidy_repair_string($out, ['input-xml'=> 1, 'indent' => 1, 'wrap' => 0], 'utf8');
		//spaces to tabs
		$out = preg_replace('/(?:^|\G)  /um', "\t", $out);
		
		//return cdatas back
		$out = preg_replace_callback("/\/{3}cdata\/{3}(\d+)\/{3}/", function($m) use (&$cdatas){ return $cdatas[0][$m[1]];  }, $out);	
				
		return $out;
	}
	
	
	
	static function stuctAdd(&$sxml, &$arr)
	{
		foreach($arr as $node){
			$tag = strtolower($node['tag']);
			
			if(isset($node['childs'])){
				$child = $sxml->addChild($tag);
				self::stuctAdd($child, $node['childs']);
			}else{
				if($node['value']!==''){
					if(preg_match('/[<>]/',$node['value'])){
						//d::dumpas('test');
						$child = $sxml->addChild($tag);
						$child->addCData($node['value']);
					}else{
						$child = $sxml->addChild($tag, $node['value']);
					}
				}
			}
			
			
			if(isset($node['attributes']))
				foreach($node['attributes'] as $att => $val)
					$child->addAttribute(strtolower($att), $val);
			
		}
	}
	
	function struct2Xml($arr)
	{
		$sxml = new SimpleXMLExtended('<?xml version="1.0" encoding="utf-8"?><xml></xml>', 0, false);
		
		//$test = $sxml->xpath();
		
		//d::dumpas($test);
		
		self::stuctAdd($sxml, $arr);
		
		$out = $sxml->asXML();
		
		$out = tidy_repair_string($out, ['input-xml'=> 1, 'indent' => 10, 'wrap' => 0], 'utf8');
		return $out;
	}
	
	
	
	function structMod(&$arr, $key, $val, $ln=false)
	{
		$key = trim($key,'/');
		$keyparts = explode('/', $key);
		$lastkey  = $keyparts[count($keyparts)-1];
		
		$pointer =& $arr;
		
		//d::dumpas($val, ['hidden'=>"test point 1"]);
		
		foreach($keyparts as $search){
		
			$found = false;
			foreach($pointer as &$elm){
				
				
				$key = $elm['attributes']['ID'];
				
				
				
				if($key == $search)
				{
					$found = true;
					if(isset($elm['childs'])){
						$pointer =& $elm['childs'];
					}else{
						$pointer =& $elm;
					}
					//d::ldump("pointer change $key");
					
					
					break;
				}
			}
			
			if(!$found){
				//d::ldump("pointer not found, create new $search");
				
				$new = ['tag'=>'I', 'attributes'=>['ID'=>$search], 'childs'=>[]];
				$pointer[] =& $new;
				
				if($search == $lastkey){
					$pointer =& $new;
				}else{
					$pointer =& $new['childs'];
				}
			}
		}
		
		//print_r(['before'=>$pointer]);
		
		
		if($ln){
			
			
			if(is_array($pointer)){
				
				$pointerx =& self::structLangNodeSeek($pointer, $ln, true);	
				$pointerx['value'] = $val;
				
			}else{
				$pointer = ['tag'=>'I','childs'=>['tag'=>$ln, 'value'=> $val]];
			}
		}else{
			$pointer = ['tag'=>'I', 'attributes'=>['ID'=>$search], 'value'=>$val];
		}
		
		//print_r(['after'=>$pointer]);
	}
	
	function &structLangNodeSeek(&$treePointer,$ln,$create=false)
	{
		$found = false;
		foreach($treePointer as &$elm){
			if($elm['tag']==$ln){
				$treePointer =& $elm;
				$found = true;
			}
		}
		if(!$found){
			if($create){
				$x = ['tag'=>$ln, 'value'=> false];
				$treePointer[] =& $x;
				return $x;
			}else{
				return false;
			}
		}		
				
		return $treePointer;
	}
}





class SimpleXMLExtended extends SimpleXMLElement {

	public function addCData($cdata_text) {
		$node = dom_import_simplexml($this);
		$no = $node->ownerDocument;
		$node->appendChild($no->createCDATASection(trim($cdata_text)));
	}

}
