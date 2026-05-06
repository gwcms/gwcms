<?php


class Module_Config extends GW_Module_Config_Common
{	
	function init()
	{
		$this->model = new GW_Config($this->module_path[0].'/');
		
		parent::init();
	}
}
