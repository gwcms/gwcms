<?php


class Test_Task extends GW_Tasks_App
{	
	
	var $max_execution_time=10;
	
	function process()
	{
		dump("showing arguments");
		dump($this->data->arguments);
		
		
		//for testing long task
		if($this->data->arguments['long'])
			passthru('ping localhost -c 300');
		
		
		$this->error_message="Demo error message";
		$this->error_code=666;
	}
}