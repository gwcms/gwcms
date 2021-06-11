<?php

class GW_CSV_Helper
{
	static function readByLine($file, $callback, $opts=[])
	{
		$file = fopen($file, 'r');
		$i=0;
		$delimiter = $opts['delimiter'] ?? ',';
		$skip =  $opts['skip'] ?? -1;
		
		while (($line = fgetcsv($file, 0, $delimiter)) !== FALSE) {	
			
			if($skip < $i && !$callback($line, $i))
				break;
			$i++;
		}
		
		$callback("last", -1);
			
		fclose($file);		
	}
	
	
	static function identifyAndRemoveEncoding(&$str, $opt=[])
	{
		if(substr($str, 0, 3) == "\xEF\xBB\xBF")
		{
			//if(isset($opt['d']))
			//	d::ldump(GW_Debug_Helper::stringVerbose($str));
				
			$str = substr($str, 3);	
			
			//if(isset($opt['d']))
			//	d::ldump(GW_Debug_Helper::stringVerbose($str));			
			
			return "UTF-8";
		}
	}
	
}
