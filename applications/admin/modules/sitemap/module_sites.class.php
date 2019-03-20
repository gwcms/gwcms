<?php



class Module_Sites extends GW_Common_Module
{	

	public $default_view = 'list';
	
	function init()
	{
		
		
		parent::init();	
	}	
	
	
	function viewDefault()
	{
		
	}
	
	
	
	
	function getListConfig()
	{
		
		//d::dumpas();
		
		$cfg = array('fields' => []);
		
		
						

		
		
		$cfg["fields"]["id"]="Lof";

		$cfg["fields"]["title"]="Lof";
		$cfg["fields"]["hosts"]="Lof";
		//$cfg["fields"]["admin_host"]="Lof";
					
		
		
		$cfg["fields"]['relations']='L';		
		$cfg["fields"]['update_time'] = 'lof';
		$cfg["fields"]['priority'] = 'lof';
		
		return $cfg;
	}
	
	
}
