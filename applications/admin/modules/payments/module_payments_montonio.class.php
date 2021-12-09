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
	
	
	function doSyncPayMethods()
	{
		$this->modconfig->preload('');
		$sett = $this->modconfig->exportLoadedValsNoPrefix();
		$sett['sandbox']=0;
		$t = new GW_Timer;
			
		$api = new GW_PayMontonio_Api((object)$sett);
		$methods = $api->getBanks();
		
		//if($methods)
		//	GW_Pay_Methods::singleton()->deleteMultiple('gateway="montonio"');		
		$cnt=0;
			
		foreach($methods as $countrycode => $list){
			foreach($list as $entry)
			{
				$pm = new stdClass();
				$pm->gateway = 'montonio';
				$pm->country = strtolower($countrycode);
				$pm->key = $entry->bic;
				$pm->logo = $entry->logo_url;
				$pm->title = $entry->name;
				//$pm->insert();
				$rows[] = (array)$pm;
				$cnt++;
			}
		}
		
		GW_Pay_Methods::singleton()->multiInsert($rows);
		//d::dumpas(GW::db()->last_query);
		
		$this->setMessage("time {$t->stop()} count: $cnt");
	}	
}
