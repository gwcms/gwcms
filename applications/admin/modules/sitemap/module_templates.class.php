<?php

include_once __DIR__.'/gw_template.class.php';

class Module_Templates extends GW_Common_Module
{	
	
	use Module_Import_Export_Trait;	
	
	function init()
	{	
		$this->model=new GW_Template();	
		
		parent::init();
	}

	function viewDefault()
	{
		$this->viewList();
	}
	
	function __eventAfterList(&$list)
	{
		foreach($list as $item)
			$item->tplvars_count = GW_TplVar::singleton()->count('template_id='.(int)$item->id);
	}	
}