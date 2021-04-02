<?php


class Module_Classificator_Types  extends GW_Common_Module
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
