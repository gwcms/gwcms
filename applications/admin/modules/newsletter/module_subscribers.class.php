<?php


class Module_Subscribers extends GW_Common_Module
{	

	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;		
		
	}

	
	function viewDefault()
	{
		$this->viewList();
	}
}
