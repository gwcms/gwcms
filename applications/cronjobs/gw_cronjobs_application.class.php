<?php

class GW_CronJobs_Application extends GW_Application
{
	public $path_arr;
	public $handler;
	
	
	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
	}
	
	function init()
	{
		$this->initDB();
		$this->loadConfig();
		
		$task_name = array_shift($this->path_arr);
		$class_name = "GW_{$task_name}_Job";
		
		$dir =& GW::s('DIR');

		
		$this->handler = new $class_name(Array('path_arr'=>$this->path_arr));
		$this->handler->init();
		$this->handler->app = $this;
		$this->handler->task_name = $task_name;
	}
	
	function process()
	{
		$this->handler->process();
	}
}