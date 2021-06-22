<?php


class Module_Classificators  extends GW_Common_Module
{	
	
	function init()
	{
		parent::init();
		$this->options['classtypes'] = GW_Classificator_Types::singleton()->getOptions();
		
		$this->app->carry_params['clean']=1;
		$this->app->carry_params['type']=1;
		
		if( ($tmp=$this->app->path_arr[1]['data_object_id']??false) ){
			$_GET['type'] = $tmp;
		}			
		
		if(isset($_GET['type'])){
			$this->filters['type'] = $_GET['type'];
		}
		
		
		if(isset($_GET['group'])){
			
			$group = $this->getGroupByKey();
			
			$this->filters['type'] = $group->id;
		}	
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

}
