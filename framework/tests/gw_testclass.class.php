<?php


class GW_TestClass
{
	public $test_result=[];
	public $test_class_present = true;
	public $info=[];
	
	function __construct($testclass)
	{	
	
		if($this->test_class_present)
			$this->testobj = new $testclass;
		
		if(method_exists($this, 'init'))
			$this->init();
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
		
		if($this->info)
			$this->test_result['info'] = $this->info;
		
		
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
	
	function addInfo($key, $val)
	{
		$this->info[$key] = $val;
	}
	
	
	function initDB()
	{
		GW::db();
	}
	
	function initAdminAutoLoad()
	{
		
		GW_Autoload::addAutoloadDir(GW::s('DIR/ADMIN/ROOT').'/lib');
		
		
	}
}