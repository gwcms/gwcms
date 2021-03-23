<?php

include_once __DIR__.'/module_genericcassificator.class.php';
class Module_Classificators  extends Module_GenericClassificator
{	
	
	function init()
	{
		parent::init();
		$this->options['classtypes'] = Shop_classificator_Types::singleton()->getOptions();
	}
	
	function getOptionsCfg()
	{
		
		if(isset($_GET['type']))
		{
			$opts['condition_add']=GW_DB::prepare_query(GW_DB::buidConditions(['type'=>(int)$_GET['type']]));
		}
		
		if(isset($_GET['group'])){
			$group = Shop_Classificator_Types::singleton()->find(['`key` =?', $_GET['group']]);
			if(!$group){
				
				$group = Shop_Classificator_Types::singleton()->createNewObject([
				    'title'=>$_GET['group'],
				    'key'=>$_GET['group'],
					]);
				
				$group->insert();
			}
			
			$opts['condition_add']=GW_DB::prepare_query(GW_DB::buidConditions(['type' => $group->id ]));
		}
		
		
		
		return $opts;	
	}
}
