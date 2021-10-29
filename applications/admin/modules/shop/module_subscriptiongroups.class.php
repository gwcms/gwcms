<?php


class Module_SubscriptionGroups extends GW_Common_Module
{
	use Module_Import_Export_Trait;

	/**
	 * @var GW_Product
	 */

	function init()
	{
		$this->initLogger();

		$this->config = new GW_Config($this->module_path[0].'/');
		$this->features = array_fill_keys((array)json_decode($this->config->features), 1);
		
		parent::init();
		$this->model = Shop_SubscriptionGroups::singleton();
		$this->mod_fields = GW_Adm_Page_Fields::singleton()->findAll(['parent=?', $this->model->table]);
		
		
	
		$this->list_params['paging_enabled']=1;
		
		$this->addRedirRule('/^doImport|^viewImport/i','import');
		
		

		
		
		$this->app->carry_params['clean'] = 1;


	}

	function doAddPeriod()
	{
		$form = ['fields'=>[
			'date_start'=>['type'=>'date', 'required'=>1],
			'date_end'=>['type'=>'date','required'=>1],
			'buy_enable_date'=>['type'=>'date','required'=>1],
			'qty'=>['type'=>'number','required'=>1]
		    ],'cols'=>4];
		
		
		
		$answers=$this->prompt($form, GW::l('/g/ACTION_REQUESTS_ADDITIONAL_INPUT'));

		
		if(!$answers)
			return false;		
		
		$titles = [];
		
		foreach(Shop_SubscriptionGroups::singleton()->findAll() as $group)
		{
			$titles[]=$group->title;
			
			$period = Shop_Subscription_Period::singleton()->createNewObject();
			$period->group_id = $group->id;
			$period->date_start = $answers['date_start'];
			$period->date_end = $answers['date_end'];
			$period->buy_enable_date = $answers['buy_enable_date'];
			$period->qty = $answers['qty'];
			$period->insert();
		}
		
		
		$this->setMessage("Period $period->date_start - $period->date_end groups: ".implode(',',$titles));
				
	}	

	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		$cfg['fields']["image"] = "L";
		//$cfg['fields']["changetrack"] = "L";
		$cfg['fields']["orders"] = "L";
		$cfg['fields']["period"] = "L";
		
		
		return $cfg;
	}		


	
	function prepareCounts($list) {
		
		$ids = array_keys($list);
		
		
		if($this->isListEnabledField("period")){
			$this->tpl_vars['counts']['period'] = $this->model->getChildCounts('Shop_Subscription_Period', $ids);
			
		}
		
		parent::prepareCounts($list);
	}
	

}

