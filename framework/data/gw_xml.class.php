<?php

class GW_XML
{

	private function __array2XML($c, $a)
	{
		foreach ($a as $v) {
			if (isset($v["@text"])) {
				$ch = $c->addChild($v["@tag"], $v["@text"]);
			} else {
				$ch = $c->addChild($v["@tag"]);
				if (isset($v["@items"])) {
					self::__array2XML($ch, $v["@items"]);
				}
			}
			if (isset($v["@attr"])) {
				foreach ($v["@attr"] as $attr => $val) {
					$ch->addAttribute($attr, $val);
				}
			}
		}
	}
	/*
	  Array
	  (
	  "@tag"=>"name",
	  "@attr"=>array("id"=>"1","class"=>"2")
	  "@items"=>array(
	  0=>array(
	  "@tag"=>"name","@text"=>"some text"
	  )
	  )
	 */

	static function array2XML($arr, $root)
	{
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><{$root}></{$root}>");

		self:: __array2XML($xml, $arr);
		return $xml->asXML();
	}

	static function humanReadable($xml, $html_output = false)
	{
		$xml_obj = new SimpleXMLElement($xml);
		$level = 4;
		$indent = 0; // current indentation level  
		$pretty = array();

		// get an array containing each XML element  
		$xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

		// shift off opening XML tag if present  
		if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0]))
			$pretty[] = array_shift($xml);


		foreach ($xml as $el) {
			if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
				// opening tag, increase indent  
				$pretty[] = str_repeat(' ', $indent) . $el;
				$indent += $level;
			} else {
				if (preg_match('/^<\/.+>$/', $el)) {
					$indent -= $level;  // closing tag, decrease indent  
				}
				if ($indent < 0) {
					$indent += $level;
				}
				$pretty[] = str_repeat(' ', $indent) . $el;
			}
		}
		$xml = implode("\n", $pretty);
		return ($html_output) ? htmlentities($xml) : $xml;
	}
	/* 	
	 * Function assocToXML($theArray)
	 * 
	  exmple assoc array:
	  Array
	  (
	  [1] => Array
	  (
	  [id] => 18
	  [title] => test
	  [@tag] => item
	  )
	  [2] => Array
	  (
	  [@comment] => Way to generating comment
	  )
	  [demo] => Array
	  (
	  [title] => test 123
	  )
	  )
	  Will generate:
	  <item>
	  <id>18</id>
	  <title>test</title>
	  </item>
	  <!--Way to generating comment-->
	  <demo>
	  <title>test 123</title>
	  </demo>
	 */

	static function assocToXML($theArray)
	{
		// parse the array for data and output xml
		foreach ($theArray as $tag => $val) {
			if (!is_array($val)) {
				$theXML .= '<' . $tag . '>' . htmlentities($val) . '</' . $tag . '>';
			} elseif ($val['@xml']) {
				$theXML .= $val['@xml'];
			} elseif ($val['@comment']) {
				$theXML .= '<!--' . $val['@comment'] . '-->';
			} else {
				if ($val['@tag']) {
					$tag = $val['@tag'];
					unset($val['@tag']);
				}

				$theXML .= '<' . $tag . '>' . self::assocToXML($val, $tabCount + 1);
				$theXML .= '</' . $tag . '>';
			}
		}

		return $theXML;
	}
	
	static function xmlToArray($xmlstring)
	{
		$xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
		$json = json_encode($xml);
		$data = json_decode($json,TRUE);
			
		return $data;
	}

	static function simpleXmlArrFixList($arr)
	{
		if(!$arr)
			return [];
			
		//d::dumpas(array_keys($arr)[0]);
		if(array_keys($arr)[0]=="0"){
			return $arr;
		}else{
			return [$arr];
		}
	}	
	
}
