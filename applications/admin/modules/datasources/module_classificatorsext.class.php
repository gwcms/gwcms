<?php


class Module_ClassificatorsExt  extends GW_Common_Module
{	
	public $import_add_filters=true;
	use Module_Import_Export_Trait;
	
	function init()
	{
		parent::init();
		$this->options['classtypes'] = GW_Classificator_Types::singleton()->getOptions();
		//$this->options['classtypeskey'] = 
		
		$this->app->carry_params['clean']=1;
		$this->app->carry_params['pick']=1;
		$this->app->carry_params['type']=1;
		
		
		if(isset($_GET['type']) && !is_numeric($_GET['type'])){
			if($classtype=GW_Classificator_Types::singleton()->find(['`key`=?', $_GET['type']])){
				$_GET['type']=$classtype->id;
			}
			if(!$classtype){
				
				$new = GW_Classificator_Types::singleton()->createNewObject(['key'=>$_GET['type']]);
				
				//$sitelangs = GW::s('LANGS');
				//foreach($sitelangs  as $ln)
				//	$new->set("title_{$ln}", $_GET['type']);
				$new->title = $_GET['type'];
				
				$new->insert();
				$_GET['type'] = $new->id;
			}
		}
		

		
		//if( $this->app->path_arr[1]['path_clean']=='datasources/classificator_types' &&  ($tmp=$this->app->path_arr[1]['data_object_id']??false) ){
		//	$_GET['type'] = $tmp;
		//}			
		
		if(isset($_GET['type'])){
			$this->filters['type'] = $_GET['type'];
		}
		
			
		
		if(isset($_GET['group'])){
			
			$group = $this->getGroupByKey();
			
			$this->filters['type'] = $group->id;
		}	
		
		//d::dumpas($this->filters['type']);
	}
	
	function getGroupByKey()
	{
		$group = GW_Classificator_Types::singleton()->find(['`key` =?', $_GET['group']]);
		if(!$group){

			$group = GW_Classificator_Types::singleton()->createNewObject([
			    'title'=>$_GET['group'],
			    'key'=>$_GET['group'],
				]);

			$group->insert();
		}	
		return $group;
	}
	
	function getOptionsCfg()
	{
		
		if(isset($_GET['type']))
		{
			$opts['condition_add']=GW_DB::prepare_query(GW_DB::buidConditions(['type'=>(int)$_GET['type']]));
		}
		
		if(isset($this->filters['type'])){
			
			
			$opts['condition_add']=GW_DB::prepare_query(GW_DB::buidConditions(['type' => $this->filters['type'] ]));
		}
		
		
		
		return $opts;	
	}
	
	
	
	function doImportOnePerLine()
	{
		
		$form = ['fields'=>['rows'=>['type'=>'textarea', 'required'=>1]],'cols'=>1];

		
		if(!($answers=$this->prompt($form, GW::l('/m/ONE_PER_LINE'))))		
			return false;
		
		
		$rows = explode("\n", $answers['rows']);
		$cnt = 0;
		
		foreach($rows as $row)
		{
			if(!$row)
				continue;
			
			$item = new GW_Classificators;
			$item->set('title',$row);
			
			if($this->filters)
				$item->setValues($this->filters);
			
			$item->active=1;
			$item->priority = $cnt;
			
			$item->insert();
			$cnt++;
		}
		$this->setMessage("New entries cnt: $cnt");
		$this->jump();
	}
	
	
	function getListConfig()
	{

		
		
		$cfg = parent::getListConfig();
				
		foreach($this->app->langs as $ln)
			$cfg['inputs']['title_'.$ln]=['type'=>'text'];	
		
		$cfg['inputs']['key']=['type'=>'text'];	
		$cfg['inputs']['type']=['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'datasources/classificator_types'];
		//$cfg['inputs']['aka']=['type'=>'text'];	
		
		if(isset($this->filters['type'])){
			unset($cfg['fields']['type']);
		}		
		

		return $cfg;
	}
	
	function __eventBeforeInsert($item)
	{
		if(!$item->user_id)
			$item->user_id = $this->app->user->id;
	}
}
