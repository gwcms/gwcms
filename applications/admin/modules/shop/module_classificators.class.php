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
	
	
	function doCounts()
	{
		$class_map = Shop_Classificator_Types::singleton()->getAssoc(['id','key']);
		
		$classif = GW::db()->fetch_assoc("SELECT `type`,count(*) FROM shop_classificators GROUP BY `type`", 0);
		
		
		
		
		
		$t = new GW_Timer;
		$affected = 0;
		
		foreach($classif as $id => $nevermind){
			$field = GW_DB::escapeField($class_map[$id]);
			$results = GW::db()->fetch_rows("SELECT $field,count(*) FROM shop_products WHERE active=1 AND qty>0 GROUP BY $field", 0);
			
			//header('Content-type: text/plain');
			$rows = [];

			foreach($results as $row)	
				$rows[$row[0]]=['count'=>$row[1]];


			//GW::db()->multi_insert(nat_p_instrumentation::singleton()->table, $rows, true);
			GW::db()->query("UPDATE `".Shop_Classificators::singleton()->table."` SET `count`=0");
			GW::db()->updateMultiple(Shop_Classificators::singleton()->table, $rows);

			if(GW::db()->error){

				$this->setError(GW::db()->error.' '.GW::db()->error_query);
			}
			$affected += GW::db()->affected();
		}
		
		
		
		$s = $t->stop();
		$this->setMessage("Done counting. Affected: $affected. Speed: $s s");
		
		
		if($this->sys_call){
			exit;
		}
		$this->jump();
	}
}
