<?php


class gw_data_object_test extends gw_testclass
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
	
	
	function testEncode()
	{
		
		$o = GW_ADM_Page::singleton()->find('path="system"');
		
		//d::dumpas($o);
		
		$rand = random_int(100000, 999999);
		
		$array = $o->get('info');
		
		$test1_string="{$rand}abc";
		$test0_string="test{$rand}";
		$test2_string = "{$rand}hellou";
			
		
		$array->test=$test1_string;
		
				
		$o->set('info/test1', $test0_string);
		$info = $o->info;
		
		$this->assertEquals($o->get('info/test'), $test1_string);
		$this->assertEquals($o->info->test, $test1_string);
		$this->assertEquals($info->test, $test1_string);
		
		$this->assertEquals($info->test1, $test0_string);
		
		$this->assertEquals($o->isChangedField('info'), true);
		
		$o->save();
		
		
		$o1 = GW_ADM_Page::singleton()->find('path="system"');
		
		
		$this->assertEquals($o1->info->test, $test1_string);
		
		
		
		$info = $o1->info;
		$info->test3 = $test2_string;
		
		$o1->info = $info;
		$o1->save();
		
		$o2 = GW_ADM_Page::singleton()->find('path="system"');
		$this->assertEquals($o1->info->test3, $test2_string);
		
		
		//load method #2
		$o3 = GW_ADM_Page::singleton()->createNewObject($o1->id, true);
		$this->assertEquals($o3->info->test3, $test2_string);
		
		//load method #3
		$o4 = GW_ADM_Page::singleton()->find($o1->id);
		$this->assertEquals($o4->info->test3, $test2_string);		
		
		//d::dumpas($o3->info->test3 == );
		
		
		
		//d::dumpas($o->changed_fields);
		
		
		//d::ldump([$o->get('info/test'), $o->info->test, $info->test]);

		//test
		
		
	}
	
	function testExtendedObject()
	{
		$user = GW_User::singleton()->createNewObject(GW_USER_SYSTEM_ID, true);
		
		$rand = random_int(100000, 999999);
		$test0_string="test{$rand}";		
		
		
		$user->set('ext/labas', $test0_string);
		
		$this->assertEquals($user->get('ext')->labas, $test0_string);
		$this->assertEquals($user->get('ext/labas'), $test0_string);
	}
}