<?php


class gw_encode_fields_test extends gw_testclass
{
	
	public $test_class_present = false;
	
	
	
	function init()
	{
		
		//$this->test_obj = new GW_General_RPC;
		//$this->test_obj->url = "http://192.168.0.24/acs/service/user/";
		
		$this->initDB();
		$this->initAdminAutoLoad();
		
		$this->testobj = false;
		
		
	}	
	
	
	function testGetCountryByPhone()
	{
		
		$o = GW_ADM_Page::singleton()->find('path="system"');
		
		//d::dumpas($o);
		
		$rand = random_int(100000, 999999);
		
		$array = $o->get('info');
		$array->test="{$rand}abc";
		
		
		
		$o->set('info/test1', "test{$rand}");
		//$o->set('info/test1', 'test123');
		$info = $o->info;
		
		$this->assertEquals($o->get('info/test'), "{$rand}abc");
		$this->assertEquals($o->info->test, "{$rand}abc");
		$this->assertEquals($info->test, "{$rand}abc");
		
		$this->assertEquals($info->test1, "test{$rand}");
		
		$this->assertEquals($o->isChangedField('info'), true);
		
		$o->save();
		
		
		$o1 = GW_ADM_Page::singleton()->find('path="system"');
		
		$this->assertEquals($o1->info->test, "{$rand}abc");
		
		
		//d::dumpas($o->changed_fields);
		
		
		//d::ldump([$o->get('info/test'), $o->info->test, $info->test]);

		//test
		
		
	}
}