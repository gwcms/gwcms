<?php



class Module_Membership extends GW_Common_Module
{	

	use Module_Import_Export_Trait;
	

		
	function init()
	{
		parent::init();
		
		$this->list_params['paging_enabled']=1;
	}
	
	
	function __eventAfterList($list)
	{		
		$this->attachFieldOptions($list, 'user_id', 'GW_User');	
	}		

	

	
	//function doItaxCo
}
