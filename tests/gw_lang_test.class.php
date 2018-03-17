<?php


class gw_lang_test extends GW_TestClass
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
		//todo: 
		//padaryt
		
		///naujas vertimas
		GW_Lang::$ln = "lt";
		GW_Lang::setCurrentApp('site');
		GW::$devel_debug = true;
		
		include GW::s('DIR/APPLICATIONS').'site/config/main.php';
		
		header('Content-type: text/html; charset=utf-8');
		
		$trans = GW_Translation::singleton()->find("module='G/application' AND `key`='BANANAS'");
		$trans->saveValues(['value_lt'=>'yra greitai virskinamas','value_en'=>'fast digestion']);
		

		
		print_r([
		    'naujas'=>GW::ln("/g/TESTTRANSLATIONS", "lt:test new translation"),
		    'change_ln_lt'=>GW::ln("/LN/lt/g/BANANAS"),
		    'change_ln_en'=>GW::ln("/LN/en/g/BANANAS"),
		]);
		
		
		
		
		if($trans = GW_Translation::singleton()->find("module='G/application' AND `key`='TESTTRANSLATIONS'"))
		{
			$trans->delete();
		}
		
		exit;
		
		
	}
	

	
	
}