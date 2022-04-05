<?php




class Module_mergedpaymethods extends GW_Common_Module
{	
	
	function init()
	{	
		$this->model = GW_Pay_Methods::singleton();
		parent::init();
		
		
		if(!isset($_GET['country'])){
			$this->list_params['paging_enabled']=1;	
		}else{
			$this->filters['country']=$_GET['country'];
			$backurl = $this->buildUri(false);
			$otherulr = $this->buildUri(false, ['act'=>'doManageCountry']);
			$countryname = GW_Country::singleton()->getCountryByCode($_GET['country'],$this->app->ln);
		
			$this->setMessage("Managing <b>$countryname</b> use drag drop rows to change order. <a href='{$backurl}' class='btn btn-sm btn-default'>Back</a> <a  class='btn btn-sm btn-primary' href='$otherulr'>Other country</a>");
			
		}

		


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

	
	function getCountryOpt()
	{
		$countries0 = GW_Country::singleton()->getOptions($this->app->ln == 'lt' ? 'lt': 'en');	

		$countries = [];
		$active_country = GW_Pay_Methods::singleton()->getDistinctVals('country');
		foreach($active_country as $cc)
			$countries[strtoupper($cc)] = $countries0[strtoupper($cc)] ?? $cc;
		
		return $countries;
	}
	
	function doManageCountry()
	{
		
		$form = ['fields'=>['country'=>['type'=>'select', 'options'=>$this->getCountryOpt(), 'required'=>1]],'cols'=>1];

		
		if(!($answers=$this->prompt($form, $form)))	
			return false;
		

		
		$this->jump(false, ['country'=>$answers['country']]);
	}
	
	
	
	
}
