<?php




class Module_Payments_Revolut extends GW_Common_Module
{	
	
	function init()
	{	
		$this->model = GW_PayRevolut_Log::singleton();
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
}
