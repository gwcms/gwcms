<?php

function dump($var)
{
	if($_GET['dump_backtrace'])
		backtrace();
		
	static $html;
	
	if(!$html)
		// 1st if calling from apache? php-cli does not give this , 2nd text/plain format
		$html = ($_SERVER["SERVER_SOFTWARE"] && stripos(implode('',headers_list()),'text/plain')===false) ? 1 : 2;
	
	echo $html==1 ? "<pre>" : '';
	
	$var = func_num_args() > 1 ? func_get_args() : $var;
	
	if(is_object($var))
		print_r($var);
	else
		print(is_array($var) ? GW_Json_Format_Helper::f($var) : $var);	
	
	echo $html==1 ? "</pre>" : "\n";
}

class GW_Timer
{
	var $start;
	var $time_sum=0;
	function __construct(){$this->start=microtime(1);}
	function start(){$this->start=microtime(1);}
	function pause(){$this->time_sum += microtime(1) - $this->start;}
	function result($precision=2){return sprintf('%01.'.(int)$precision.'f',$this->time_sum);}
	function stop($precision=2){$this->pause();$this->start();return $this->result($precision);}
}

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
		
		
		if(self::tryLoadDirArray($file, self::$dirs[GW::$public ? 'PUB':'ADMIN']))
			return true;
			
		
		//for example try search gw_article.class.php in admin/modules/
		//will search admin/modules/*/gw_article.class.php
		//will find admin/modules/articles/gw_article.class.php
		
		
		self::searchSubDirs(GW::$dir['MODULES'], $file);
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
		self::$dirs['ADMIN']=Array(GW::$dir['LIB']);
		self::$dirs['PUB']=Array(GW::$dir['LIB'], GW::$dir['PUB_LIB']);
		
		spl_autoload_register(Array('self','load'));
		
		self::$search_timer=new GW_Timer;
	}
}

