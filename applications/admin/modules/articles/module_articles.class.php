<?php


class Module_Articles extends GW_Common_Module
{	

	public $default_view='viewList';
	
	function init()
	{	
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
}
