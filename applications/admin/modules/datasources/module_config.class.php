<?php


class Module_Config  extends GW_Common_Module
{	
	use Module_Import_Export_Trait;	
		
	function init()
	{
		parent::init();
				
		$this->app->carry_params['clean']=1;
		$this->app->carry_params['type']=1;
		
	
	}
	

	

}
