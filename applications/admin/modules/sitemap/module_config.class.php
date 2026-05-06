<?php


class Module_Config extends GW_Module_Config_Common
{	
	function init()
	{		
		$this->model = new GW_Config($this->module_path[0].'/');
		
		$this->options['user_groups'] = GW_Users_Group::singleton()->getOptions();
			
		parent::init();
	}
	function viewInvoice()
	{
		return $this->viewDefault();
	}	
	
	function viewEmailTemplates()
	{
		return $this->viewDefault();
	}		
	
}
