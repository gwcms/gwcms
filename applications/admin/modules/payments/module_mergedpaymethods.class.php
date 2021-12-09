<?php




class Module_mergedpaymethods extends GW_Common_Module
{	
	
	function init()
	{	
		$this->model = GW_Pay_Methods::singleton();
		parent::init();
		
		$this->list_params['paging_enabled']=1;	



		$this->addRedirRule('/^doRevolut|^viewRevolut|^revolut/i',['options','pay_revolut_module_ext']);	
		
	}
	
/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */	
	
	function getOptionsCfg()
	{
		$opts = [
			'search_fields'=>['cardholder_name','email','id'],
		];	
		
		
		return $opts;	
	}	
	

	
	function doRefund()
	{
		
		$paylog =  $this->getDataObjectById();
		d::dumpas($paylog);
		
		d::dumpas($response);
	}
	
	function doUpdate()
	{
		
		$paylog =  $this->getDataObjectById();
		$resp = $this->revolutUpdate($paylog);
		
		d::dumpas($resp);
		
		$this->jump();
		
	}	
	
	
	function __eventBeforeConfig()
	{
		$this->options['gateway'] = GW_Pay_Methods::singleton()->getDistinctVals('gateway');
		$this->options['group'] = GW_Pay_Methods::singleton()->getDistinctVals('group');
		
	}
	
	function getMoveCondition($item)
	{
		$tmp = [];
		$tmp['country']=$item->get('country');		
		//$tmp['gateway']=$item->get('gateway');
		//$tmp['group']=$item->get('group');
		
		return GW_SQL_Helper::condition_str($tmp);
	}	
	
	
	
	
}
