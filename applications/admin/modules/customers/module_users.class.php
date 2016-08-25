<?php

class Module_Users extends GW_Common_Module 
{

	function init() 
	{
		//$this->filters['id'] = isset(GW::$app->path_arr[1]['data_object_id']) ? GW::$app->path_arr[1]['data_object_id'] : false;

		parent::init();
		$this->cfg = new GW_Config($this->module_path[0].'/');
		
		
		$this->rootadmin = $this->app->user->isRoot();
		
		if(!$this->rootadmin){
			$this->filters['parent_user_id'] = $this->app->user->id;
		}
		
		$this->options['parent_user_id'] = GW::getInstance('GW_User')->getOptions(false);		
		
		$this->options['sms_pricing_plan']=GW::getInstance('GW_Pricing_Item')->getAllPricingPlans();
	}
	
	function viewDefault()
	{
		$this->viewList();
	}
	
	function eventHandler($event, &$context) 
	{
		switch($event)
		{
			case "BEFORE_SAVE_0":
				
				$item = $context;
				
				if($item->id){
					$item->setValidators('update');
				}else{
					$item->setValidators('insert');
					$item->group_ids = [$this->cfg->customer_group];
					
					$item->parent_user_id = $this->app->user->id;
				}
				
				
				
			break;
		}
		
		parent::eventHandler($event, $context);
	}


	

}

?>
