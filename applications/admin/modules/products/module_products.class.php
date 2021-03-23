<?php


class Module_Products extends GW_Common_Module
{
	use Module_Import_Export_Trait;

	/**
	 * @var GW_Product
	 */

	function init()
	{
		$this->initLogger();

		$this->config = new GW_Config($this->module_path[0].'/');
		
		parent::init();
		$this->model = Shop_Products::singleton();
		$this->mod_fields = GW_Adm_Page_Fields::singleton()->findAll(['parent=?', $this->model->table]);
		
		
	
		$this->list_params['paging_enabled']=1;
		
		$this->addRedirRule('/^doImport|^viewImport/i','import');
		
		
		//is import in progress
		
		
		$this->filters['parent_id'] = $_GET['parent_id'] ?? 0;
		
		if($this->filters['parent_id']){
			$this->list_params['paging_enabled']=false;
		}
		
		
		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['parent_id'] = 1;

	}

	function __eventAfterList($list)
	{
		GW_Composite_Data_Object::prepareLinkedObjects($list, 'typeObj');
		
		
		$ids = array_keys($list);
		
		$cnts = Shop_Products::singleton()->getModCounts($ids);
		foreach($cnts as $pid => $cnt)
			$list[$pid]->mod_count = $cnt;
		
	}	

	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		$cfg['fields']["image"] = "L";
		$cfg['fields']["mod"] = "L";
		
		return $cfg;
	}		
	
	
	function doCreateModification()
	{
		$item = $this->getDataObjectById();
		$mod = $this->model->createNewObject();
		$mod->parent_id = $item->id;
		$mod->title = "Modification of ".$item->title;
		$mod->insert();
		$this->setMessage("Mofication was created");
		
		
		Navigator::jump($this->buildUri("$mod->id/form"));
	}
}

