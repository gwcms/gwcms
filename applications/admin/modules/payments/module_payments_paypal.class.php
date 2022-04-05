<?php


class Module_Payments_Paypal extends GW_Common_Module
{	
	
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
