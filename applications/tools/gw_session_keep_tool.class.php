<?php

class GW_Session_Keep_Tool
{
	public $path_arr;
	
	public $admin=false;
	
	public $app;
	
	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
		
	}
	
	
	function init()
	{
		$this->app->initDB();
	}
	
	function process()
	{
		$this->app->initSession();
		
		
		if(!isset($_GET['extend']))
			$GLOBALS['do_not_register_request'] = true;
		
		$this->app->initAuth();
		
		
		if($this->app->user)
			die((string)$this->app->user->remainingSessionTime());

		die('-2');
	}
}


/*
 * 
if(!$_GET['extend'])
	$do_not_register_request=true;


include GW::$dir['ADMIN'].'init_auth.php';
 * 

 */