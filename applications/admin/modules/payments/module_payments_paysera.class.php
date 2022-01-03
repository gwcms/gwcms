<?php


class Module_Payments_Paysera extends GW_Common_Module
{	
	
	function init()
	{	
		$this->model = GW_Paysera_Log::singleton();
		parent::init();
		
		$this->list_params['paging_enabled']=1;			
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
			'search_fields'=>['p_firstname','p_lastname','id'],
		];	
		
		
		return $opts;	
	}	


	function doSyncPayMethods()
	{
		//SELECT * FROM `gw_pay_methods`
		
		
		//$this->mod
	
		
		$paymentMethodsInfo = WebToPay::getPaymentMethodList($this->modconfig->paysera_project_id, $this->modconfig->default_currency_code);
		//$countries = $paymentMethodsInfo->getCountries();


		$t = new GW_Timer;
		
		$cnt = 0;
				
		$rows=[];
		
		foreach ($paymentMethodsInfo->getCountries() as $countrycode => $country) {
			foreach ($country->getGroups() as $groupkey =>  $group) {
				foreach ($group->getPaymentMethods() as $paymentMethod) {
			
					$pm = new stdClass();
					$pm->gateway = 'paysera';
					$pm->country = $countrycode;
					$pm->key = $paymentMethod->getKey();
					$pm->logo = $paymentMethod->getLogoUrl();
					$pm->title = $paymentMethod->getTitle();
					$pm->min_amount = $paymentMethod->minAmount/100;
					$pm->max_amount = $paymentMethod->maxAmount/100;
					$pm->group = $groupkey;
					$pm->insert_time = date('Y-m-d H:i:s');
					$rows[] = (array)$pm;
					$cnt++;
}
