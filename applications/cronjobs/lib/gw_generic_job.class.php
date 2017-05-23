<?php

class GW_Generic_Job 
{
	public $path_arr;
	public $admin=false;	
	public $app;
	public $task_name;
	public $messages; //for debuging
	
	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
		
	}
	
	
	function init()
	{
		
	}
	
	function process()
	{
		
	}
	
	function log($msg)
	{
		$str = '['.date('Ymd His')."][{$this->task_name}] $msg\n";
		$this->messages[] = $str;
		
		
		file_put_contents(GW::s('DIR/LOGS').'jobs_'.date('Ymd').'.log', $str, FILE_APPEND);
		
		if(isset($_GET['debug']))
			echo $str;
	}
	
	function outputMessages()
	{
		d::ldump(implode("\n",$this->messages));
	}
}