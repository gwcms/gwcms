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
	{	$err =[];
	
		if(!$this->__assertTrue($testval == $expectedval, $err)){

			$err['val']=$testval;
			$err['expected_val']=$expectedval;
		}
	}
	
	function __assertTrue($state, &$err=false){
		
		$callee = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	
		
		
		if($state)
		{
			@$this->test_result['success']++;
			//@$this->test_result[$callee['function']]['success']++;
		}else{
			@$this->test_result['fail']++;
			
			
			$lines = file_get_contents($callee[1]['file']);
			$lines = explode("\n", $lines);
			$linenr=$callee[1]['line'];
			$line = trim($lines[ $linenr-1 ]);
			
			//d::dumpas([$lines, $linenr, $callee]);
			
			
			
			$err = ['func' => $callee[2]['function'],'line'=>$line, 'lineno'=>$linenr, 'file'=>$callee[1]['file'], 'testmeth'=>$callee[1]['function']];
			
			@$this->test_result['fails'][] =& $err;
		}

		return $state;
	}
	
	function assertTrue($state)
	{
		$this->__assertTrue($state);
	}
}