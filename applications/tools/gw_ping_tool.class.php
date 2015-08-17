<?php

class GW_Ping_Tool
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
		$response = [];
		
		$this->app->initSession();
		
		
		if(!isset($_GET['extend']))
			$GLOBALS['do_not_register_request'] = true;
		
		$this->app->initAuth();
		
		$response['sess_expires'] = -2;
		
		if($this->app->user)
		{
			$response['sess_expires']=$this->app->user->remainingSessionTime();
			$response['new_messages']=$this->app->user->countNewMessages();
		}	
		
		FINISH:
		echo json_encode($response);
		exit;	
		
	}
}


/*
 * 
if(!$_GET['extend'])
	$do_not_register_request=true;


include GW::$dir['ADMIN'].'init_auth.php';
 * 

 */