<?php


class gw_service_test_user extends gw_testservice
{
	
	function init()
	{
		$this->testobj->basicAuthSetUserPass('aaa','bbb');
	}
	
	
	function randStr($length=15)
	{
		$set="ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		$str = date('YmdHi').'00';
		
		for($i=0;$i<$length;$i++)
			$str.=$set[rand(0,34)];

		return $str;
	}
	
	
	
	private $userid;
	private $token;
	
	function testGoodLogin()
	{
		//test good login
		
		$resp = $this->testobj->login([],['user'=>'demo','pass'=>'123456','ip'=>$_SERVER['REMOTE_ADDR']]);

		$this->assertEquals($resp->user->username, 'demo');
		
		$this->userid = $resp->user->id;
		$this->token = $resp->user->token;
		
		
	}
	
	function testBadLogin()
	{
		//test bad login
		
		$resp = $this->testobj->login([],['user'=>'demo','pass'=>'123456aaa','ip'=>$_SERVER['REMOTE_ADDR']]);
		
		$this->assertEquals($resp->error, '1');		
	}	
	
	function testUpdate()
	{
		$testval = 'Service testing '.$this->randStr();
		$resp2 = $this->testobj->update([],['user'=>['id'=>$this->userid, 'description'=>$testval], 'token'=>$this->token]);
		
		
		$respinf = $this->testobj->info([], ['userid'=>$this->userid, 'token'=>$this->token]);
		
		$this->assertEquals($respinf->user->description, $testval);
	}
	
	function testErrorOnUpdate()
	{
		$resp3 = $this->testobj->update([],['user'=>['id'=>$this->userid, 'email'=>'asdfs'], 'token'=>$this->token]);
		
		
		$this->assertEquals($resp3->updateuser, 'FAIL');
		$this->assertEquals($resp3->errors->email, '/G/VALIDATION/EMAIL/INVALID_EMAIL');
	}
	
	function testRegister()
	{
		
	}
	
	function testLogout()
	{
		
	}
}