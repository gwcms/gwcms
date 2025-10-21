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
	
	
	static function csvToArray($filename, $delimiter = ",") {
	    if (!file_exists($filename) || !is_readable($filename)) {
		return false;
	    }

	    $header = null;
	    $data = [];

	    if (($handle = fopen($filename, "r")) !== false) {
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
		    if (!$header) {
			// First row becomes header keys
			$header = $row;
		    } else {
			// Combine header with row values
			$data[] = array_combine($header, $row);
		    }
		}
		fclose($handle);
	    }

	    return $data;
	}	
	
}
