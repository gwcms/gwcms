<?php


class Module_Payments_Paysera extends GW_Common_Module
{	
	
	function init()
	{	
		$this->model = GW_Paysera_Log::singleton();
		parent::init();
		
		$this->list_params['paging_enabled']=1;			
	}
	
/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */	
	
	function getOptionsCfg()
	{
		$opts = [
			'search_fields'=>['p_firstname','p_lastname','id'],
		];	
		
		
		return $opts;	
	}	
}
