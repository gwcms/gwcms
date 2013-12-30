<?php

/**
 * 
 * @author wdm
 *
 * GateWay CMS namespace
 * 
 */

class GW
{
	/**
	 * 
	 * @var GW_Request
	 */
	static $request;
	/**
	 * 
	 * @var GW_ADM_User || GW_User
	 */	
	static $user;
	/**
	 * 
	 * @var GW_DB
	 */	
	static $db;
	static $static_conf;
	static $db_conf;
	static $dirnames;
	/**
	 * 
	 * @var Smarty
	 */	
	static $smarty;
	static $lang;
	static $dir;
	/**
	 * 
	 * @var GW_Auth
	 */	
	static $auth;
	static $public=false;
	static $error_log;
	
	static function getInstance($class, $file=false) 
	{
		static $cache;
		
		if( $cache[$class])
			return $cache[$class];
			
		if($file)
			include_once GW::$dir['ADMIN'].$file;
			
		$cache[$class] = new $class();
	
		return $cache[$class];
	}
	
	function &_($varname)
	{
		return self::$$varname;
	}
	
}