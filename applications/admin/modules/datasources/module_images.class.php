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
		$this->jump();
	}


	function viewCrop()
	{
		return parent::viewForm();
	}
	
	function doCrop()
	{
		$item = $this->getDataObjectById();
		$cropdata = json_decode($_POST['data'], true);
		$item->cropSelf($cropdata);
		
		$this->jump();
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
