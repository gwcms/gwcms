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
	
	
	
	function doVolodymyrMove()
	{
		$vars = parent::viewList();
		
		foreach($vars['list'] as $item){
			
			$tr = GW_Translation::singleton()->find(['`key`=? AND module=?',$item->key,$item->module]);
			if(!$tr)
			{
				d::ldump("Cant find {$item->fullkey}");
				continue;
			}
			
			$tr->set("value_ua", $item->value_ru);
			//if(GW::ln($item->))
			$tr->update();
			d::ldump("{$item->key} : {$item->value_ru}");
		}
		
		//d::dumpas(count($vars['list']));
		
	}
	
	function doSaveTrans()
	{
		list($module, $key) = GW_Translation::fullkeyToModAndKey($_REQUEST['key']);
		
		$i0 = GW_Translation_Over::singleton();
		$lang = str_replace('/[^a-z]/','',$_REQUEST['ln']);
		
		
		list($ctx_group, $ctx_id) = explode("/", $_REQUEST['context']);
		
		$trans = $i0->find(["`key`=? AND `module`=? AND context_group=? AND context_id=?", $key, $module, $ctx_group, $ctx_id]);
		
		if(!$trans){
			$t = $i0->createNewObject([
			    'key'=>$key,
			    'module'=>$module, 
			    "value_$lang"=>$_REQUEST['new_val'], 
			    'context_group'=>$ctx_group,
			    'context_id'=>$ctx_id
			]);
			
			$t->insert();
			$method = "insert";
		}else{
			$trans->saveValues(["value_$lang"=>$_REQUEST['new_val']]);
			$method = "update";
		}
		
		$replace_what = GW::s("SITE_URL");
		
		$resp = ['status'=>"ok", 'method'=>$method];
		
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV)
		{
			initEnviroment(GW_ENV_PROD);
			$url = GW::s("SITE_URL").$_SERVER['REQUEST_URI'].'?'. http_build_query($_POST);			
			$resp['prod_request'] = $url;
		}
			
		
		
		
		
		
		die(json_encode($resp));
	}	
	
}
