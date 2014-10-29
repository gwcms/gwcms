<?php

class GW_Tools_Application extends GW_Application
{
	public $path_arr;
	public $handler;
	
	
	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
	}
	
	function init()
	{
		$class_name = 'GW_'.array_shift($this->path_arr).'_tool';
		
		$dir =& GW::s('DIR');
		$dir['AUTOLOAD'][] = __DIR__;
		
		$this->handler = new $class_name(Array('path_arr'=>$this->path_arr));
		$this->handler->app = $this;
		$this->handler->init();
	}
	
	function process()
	{
		$this->handler->process();
	}
}