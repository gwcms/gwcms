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
	
	function montonioCfg()
	{
		$this->modconfig->preload('');
		return (object)$this->modconfig->exportLoadedValsNoPrefix();
	}
	
	function checkSellerCfg($order, &$cfg)
	{
		if($order->seller_id){
			list($access_key, $secret_key) = explode('|', $order->seller->get('keyval/montonio_config'));
			$cfg = (object)['access_key'=>$access_key, 'secret_key'=>$secret_key, 'sandbox'=>0];
		}
	}

	
	function doRefund()
	{
		$paylog =  $this->getDataObjectById();
		$order = $paylog->order;
		
		if(!$order){
			$this->setError('Order not found for selected Montonio log');
			$this->jump();
		}
		
		$payinfo = $paylog->data_array ?: [];
		$order_uuid = $payinfo['uuid'] ?? false;
		
		if(!$order_uuid){
			$this->setError('Montonio order UUID not found in payment log');
			$this->jump();
		}
		
		if((int)$order->payment_status === 9 && (int)$order->status === 9){
			$this->setError('Order already refunded');
			$this->jump();
		}
		
		$cfg = $this->montonioCfg();
		$this->checkSellerCfg($order, $cfg);
		
		$api = new GW_PayMontonio_Api2($cfg);
		$refund_amount = (float)($paylog->received_amount ?: $order->amount_total);
		$refund_reference = 'refund-'.$order->id.'-'.$paylog->id.'-'.date('YmdHis');
		
		try{
			$response = $api->createRefund($order_uuid, $refund_amount, $refund_reference);
		}catch(Exception $e){
			$this->setError($e->getMessage());
			$this->jump();
		}
		
		$comment = "Refunded via Montonio on ".date('Y-m-d H:i:s')
			."\nAmount: ".number_format($refund_amount, 2, '.', '')." EUR"
			."\nUUID: ".$order_uuid
			."\nRefund reference: ".$refund_reference
			."\nDetails: ".json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		$order->fireEvent('BEFORE_CHANGES');
		$order->set('extra/refund/requested_at', date('Y-m-d H:i:s'));
		$order->set('extra/refund/amount', $refund_amount);
		$order->set('extra/refund/refund_reference', $refund_reference);
		$order->set('extra/refund/uuid', $order_uuid);
		$order->set('extra/refund/api_response', $response);
		$order->set('extra/refund/pending_comment', $comment);
		$order->updateChanged();
		
		$url = Navigator::backgroundRequest('admin/'.$this->app->ln.'/payments/ordergroups?'.http_build_query([
			'id' => $order->id,
			'act' => 'doMarkAsRefundSystem',
			'sys_call' => 1,
			'refund_amount' => $refund_amount,
			'refund_reference' => $refund_reference,
			'refund_uuid' => $order_uuid,
		]));
		
		$this->setMessage("Refund started for order #{$order->id}. Bg call: ".$url);
		$this->jumpAfterSave();
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
