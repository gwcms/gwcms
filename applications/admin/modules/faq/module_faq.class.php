<?php


class Module_FAQ extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		//$this->options['group_id'] = GW_Articles_Group::singleton()->getOptions(false);
						
	}



 
}
