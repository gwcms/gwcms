<?php




class Module_Payments_Montonio extends GW_Common_Module
{	
	
	function init()
	{	
		$this->model = gw_payuniversal_log::singleton();
		parent::init();
		
		$this->list_params['paging_enabled']=1;	



		$this->addRedirRule('/^doMontonio|^viewMontonio|^montonio/i',['options','pay_montonio_module_ext']);	
		
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
			'search_fields'=>['order_id','data'],
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
