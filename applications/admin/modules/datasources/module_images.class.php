<?php


class Module_Images extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		
	}
	function doRotate() {
		
		$item = $this->getDataObjectById();
		$item->rotate(0);
		$item->deleteCached();
		$this->app->jump();
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
