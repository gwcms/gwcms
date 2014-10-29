<?php

class GW_Autoload
{
	static $dirs=Array();
	static $search_timer;
	
	static function tryLoad($file, $dir='')
	{
		$file=$dir.$file;
		
		if (is_file($file))
		{	
			require $file;
			return true;
		}
	}
	
	static function tryLoadDirArray($file, $dirs)
	{
		foreach($dirs as $dir)
		{
			if(self::tryLoad($file, $dir))
				return true;
				
			if(self::searchSubDirs($dir, $file))
				return true;
		}
	}
	
	static function load($class)
	{
		$file = strtolower($class) . '.class.php'; 
		
		//do not try load smarty classes, smarty has own autoloader
		if(strpos($file,'smarty_')!==false)
			return;
		
		
		
		
		if(self::tryLoadDirArray($file, GW::s('DIR/AUTOLOAD')))
			return true;
			
		
		//for example try search gw_article.class.php in admin/modules/
		//will search admin/modules/*/gw_article.class.php
		//will find admin/modules/articles/gw_article.class.php
		
		
		self::searchSubDirs(GW::s('DIR/AUTOLOAD_RECURSIVE'), $file);
	}
	
	static function searchSubdirs($root_dir, $file)
	{
		self::$search_timer->start();
		$files=glob($root_dir."*/".$file);
		self::$search_timer->pause();
		
		//Pažiūrėti kiek sugaištama laiko paieškoms
		//dump(self::$search_timer->result(10));
		//dump("Looking for $root_dir*/$file");
		
		if(count($files))
		{
			if(count($files)>1)
				trigger_error("Same class file found".json_encode($files), E_USER_NOTICE);
				
			//dump("found $files[0]");
			return self::tryLoad($file, dirname($files[0]).'/');
		}
	}
	
	static function init()
	{	
		spl_autoload_register(Array('self','load'));
		
		self::$search_timer=new GW_Timer;
	}
}