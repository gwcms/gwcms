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

	

}

