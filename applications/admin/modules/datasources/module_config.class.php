<?php


class Module_Config  extends GW_Common_Module
{	
	
	function init()
	{
		parent::init();
				
		$this->app->carry_params['clean']=1;
		$this->app->carry_params['type']=1;
		
	
	}
	

	

}
