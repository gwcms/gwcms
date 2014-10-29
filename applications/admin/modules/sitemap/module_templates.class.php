<?php

include_once __DIR__.'/gw_template.class.php';

class Module_Templates extends GW_Common_Module
{	
	function init()
	{	
		$this->model=new GW_Template();	
		
		parent::init();
	}

	function viewDefault()
	{
		$this->viewList();
	}
}