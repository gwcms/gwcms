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
		foreach ($list as $item)
			if ($item['tag'] == strtoupper($ln))
				return $item['value'];

		return "%NOT SPECIFIED%";
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
}
