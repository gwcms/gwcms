<?php


class GW_TestService extends GW_TestClass
{
	public $test_result=[];
	
	function __construct($testclass)
	{
		$this->testobj = new GW_General_RPC;

		//get service name from test class name
		$servicename = preg_replace('/^gw_service_test_/','',get_called_class());
		
		$this->testobj->url = GW::s("SITE_URL") . 'service/'.$servicename;
		
				
		
		$this->init();
		
	}
	
	/**
	 * override it // use for authentification
	 */
	function init()
	{
		
	}
	

}