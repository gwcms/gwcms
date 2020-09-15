<?php


class Module_Elements extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	
	function init()
	{
		$this->model = GW_Form_Elements::singleton();
		
		$this->app->carry_params['clean']=1;
		$this->parent_id=$this->app->path_arr[1]['data_object_id'];
		
		
		$this->filters['owner_id']=$this->parent_id;
		parent::init();
		
		
		
		$this->model->getTypes();
	}
	
	
	
}