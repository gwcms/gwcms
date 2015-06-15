<?php


class Module_Groups extends GW_Common_Module
{	

	function init()
	{
		$this->model = new GW_Articles_Group();
		
		parent::init();
	}

	
	function viewDefault()
	{
		$this->viewList();
	}
}
