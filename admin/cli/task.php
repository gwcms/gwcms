#!/usr/bin/php
<?php

include __DIR__.'/../init_basic.php';


//task.php defered on_new_message //used to register task from other system 
//task.php on_new_message //used for testing, good to see outpur in realtime

if(is_numeric($argv[1])) //Tasks stored in DataBase
{
	$app = new GW_Tasks_App($argv[1]);
	
	if($argv[2]=='wrap')
		$app->runSeparate();
	else
		$app->runInside();
		
}else{ // direct run without storing in DataBase

	if($argv[1]=='defered'){
		GW_Task::addSingleStatic($argv[2]);
	}else{
		//preg replace hacks
		$task_id = preg_replace('/[^a-z0-9_-]/i','',$argv[1]);
		
		
		if(file_exists($f=__DIR__.'/tasks/'.$task_id.".run.php")){
			include $f;
		}elseif($f=__DIR__.'/tasks/'.$task_id.".task.class.php"){
			include $f;
			
			//try this shell_exec("$dir/task.php test --param1=a --param2=b")
			GW_Tasks_App::runDirect($task_id, GW_App_Base::parseParams());
		}
	}

}
