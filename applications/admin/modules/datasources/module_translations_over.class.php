<?php


class Module_Translations_Over extends GW_Common_Module
{	
	use Module_Import_Export_Trait;	
	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		$this->app->carry_params['owner_key']=1;
		$this->app->carry_params['clean']=1;
		
		
		if(isset($_GET['owner_key']))
		{
			list($this->filters['context_group'], $this->filters['context_id']) = explode('/', $_GET['owner_key']);
		}

		//if(!isset($this->list_params['order']))
		//	$this->list_params['order'] = "";
		
		if(isset($_GET['transsearch']))
		{
			list($group,$module, $key) = explode('/',$_GET['transsearch'],3);
			$module = $group."/".$module;
			
			$this->replaceFilter("module", $module, "EQ");		
			$this->replaceFilter("key", $key, "EQ");	
			unset($_GET['transsearch']);
			$this->app->jump();
		}
		
		
		
		
	}
	
	
	
	function getListConfig()
	{
		
		//d::dumpas();
		
		$cfg = array('fields' => []);
		
		
						
		if(!isset($this->filters['context_group']))
			$cfg["fields"]["context_group"]="Lof";
		
		if(!isset($this->filters['context_id']))
			$cfg["fields"]["context_id"]="Lof";
		
		
		$cfg["fields"]["id"]="lof";
		$cfg["fields"]["fullkey"]="Lof";
		
		if($this->view_name == "form" && !isset($_GET['form_ajax']))
		{
			$cfg["fields"]["value"]="Lof";
			
			
		}else{
			
			foreach(GW::s("LANGS") as $lang)
				$cfg["fields"]["value_".$lang]="Lof";			
		}
		

			
		
		$cfg["fields"]['update_time'] = 'lof';
		$cfg["fields"]['priority'] = 'lof';
		
		return $cfg;
	}	
}
