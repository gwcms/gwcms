<?php


class Module_Default extends GW_Module
{	

	function init()
	{
		parent::init();
	}

	
	function viewDefault()
	{

	}

	function doSwitchUserReturn()
	{
		$this->app->auth->switchUserReturn();
		$this->jump();
	}	
	
}

