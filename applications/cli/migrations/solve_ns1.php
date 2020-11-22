#!/usr/bin/php
<?php

chdir(__DIR__.'/../../../');
include __DIR__.'/../../../init_basic.php';


$dir = GW::s("DIR/ADMIN");


$mods = glob($dir['MODULES'].'*/module_*.class.php');
$cnt = 0;
$skip = 0;


foreach($mods as $mod)
{
	$dirname = basename(dirname($mod));
	echo "Working on $dirname ".basename($mod);
	
	$code = file_get_contents($mod);
	
	
	if(strpos($code, 'namespace ')===false){
		
		/*
		$code = explode("<?php", $code);
		
		
		
		$code = preg_replace_callback("/new ([a-z_0-9]+)/i", function($m){return "new \\{$m[1]}";}, $code);	
		$code = preg_replace_callback("/([a-z_0-9]+)::/i", function($m){return "\\{$m[1]}::";}, $code);	
		$code = str_replace('\parent::','parent::', $code);
		$code = str_replace('\self::','self::', $code);
		
		$code = str_ireplace('GW_Common_Module', '\GW_Common_Module', $code);
		$code = str_ireplace('GW_Module', '\GW_Module', $code);
		
		
		$code = "<?php\n"."\nnamespace GwAdmMod\\".strtolower($dirname).";\n".$code[1];*/
		
		
		$code = str_ireplace('Module_', 'AdmModule_'.ucfirst($dirname).'_', $code);
		file_put_contents($mod,$code);
		
		//rename($mod, str_replace(''));
		
		
		$cnt++;
		echo " OK";
	}else{
		$skip++;
		echo " SKIP";
	}
	
	
	
	echo "\n";
}

print_r(['fix'=>$cnt, 'skip'=>$skip]);



//priezastis module_items, module_contacts pasikartojamumas
//atsauktas priezastys labai dau \ dadet visur

//sprendimas perdaryt module_katalogopavad_controleriopavadinimas

