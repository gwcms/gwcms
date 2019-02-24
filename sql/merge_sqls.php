<?php

$files = glob(__DIR__.'/201*.sql');

$str = '';
$str .="#File count: ".count($files)."\n";

foreach($files as $file)
{
	$str .= '#------------------------------'.basename($file)."-----------------------------------------------------------------------\n";
	
	$str .= "\n".file_get_contents($file)."\n";
	
	$str .= "\n";
}

//file_put_contents(__DIR__."/../repository/.sys/temp/merge_".date('Ymd_his').".sql", $str);


header('Content-type: text/plain');

echo $str;