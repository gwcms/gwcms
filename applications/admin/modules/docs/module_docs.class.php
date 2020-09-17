<?php


class Module_Docs extends GW_Common_Module
{	

	use Module_Import_Export_Trait;		
	
	
	function init()
	{	
		parent::init();
		
		$this->model = GW_Doc::singleton();
		
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

	
	function viewDefault()
	{
		$this->viewList();
	}

	
	
	function getListConfig()
	{
		
		$cfg = parent::getListConfig();
		
		$cfg["fields"]['insert_time'] = 'lof';
		$cfg["fields"]['update_time'] = 'lof';
		//$cfg["fields"]['priority'] = 'lof';
		
		return $cfg;
	}	


		
	
	
	
	
	
	
	function __eventBeforeDelete($item)
	{
		if($item->protected)
		{
			$this->setError("Cant delete protected item");
		}
		
	}
	
	//function __eventAfterForm()
	//{
	//	d::dumpas('test');
		
	//}
	
	function doTest()
	{
		d::dumpas('test');
	}
	
	
	
	function doOpenInSite()
	{
		$id = $_GET['id'];
		
		Header('Location: '.Navigator::getBase().$this->app->ln.'/direct/docs/docs/item?id='.$id);
	}
	
}
