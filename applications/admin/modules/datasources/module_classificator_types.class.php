<?php


class Module_Classificator_Types  extends GW_Common_Module
{	
	
	function getListConfig()
	{

		
		
		$cfg = parent::getListConfig();

		$cfg['fields']['count'] = 'L';	

		return $cfg;
	}
	
	
	function __eventAfterList(&$list)
	{
		//foreach($list as $contest)
		//	$contest->participants_count = IPMC_Competition_Participant::singleton()->count('competition_id='.(int)$contest->id);
		
		
		$results = GW::db()->fetch_assoc("SELECT type,count(*) FROM `".GW_Classificators::singleton()->table."` GROUP BY type", 0);
		
		foreach($list as $item)
			$item->count = $results[$item->id] ?? 0;		
		
		
				
		//d::dumpas($list);
		//jei nerado paveiksleliu tai reiskias per paskiau neisitrauks i cache ir dar karta uzklausa darys
	}	
	
	
	function getOptionsCfg()
	{
		$opts = [];
		
		if(isset($_GET['type']))
		{
			$opts['condition_add']=GW_DB::prepare_query(GW_DB::buidConditions(['type'=>(int)$_GET['type']]));
		}
		
		
		
		return $opts;	
	}
}
