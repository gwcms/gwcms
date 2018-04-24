<?php


class gw_lang_test extends GW_TestClass
{
	
	function __construct($testclass) {
		$this->init();
	}
	
	function init()
	{
		GW::db();
		
		GW_Lang::$ln = "lt";
		GW_Lang::setCurrentApp('site');
		GW::$devel_debug = true;
		
		include GW::s('DIR/APPLICATIONS').'site/config/main.php';		
	}
	
	
	function testLang()
	{
		//todo: 
		//padaryt
		
		///naujas vertimas

		
		$trans = GW_Translation::singleton()->find("module='G/application' AND `key`='BANANAS'");
		$trans->saveValues(['value_lt'=>'yra greitai virskinamas','value_en'=>'fast digestion']);
		
		$this->assertEquals(GW::ln("/g/TESTTRANSLATIONS", "lt:test new translation"), "test new translation");
		$this->assertEquals(GW::ln("/LN/en/g/BANANAS"), "fast digestion");
		$this->assertEquals(GW::ln("/LN/lt/g/BANANAS"), "yra greitai virskinamas");
		
				
		
		if($trans = GW_Translation::singleton()->find("module='G/application' AND `key`='TESTTRANSLATIONS'"))
		{
			$trans->delete();
		}	
		
	}
	
	
	
	function testArrayMerge()
	{
		$xml = GW::ln("/g/TEST_ARR_MERGE");
		$db = GW::l("/g/TEST_ARR_MERGE");
		
		
		if($trans = GW_Translation::singleton()->find("module='G/application' AND `key`='TEST_ARR_MERGE/THIRD'"))
		{
			$trans->delete();
		}else{
			$this->assertEquals("Translation not found", "maybe it is removed");
		}	
		
		
		
		
		
		
		header('Content-type: text/html; charset=utf-8');
		d::ldump([$db, $xml]);
		
	}
	

	
	
}