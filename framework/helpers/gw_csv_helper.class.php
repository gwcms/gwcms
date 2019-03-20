<?php

class GW_CSV_Helper
{
	static function readByLine($file, $callback, $opts=[])
	{
		$file = fopen($file, 'r');
		$i=0;
		
		while (($line = fgetcsv($file)) !== FALSE) {			
			if(!$callback($line, $i=0))
				break;
		}
		fclose($file);		
	}
	
}
