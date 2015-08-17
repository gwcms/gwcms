<?php

//call me example: http://site.url.and.or.base.path/service/test/public/datetime
//call me example: http://site.url.and.or.base.path/service/test/public/echo?test=best&abc=123

// or class call
// $rpc = new GW_General_RPC('http://site.url.and.or.base.path/service/test')
// $rpc->call('public/echo',['test'=>'best','abc'=>123, $post_args]);

class GW_Test_service extends GW_Common_Service
{

	
	function pactDateTime($args)
	{
				
		return ['date'=>date('Y-m-d H:i:s')];
		
	}
	
	function actTestInternet()
	{
		if(GW_Test_Internet::check($error)){
			return ['testinternet'=>1];			
		}else{
			return ['error_code'=>6, 'error'=>$error];			
		}
	}
}