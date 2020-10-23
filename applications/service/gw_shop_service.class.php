<?php

//call me example: http://site.url.and.or.base.path/service/test/public/datetime
//call me example: http://site.url.and.or.base.path/service/test/public/echo?test=best&abc=123
// or class call
// $rpc = new GW_General_RPC('http://site.url.and.or.base.path/service/test')
// $rpc->call('public/echo',['test'=>'best','abc'=>123, $post_args]);

class GW_Shop_service extends GW_Common_Service
{

	public $username = 'aaa';
	public $pass = 'bbb';

	
	function init()
	{
		parent::init();
		
		list($this->username, $this->pass) = explode(':', GW_Config::singleton()->get('gw_products/userapi_userpass'));
	}
	
	function checkAuth()
	{
		if ($this->checkBasicHTTPAuth())
			return true;
	}
	
	

	
	function actCreateDiscountCode()
	{
		
	}



	function actTestCall()
	{

		$rpc->debug = true;

		$response = $rpc->sysUserCall('sysinfo');
		$response->meta = $rpc->debug_data;

		return (array) $response;
	}
}
