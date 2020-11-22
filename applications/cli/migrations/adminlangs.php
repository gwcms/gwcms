#!/usr/bin/php
<?php

chdir(__DIR__.'/../../../');
include __DIR__.'/../../../init_basic.php';


$dir = GW::s("DIR/ADMIN/ROOT");



$cnt = 0;
$skip = 0;



$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir.'/'));

$files = array(); 

foreach ($rii as $file) {

    if ($file->isDir()){ 
        continue;
    }elseif(pathinfo($file->getPathname(), PATHINFO_EXTENSION)!='tpl'){
	    continue;
    }
    

    $files[] = $file->getPathname(); 

}

//print_r($files);

$acnt = 0;

foreach($files as $file)
{
	print_r($file."\n");
	$code = file_get_contents($file);
	
	


	$cnt = 0;
	$replace = function($m) use (&$cnt) {
		echo $m[1].$m[2].' -> '."GW::l('/m/".str_replace('.','/', $m[2])."')"."\n";
		$cnt++; 
		return "GW::l('/m/".str_replace('.','/', $m[2])."')";  
	};
	$code = preg_replace_callback('/(\$m->lang\.)([a-z_0-9.]+)/i', $replace, $code);	

	$acnt += $cnt;
	
	if($cnt){
		
		file_put_contents($file, $code);
	}
	
	print_r("$cnt\n");
		
		

}

print_r("total cnt: $acnt\n");




//priezastis module_items, module_contacts pasikartojamumas
//atsauktas priezastys labai dau \ dadet visur

//sprendimas perdaryt module_katalogopavad_controleriopavadinimas

