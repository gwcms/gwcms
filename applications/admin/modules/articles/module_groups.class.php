<?php


class Module_Groups extends GW_Common_Module
{	

/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */
	
	
	function viewOptions()
	{
		$opts = $this->model->getOptions(false);
		
		echo json_encode($opts);
		exit;
	}


	function init(){
		parent::init();
		
		
		$this->app->carry_params=['group_id'=>1,'clean'=>1, 'type'=>1];
		
	}
	
		
	
}
