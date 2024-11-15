<?php


class Module_Articles extends GW_Common_Module
{	
	
	
	public $multisite = true;
	
	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		$this->options['group_id'] = GW_Articles_Group::singleton()->getOptions(false);
						
	}
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		//dont show at first time
		//foreach(['group_id','duration','insert_time','update_time','id'] as $field)
		//	$cfg['fields'][$field] = str_replace('L', 'l', $cfg['fields'][$field]);
		
		$cfg['fields']["image"] = "L";
		$cfg['fields']["short"] = "lof";
		$cfg['fields']["text"] = "lof";
		
		return $cfg;
	}	
	

	function __eventAfterList(&$list)
	{
		
	}


 
}
