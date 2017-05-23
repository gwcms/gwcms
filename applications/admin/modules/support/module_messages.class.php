<?php

class Module_Messages extends GW_Common_Module 
{

	function init() 
	{
		

		parent::init();
		$this->cfg = new GW_Config($this->module_path[0].'/');
				
		$this->list_params['paging_enabled']=1;
	
	}
	
	function viewDefault()
	{
		$this->viewList();
	}
	

}

?>
