<?php

class GW_General_RPC
{
	public $url;
	public $debug=false;
	public $debug_data;
	public $last_url;
	public $timeout=500;
	
	public function call($name, $get=[], $post=[]) 
	{
		//open connection
		$ch = curl_init();
		//set the url, number of POST vars, POST data

		$getargs= $get ? '?'.http_build_query($get) : '';
			
			
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_URL, $url=$this->url.'/'.$name.$getargs);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if(method_exists($this, '__eventCurlOpt'))
			$this->__eventCurlOpt($ch);

		//execute post
		$raw_result = curl_exec($ch);
		
		if($this->debug)
			$this->debug_data[] = ['url'=>$url,'act'=>$name, 'get'=>$get, 'post'=>$post, 'response'=>$raw_result, 'error'=>curl_error ($ch)];
		
		$this->last_url = $url;
		
		$json_result = json_decode($raw_result);
		
		if(!isset($json_result->process_time))
		{
			if(!$json_result)
				$json_result=new stdClass;

			
			$json_result->error_code='66';
			$json_result->response=$raw_result;
			$json_result->request = ['url'=>$url];
			$json_result->curl_errpr = curl_error($ch);
		}
		
		if(isset($json_result->error_code) && method_exists($this, '__eventOnError'))
			$this->__eventOnError($json_result);		
		
		curl_close($ch);
		
		return $json_result;
	}
	
	public function __call($name, $args)
	{
		$get = isset($args[0]) ? $args[0] : [];
		$post = isset($args[1]) ? $args[1] : [];
			
		return $this->call($name, $get, $post);
	}
	
	function sysUserCall($name, $get=[], $post=[], $uid=GW_USER_SYSTEM_ID)
	{
		$token = GW::getInstance('gw_temp_access')->getToken($uid);
		
		$get['temp_access']=$uid.','.$token;
		
		return $this->call($name, $get, $post);
	}
}