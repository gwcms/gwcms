#!/usr/bin/php
<?php

chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php';


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
		
		$code = explode("<?php", $code);
		
		
		
		$code = preg_replace_callback("/new ([a-z_0-9]+)/i", function($m){return "new \\{$m[1]}";}, $code);	
		$code = preg_replace_callback("/([a-z_0-9]+)::/i", function($m){return "\\{$m[1]}::";}, $code);	
		$code = str_replace('\parent::','parent::', $code);
		$code = str_replace('\self::','self::', $code);
		
		$code = str_ireplace('GW_Common_Module', '\GW_Common_Module', $code);
		$code = str_ireplace('GW_Module', '\GW_Module', $code);
		
		
		$code = "<?php\n"."\nnamespace GwAdmMod\\".strtolower($dirname).";\n".$code[1];
		file_put_contents($mod,$code);
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



/*
gw_autoload.class:
	static function load($class)
	{
		$file = strtolower($class) . '.class.php';
		
		//do not try load smarty classes, smarty has own autoloader
		if (strpos($file, 'smarty_') !== false)
			return;

		//load classes under namespaces
		if(strpos($file,'\\') !== false){
			$parts = explode('\\', strtolower($class));
			
			
			if($parts[0]=='gwadmmod'){
				$file = GW::s('DIR/ADMIN/MODULES').$parts[1].'/'.$parts[2].'.class.php';
				include_once $file;
			}else{
				$file = implode('/', $parts) . '.class.php';	
			}
			
		}

		if (self::tryLoadDirArray($file, GW::s('DIR/AUTOLOAD')))
			return true;


		//for example try search gw_article.class.php in admin/modules/
		//will search admin/modules/ * /gw_article.class.php
		//will find admin/modules/articles/gw_article.class.php
		
	
		self::searchSubDirs(GW::s('DIR/AUTOLOAD_RECURSIVE'), $file);
	}
 * 
 * 
 gw_application.class:
	function &constructModule($dir, $name)
	{
		include_once GW::s("DIR/{$this->app_name}/MODULES") . "{$dir}/module_{$name}.class.php";
		
		$dir = strtolower($dir);
		$name = "\\GwAdmMod\\$dir\\Module_{$name}";

		$obj = new $name();
		$obj->app = $this;
		
		return $obj;
	}
 * 
 *  */

