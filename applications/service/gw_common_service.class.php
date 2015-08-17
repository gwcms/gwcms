<?php

class GW_Common_Service
{
	public $path_arr;
	
	public $admin=false;
	public $app;
	public $debug = true;
	
	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
		
	}
	
	function init()
	{
		$this->app->initDB();
	}
	
	function checkAuth($can_pass_auth = false)
	{
		$args = $_POST;
		
		if($_SERVER['REMOTE_ADDR']=='127.0.0.1')
			$args = $_REQUEST;
		
		
		$identity = false;
		
		if($can_pass_auth && isset($args['uid']) && isset($args['upwd']))
		{
			$identity = GW::getInstance('r1_identity')->find(['id=? AND pass=? AND active=1', $args['uid'], md5($args['upwd'])]);
			
		}elseif( isset($args['uid']) && isset($args['token']) ){
			$identity = GW::getInstance('r1_identity')->find(['id=? AND token=? AND ip=? AND active=1', $args['uid'], $args['token'], $_SERVER['REMOTE_ADDR']]);
			
		}
		
		if($identity){
			$identity->saveValues(['last_login_time'=>date('Y-m-d H:i:s')]);
			
			$this->user = $identity;
			return true;
		}
	}
	
	function actGetToken()
	{
		$this->user->token = GW_String_Helper::getRandString(20);
		$this->user->ip = $_SERVER['REMOTE_ADDR'];
		$this->user->update(['token','ip']);
		
		return ['token'=>$this->user->token];
	}
	
	function actPublic($args)
	{
		$act = array_shift($args);

		if(is_callable([$this, 'pact'.$act]))
		{
			$response = $this->{'pact'.$act}($args);
		}else{
			$response['error']="Requested public action not found";
			$response['error_code']='405';				
		}
		
		return $response;
	}
	
	function pactEcho($path)
	{
		return ['post'=>$_POST, 'get'=>$_GET, 'path'=>$path];
	}	
	
	
	
	function processAct(&$args, &$response)
	{
		$act = array_shift($args);

		if(is_callable([$this, 'act'.$act]))
		{
			$response = $this->{'act'.$act}($args);
		}else{
			$response['error']="Requested action not found";
			$response['error_code']='404';				
		}
	}
	
	
	function process()
	{
		ob_start();
		
		$t = new GW_Timer;
		$response=[];
		
		$args = $this->path_arr;
				
		if(!count($args) || count($args)==1 && !$args[0])
		{
			$response['error']="Bad request";
			$response['error_code']='400';
		}else{		
			
			//no authorization required for /public/*
			if($args[0]=='public')
			{
				$this->processAct($args, $response);
			}else{
				//authorized requests
				
				if(!$this->checkAuth($args[0]=='getToken'))
				{
					$response['error']="Unauthorized";
					$response['error_code']='401';
				}else{
					$this->processAct($args, $response);
				}
			}
		
		}
		
		$response['process_time']=$t->stop(5);
		
		$unexpected=ob_get_contents();
		ob_end_clean();
		
		if($unexpected)
		{
			if($this->debug)
				$response['unexpected_output'] = $unexpected;
			
			mail('errors@gw.lt', "Error under service ".$this->name, "Unexpected output: \r\n".$unexpected);
		}
		
		
		header('Content-type: text/plain');
		echo json_encode($response, JSON_PRETTY_PRINT);
		exit;
	}
}