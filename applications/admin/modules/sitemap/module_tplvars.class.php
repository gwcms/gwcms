<?php

include_once __DIR__.'/gw_tplvar.class.php';

class Module_TplVars extends GW_Common_Module
{
	use Module_Import_Export_Trait;	
	
	function init()
	{				
		$this->filters['template_id']=(int)$this->app->path_arr[1]['data_object_id'];
		
		$this->model=new GW_TplVar();
		
		parent::init();
	}

	function viewDefault()
	{
		$this->viewList();
	}
	
}