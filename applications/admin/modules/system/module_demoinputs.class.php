<?php


class Module_DemoInputs extends GW_Module_Config_Common
{	
	function init()
	{
		$this->model = new GW_Config($this->module_path[0].'/');
		
		parent::init();
	}

	
	function viewDefault()
	{
		$this->model->preload('demo_');
		return parent::viewDefault();
	}
	
	
	
	
	
	function __afterSave(&$vals)
	{
		//;
	}
}


