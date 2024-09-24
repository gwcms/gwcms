<?php




class Module_Payments_Montonio extends GW_Common_Module
{	
	
	function init()
	{	
		$this->model = gw_paymontonio_log::singleton();
		parent::init();
		
		$this->list_params['paging_enabled']=1;	



		$this->addRedirRule('/^doMontonio|^viewMontonio|^montonio/i',['options','pay_montonio_module_ext']);	
		
		$this->item_remove_log=1;
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
			'title_func' =>function($itm){ return $itm->title; }
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
	
	
	function initApiV1()
	{
		$this->modconfig->preload('');
		$sett = $this->modconfig->exportLoadedValsNoPrefix();
		$sett['sandbox']=0;
		$t = new GW_Timer;
			
		$api = new GW_PayMontonio_Api((object)$sett);		
		return $api;
	}
	

	function doSyncPayMethods()
	{
		$this->modconfig->preload('');
		$sett = $this->modconfig->exportLoadedValsNoPrefix();
		$sett['sandbox']=0;
		$t = new GW_Timer;
			
		
		
		if($this->modconfig->version==2){
		
			$api = new GW_PayMontonio_Api2((object)$sett);
			$data = $api->getBanks();

			//d::dumpas([$sett,$methods]);

			$methods = $data->paymentMethods->paymentInitiation->setup;
			//d::Dumpas($methods);

			if($methods)
				GW_Pay_Methods::singleton()->deleteMultiple('gateway="montonio"');		
			$cnt=0;

			foreach($methods as $countrycode => $countrymethods){
				foreach($countrymethods->paymentMethods as $entry)
				{
					//d::dumpas($entry);
					if(!in_array('EUR', $entry->supportedCurrencies)){
						continue;
					}

					$pm = new stdClass();
					$pm->gateway = 'montonio';
					$pm->country = strtolower($countrycode);
					$pm->key = $entry->code;
					$pm->logo = $entry->logoUrl;
					$pm->title = $entry->name;
					$pm->insert_time = date('Y-m-d H:i:s');
					//$pm->insert();
					$rows[] = (array)$pm;
					$cnt++;
				}
			}

			GW_Pay_Methods::singleton()->multiInsert($rows);
			//d::dumpas(GW::db()->last_query);

			$this->setMessage("time {$t->stop()} count: $cnt");
		}else{
			$api = $this->initApiV1();
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
	
	
	function doMontonioRetryProcessSeries()
	{
		$items = $this->getDataObjectByIds();
		
		//d::Dumpas($items);
		
		$cnt = 0;
		foreach($items as $item){
			$this->doMontonioRetryProcess($item);
			$cnt++;
		}
		
		$this->setMessage('retry process: '.$cnt);
	}
	
	//del sio doMontonioRetryProcessSeries
	function markAsPaydSystem($args)
	{
		$url=Navigator::backgroundRequest($urlreq='admin/lt/payments/ordergroups?act=doMarkAsPaydSystem&sys_call=1&'. http_build_query($args));
		return $urlreq;
	}	
	
	
}
