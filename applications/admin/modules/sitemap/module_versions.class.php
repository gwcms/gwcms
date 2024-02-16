<?php


class Module_Versions  extends GW_Common_Module
{	
	public $import_add_filters=true;
	use Module_Import_Export_Trait;
	
	function init()
	{
		$this->model = new gw_sitemap_data_versions;
		parent::init();
		
		
		$this->app->carry_params['clean']=1;
		$this->app->carry_params['type']=1;
		$this->app->carry_params['page_id']=1;
		$this->app->carry_params['key']=1;
		$this->app->carry_params['ln']=1;
		
		
		
		
			
		
		if(isset($_GET['page_id'])){
			$this->filters['page_id'] = $_GET['page_id'];
		}
		
		if(isset($_GET['key'])){			
			$this->filters['key'] = $_GET['key'];
		}
		if(isset($_GET['ln'])){			
			$this->filters['ln'] = $_GET['ln'];
		}			
	}
	
	
	function getListConfig()
	{

		
		
		$cfg = parent::getListConfig();
		
		$cfg['fields'] = [];
		$cfg['fields']['key'] = 'Lof';
		$cfg['fields']['page_id'] = 'Lof';
		$cfg['fields']['ln'] = 'Lof';
		$cfg['fields']['content'] = 'Lof';
		$cfg['fields']['diff'] = 'Lof';
		$cfg['fields']['time'] = 'Lof';
				

		if(isset($this->filters['key'])){
			unset($cfg['fields']['key']);
		}
		
		if(isset($this->filters['ln'])){
			unset($cfg['fields']['ln']);
		}
		
		if(isset($this->filters['page_id'])){
			unset($cfg['fields']['page_id']);
		}
		
		$cfg['inputs']['key']=['type'=>'text'];	
		
		//$cfg['inputs']['aka']=['type'=>'text'];	

		return $cfg;
	}
	
	
	
	function __eventAfterListConfig()
	{
		if(isset($_GET['clean']) )
			$this->list_config['dl_filters'] = [];
	}
}
