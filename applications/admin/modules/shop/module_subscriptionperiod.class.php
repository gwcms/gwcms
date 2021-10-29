<?php


class Module_SubscriptionPeriod extends GW_Common_Module
{
	use Module_Import_Export_Trait;

	/**
	 * @var GW_Product
	 */

	function init()
	{
		$this->initLogger();

		
		parent::init();
		$this->model = Shop_Subscription_Period::singleton();
		
		
		if(isset($_GET['group_id']))
			$this->filters['group_id']  = $_GET['group_id'];
		
	
		$this->list_params['paging_enabled']=1;
		
		
		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['group_id'] = 1;
	}


	function getListConfig()
	{
		$cfg = parent::getListConfig();
		


		
		
		return $cfg;
	}		


	

	

}

