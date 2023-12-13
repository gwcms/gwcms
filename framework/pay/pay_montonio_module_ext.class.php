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
	
	
	function checkSellerCfg($order, &$cfg)
	{
		if($order->seller_id){
			
			list($access_key, $secret_key) = explode('|',$order->seller->get('keyval/montonio_config'));
			$cfg = (object)['access_key'=>$access_key, 'secret_key'=> $secret_key, 'sandbox'=>0];
			
			//d::dumpas($cfg);
		}
				
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
		
		
		$this->checkSellerCfg($args->order, $cfg);
				
	
		$api = new GW_PayMontonio_Api($cfg);
		
		$return_args="&id={$args->order->id}&orderid={$args->order->id}&key={$args->order->secret}";
		
		if($cfg->sandbox)
			$return_args.="&sandbox=1";
		
		$payment_data = [
			'amount'                           => $args->payprice,
			'currency'                         => 'EUR',
			'access_key'                       => $api->access_key,
			'merchant_reference'               => ($args->order->seller_id ? $args->order->seller_id.'-':'').$args->order->id,
			'merchant_return_url'              => $args->base.$this->app->ln."/direct/orders/orders?act=doMontonioAccept&action=return".$return_args,
			'merchant_notification_url'        => $args->base.$this->app->ln."/direct/orders/orders?act=doMontonioAccept&action=notify".$return_args,
			'payment_information_unstructured' => $args->paytext,
			//'preselected_aspsp'                => 'LHVBEE22',
			'preselected_locale'               => 'lt',
			//'checkout_email'                   => 'vidmantas.work@gmail.com',
			'exp'                              => time() + (60 * 10), 
		];
		
		if($user->email)
			$payment_data['checkout_email'] = $user->email;
			
		
		if($args->paytype=='montonio_cc'){
			$payment_data['preselected_aspsp'] = "CARD";
		}
		
		if($args->method ?? false){
			$payment_data['preselected_aspsp'] = $args->method;
		}
		


		
		if($this->app->user && $this->app->user->isRoot() || $_SERVER['REMOTE_ADDR']=='90.131.42.149'){
			
			$payment_data = $this->rootConfirmJson($payment_data);
			if(!$payment_data)
				return false;
			
		}
		
		
		$url = $api->getRedirectLink($payment_data);
		
		
		Navigator::jump($url);

		

	}
	

	
	function doMontonioAccept()
	{
		
		$orderid = $_GET['orderid'];
		$order = GW_Order_Group::singleton()->find(['id=?', $orderid]);	
		
		//default
		$cfg = $this->montonioCfg();

		//other seller
		$this->checkSellerCfg($order, $cfg);
		
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
			
			
			
			
			if($log->test_ipn || (float)$log->received_amount != (float)$received_amount)
				$args['paytest']=1;

			
			
			if($this->app->user && $this->app->user->isRoot()){
				
				if(!$this->confirm(json_encode(['received'=>$received_amount,'payload'=>$pay,'markasPaydSystem'=>$args], JSON_PRETTY_PRINT)))
					return false;

			}
			
			$this->markAsPaydSystem($args);	
			
			$log->processed = 1;
			$log->updateChanged();
			
		}
		
		if($_GET['action']=='notify')
			exit;
		

		$this->redirectAfterPaymentAccept($order);
		
	}
	
	

	
}
