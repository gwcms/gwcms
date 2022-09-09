<?php

include_once __DIR__.'/module_genericcassificator.class.php';
class Module_prodtypes extends Module_GenericClassificator
{	
	
	
	function init()
	{
		parent::init();
		
		$fields = GW_Adm_Page_Fields::singleton()->findAll(['parent=?', Shop_Products::singleton()->table]);	
		
		
		$opts = [];
		foreach($fields as $field)
			if($field->type=='optional')
				$opts[$field->fieldname] = $field->title;	
		
			
		
		$this->options['fields'] = $opts;
		
	}
	
	
	function doCounts()
	{
	
		
		
		$t = new GW_Timer;
		$affected = 0;
	
		$results = GW::db()->fetch_rows("SELECT `type`,count(*) FROM shop_products WHERE active=1 AND qty>0 GROUP BY `type`", 0);

		//header('Content-type: text/plain');
		$rows = [];

		foreach($results as $row)	
			$rows[$row[0]]=['count'=>$row[1]];


		//GW::db()->multi_insert(nat_p_instrumentation::singleton()->table, $rows, true);
		GW::db()->query("UPDATE `".Shop_ProdTypes::singleton()->table."` SET `count`=0");
		GW::db()->updateMultiple(Shop_ProdTypes::singleton()->table, $rows);

		if(GW::db()->error){

			$this->setError(GW::db()->error.' '.GW::db()->error_query);
		}
		$affected = GW::db()->affected();

		
		
		
		$s = $t->stop();
		$this->setMessage("Done counting. Affected: $affected. Speed: $s s");
		
		if($this->sys_call){
			exit;
		}
		$this->jump();		
	}	
	
	
	
	
	function getListConfig($item=false)
	{
		
		$cfg = parent::getListConfig();

		


		
		$cfg['inputs']['title']=['type'=>'text'];	
		$cfg['inputs']['aka']=['type'=>'text'];	
		$cfg['inputs']['fields']=['type'=>'multiselect', 'options'=>$this->options['fields']];	
		
		
		
		

		//d::dumpas($cfg['inputs']);

		
		return $cfg;
	}	
	
}




