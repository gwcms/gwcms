<?php

ini_set('html_errors', false);

class GW_Service_Application extends GW_Application
{
	public $path_arr;
	public $handler;
	
	
	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
	}
	
	function init()
	{
		$service_name = array_shift($this->path_arr);
		$class_name = 'GW_'.$service_name.'_service';
		
		$dir =& GW::s('DIR');
		$dir['AUTOLOAD'][] = __DIR__;
		
		//include __DIR__.'/config/main.php';
		
		$this->handler = new $class_name(Array('path_arr'=>$this->path_arr));
		$this->handler->app = $this;
		$this->handler->name = $service_name;
		$this->handler->init();
		
		
		$this->initAuth();
		$this->handler->user = $this->user;
	}
	
	function process()
	{
		$this->handler->process();
	}
}