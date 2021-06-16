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
	
	function __eventAfterList(&$list){
		
		
		
		$optids = [];
		foreach($list as $item)
			if($item->options_src)
				$optids[$item->options_src]=1;
			
		if($optids){
			GW_Composite_Data_Object::prepareLinkedObjects($list, 'optionsgroup');
			$this->tpl_vars['classificator_type_cnt'] = GW::db()->fetch_assoc(
				"SELECT type,count(*) FROM `".GW_Classificators::singleton()->table."` "
				. "WHERE ".GW_DB::inCondition('type', array_keys($optids))." "
				. "GROUP BY type", 0);
			
		}
		

		
		
		
	}	
	
	
	
	
}