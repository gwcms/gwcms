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
		
		
		$o3 = GW_ADM_Page::singleton()->find('path="system"');
		$test_arr = [5,10,11];
		$o3->info = $test_arr;
		$o3->updateChanged();
		
		$o4 = GW_ADM_Page::singleton()->find('path="system"');
		
		$this->assertEquals($o4->info, $test_arr);
		
		$o4->info = ['test'=>'abc'];
		$o4->updateChanged();
			
		
		
		
		//d::dumpas($o->changed_fields);
		
		
		//d::ldump([$o->get('info/test'), $o->info->test, $info->test]);

		//test
		
		
	}
}