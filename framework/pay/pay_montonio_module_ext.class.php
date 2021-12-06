<?php

//du kart tas pats merchant_reference negali but naudojamas

class pay_montonio_module_ext extends GW_Module_Extension
{
	
	function montonioCfg()
	{
		$cfg = new GW_Config("payments__payments_montonio/");	
		$cfg->preload('');
		return $cfg;
	}
	
	function doMontonioPay($args) 
	{
		//$this->userRequired();

				
		
		if(isset($args->user)){
			$user = $args->user;
		}else{
			$user = $this->app->user;
		}
		
		//if($user->id == 9)
		//	$args->payprice= 0.01;	
		
		

		$cfg = $this->montonioCfg();
		$api = new GW_PayMontonio_Api($cfg);
		$payment_data = [
			'amount'                           => $args->payprice,
			'currency'                         => 'EUR',
			'access_key'                       => $api->access_key,
			'merchant_reference'               => $args->order->id,
			'merchant_return_url'              => $args->base.$this->app->ln."/direct/orders/orders?act=doMontonioAccept&action=return&id={$args->order->id}&orderid={$args->order->id}&key={$args->order->secret}",
			'merchant_notification_url'        => $args->base.$this->app->ln."/direct/orders/orders?act=doMontonioAccept&action=notify&id={$args->order->id}&orderid={$args->order->id}&key={$args->order->secret}",
			'payment_information_unstructured' => $args->paytext,
			//'preselected_aspsp'                => 'LHVBEE22',
			'preselected_locale'               => 'lt',
			'checkout_email'                   => 'vidmantas.work@gmail.com',
			'exp'                              => time() + (60 * 10), 
		];
			
		
		if($args->paytype=='montonio_cc'){
			$payment_data['preselected_aspsp'] = "CARD";
		}


		
		if($this->app->user && $this->app->user->isRoot()){
					
			$this->confirm("<pre>".json_encode($payment_data, JSON_PRETTY_PRINT).'</pre>');
		}
		
		
		$url = $api->getRedirectLink($payment_data);
		
		
		Navigator::jump($url);

		

	}
	

	
	function doMontonioAccept()
	{
		$cfg = $this->montonioCfg();
		$api = new GW_PayMontonio_Api($cfg);
		$token = $api->decodeToken($_GET['payment_token']);
		$pay = $token['payload'];
		
		$log = new gw_payuniversal_log;
		$log->method = 'montonio';
		$log->order_id = $pay['merchant_reference'];
		$log->data = json_encode($pay);
		$log->received_amount = $pay['amount'];
		$log->insert();	
		
		$order = $this->getOrder(true);
		
		if($this->app->user && $this->app->user->isRoot()){
					
			$this->confirm("<pre>".json_encode($pay, JSON_PRETTY_PRINT).'</pre>');
		}
		
		if($pay['access_key']==$api->access_key && $pay['status'] == 'finalized'){
			
			
			
			
			if(!$order)
				d::dumpas("MONTONIO ERROR NO ORDERID RECEIVED");
			
			
			$received_amount = $pay['amount'];
			
			$args = [
			    'id'=>$order->id,
			    'rcv_amount'=>$order->amount_total,
			    'pay_type'=>'montonio',
			    'log_entry_id'=>$log->id
			];	
			
			
			
			
			if($log_entry->test_ipn || $order->amount_total != $received_amount)
				$args['paytest']=1;

			
			d::ldump($args);
			d::dumpas('mark as payd');
			
			$this->markAsPaydSystem($args);	
			
			$log->processed = 1;
			$log->updateChanged();
			
		}
		
		if($_GET['action']=='notify')
			exit;
		

		$this->redirectAfterPaymentAccept($order);
		
	}
	
	

	
}
