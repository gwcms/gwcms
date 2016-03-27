<?php

class Module_Config extends GW_Module
{	
	function init()
	{
		$this->config = new GW_Config('sys/');
		parent::init();
	}
	
	function viewDefault()
	{
		$this->app->jumpToFirstChild();
	}
}

