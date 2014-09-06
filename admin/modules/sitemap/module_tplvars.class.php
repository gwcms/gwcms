<?php

include_once __DIR__.'/gw_tplvar.class.php';

class Module_TplVars extends GW_Common_Module
{
	function init()
	{				
		$this->filters['template_id']=(int)GW::$request->path_arr[1]['data_object_id'];
		
		$this->model=new GW_TplVar();
		
		parent::init();
	}

	function viewDefault()
	{
		$this->viewList();
	}
}