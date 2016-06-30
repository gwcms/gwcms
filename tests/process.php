<?php

include __DIR__.'/../init_basic.php';


$autoload =& GW::s('DIR/AUTOLOAD');
$autoload[] = __DIR__;


$list_tests = glob(__DIR__.'/*.class.php');


$result = [];

foreach($list_tests as $file)
{
	
	$file = basename($file);
	
	$class = preg_replace('/.class.php$/', '', $file);
	$testclass = preg_replace('/_test$/', '', $class);
	

	$timer = new GW_Timer();
	$t  = new $class($testclass);
	$testrez = $t->process();
	
	@$result['fail']+=$testrez['fail'];
	@$result['success']+=$testrez['success'];
	$result['data'][$class] = $testrez;
	
	
	$result['speed'] = $timer->stop(5);

	
}

header('Content-type: text/plain');
echo json_encode(@$result, JSON_PRETTY_PRINT);