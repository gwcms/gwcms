<?php

//call me example: http://site.url.and.or.base.path/service/test/public/datetime
//call me example: http://site.url.and.or.base.path/service/test/public/echo?test=best&abc=123

// or class call
// $rpc = new GW_General_RPC('http://site.url.and.or.base.path/service/test')
// $rpc->call('public/echo',['test'=>'best','abc'=>123, $post_args]);

class GW_Test_service extends GW_Common_Service
{
	function checkAuth()
	{
		if($this->user)
			return true;
		
		die($this->getStdAuthUserPass());
	}
	
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
	
	function actSysInfo()
	{
		$i = [];
		
		$i['url']=Navigator::getBase(true);
		
		
		if($this->user->isRoot()){
			$i['user']=$this->user->toArray();
		}
		
		return $i;
	}
	
	function actTestCall()
	{
		$rpc = new GW_General_RPC();
		$rpc->url = Navigator::getBase(true).'service/test';
		$rpc->debug=true;
		
		$response = $rpc->sysUserCall('sysinfo');
		$response->meta = $rpc->debug_data;
		
		return (array)$response;
	}
}