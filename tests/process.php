<?php

include __DIR__.'/../init_basic.php';


$autoload =& GW::s('DIR/AUTOLOAD');
$autoload[] = __DIR__;

$testsdir = __DIR__.'/';
$list_tests = glob($testsdir.'*.class.php');

$result = [];

if(isset($_GET['file']))
{
	$index = array_search($testsdir.$_GET['file'], $list_tests);
	
	//$index === false - run all tests
	if($index!==false)	
		$list_tests = [$list_tests[$index]];
	

}else{
	echo "select test<br/><ul>";
	foreach($list_tests as $file)
	{
		$fn = basename($file);
		echo "<li><a href='?file=$fn'>$fn</a></li>";
		
	}
	echo "<li><a href='?file=all'>All</a></li></ul>";
	exit;
}



 

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