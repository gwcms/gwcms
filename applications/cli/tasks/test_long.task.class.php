<?php

class Test_Long_Task extends GW_Tasks_App
{	
	
	var $max_execution_time=10;
	
	function process()
	{
		passthru('ping localhost -c 300');
	}
}