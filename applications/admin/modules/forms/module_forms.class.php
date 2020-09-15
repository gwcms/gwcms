<?php


class Module_Forms extends GW_Common_Module
{	

	use Module_Import_Export_Trait;		
	
	
	function init()
	{	
		parent::init();
		
		$this->model = GW_Forms::singleton();
		
		$this->list_params['paging_enabled']=1;	
		$this->app->carry_params['owner_type']=1;
		$this->app->carry_params['clean']=1;
		
		
		if(isset($_GET['owner_type']))
		{
			$this->filters['owner_type'] = $_GET['owner_type'];
		}
		
		if(isset($_GET['owner_field']))
		{
			$this->filters['owner_field'] = $_GET['owner_field'];
		}
	}

	

	
	function getListConfig()
	{
		
		$cfg = parent::getListConfig();
		
		$cfg["fields"]['insert_time'] = 'lof';
		$cfg["fields"]['update_time'] = 'lof';
		//$cfg["fields"]['priority'] = 'lof';
		
		return $cfg;
	}	


	function getOptionsCfg()
	{
		$opts = [
		    'title_func'=>function($item){ return $item->admin_title;  },
		    'search_fields'=>['title_lt','title_en','title_ru','admin_title']
		];	
		
		return $opts;	
	}
	
	
	
	function __eventAfterList(&$list)
	{
		foreach($list as $item){
			$item->element_count = GW_Form_Elements::singleton()->count('owner_id='.(int)$item->id);
			$item->answer_count = GW_Form_Answers::singleton()->count('owner_id='.(int)$item->id);
		}
		
	}
	
}
