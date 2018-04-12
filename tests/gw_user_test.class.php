<?php


class gw_user_test extends GW_TestClass
{
	
	function __construct($testclass) {
		$this->init();
	}
	
	function init()
	{
		GW::db();
	}
	
	
	function testLang()
	{
		$user = new GW_User(GW_USER_SYSTEM_ID, true);
		
		$rand = GW_String_Helper::getRandString(50);
		
		$user->set('ext/labadiena', $rand);
		
		$this->assertEquals($user->get('ext/labadiena'), $rand);
		
		$user->set('associat/objekto/testas', $rand);
		
		$expected = (object)['objekto'=>(object)['testas'=>$rand]];
		
		//d::dumpas([$user->get('associat'),$expected]);
		
		$this->assertEquals($user->get('associat'), $expected);
		
	}
	

	
	
}