<?php


class Module_Subscribers extends GW_Common_Module
{	

	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		$this->options['groups']=GW::getInstance('GW_NL_Groups')->getOptions();
		
	}

	
	function viewDefault()
	{
		$this->viewList();
	}
	
	function viewForm()
	{
		$item = parent::viewForm();
		
		//d::dumpas($item->groups);
	}
	

	
}
