<?php

include_once __DIR__.'/module_genericcassificator.class.php';
class Module_Classificator_Types  extends Module_GenericClassificator
{	
	

	
	function getOptionsCfg()
	{
		
		if(isset($_GET['type']))
		{
			$opts['condition_add']=GW_DB::prepare_query(GW_DB::buidConditions(['type'=>(int)$_GET['type']]));
		}
		
		
		
		return $opts;	
	}
}
