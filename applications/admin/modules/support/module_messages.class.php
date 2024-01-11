<?php

class Module_Messages extends GW_Common_Module 
{

	function init() 
	{
		

		parent::init();
		$this->cfg = new GW_Config($this->module_path[0].'/');
				
		$this->list_params['paging_enabled']=1;
		
		if(isset($_GET['user_id'])){
			$this->filters['user_id'] = $_GET['user_id'];
			//$this->userObj = GW_Customer::singleton()->createNewObject($_GET['user_id'], true);
		}		
		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['user_id'] = 1;		
	
	}
	
	function viewDefault()
	{
		$this->viewList();
	}
	

}

?>
