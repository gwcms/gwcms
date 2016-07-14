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
	
	
	function process()
	{
		$timer = new GW_Timer();
		
		$list = get_class_methods($this);
		
		foreach($list as $func)
		{
			if(strpos($func,'test')===0)
			{
				$this->$func();
			}
			
		}
		
		$this->test_result['speed'] = $timer->stop(5);
		
		return $this->test_result;
	}
	
	function assertEquals($testval, $expectedval)
	{
		
		
		$callee = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$callee = $callee[1];
		
		
		
		if($testval == $expectedval)
		{
			@$this->test_result['success']++;
			//@$this->test_result[$callee['function']]['success']++;
			
		}else{
			@$this->test_result['fail']++;
			@$this->test_result['fails'][] = ['func' => $callee['function'], 'testmeth'=>__FUNCTION__, 'val'=>$testval, 'expected_val'=>$expectedval];
		}
		
		
	}
}