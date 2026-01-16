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
	
	function doMontonioPayV2($args) 
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
				
	
		$api = new GW_PayMontonio_Api2($cfg);
		
		$return_args="&id={$args->order->id}&orderid={$args->order->id}&key={$args->order->secret}";
		
		if($cfg->sandbox)
			$return_args.="&sandbox=1";
		
		
		$availcountries = ['lt'=>'lt', 'ee'=>'et', 'lv'=>'lv', 'pl'=>'pl'];
		$ln = isset($availcountries[$this->app->ln]) ? $availcountries[$this->app->ln] : 'en';
		
		$payment_data = [
			'amount'                           => $args->payprice,
			'currency'                         => 'EUR',
			'access_key'                       => $api->access_key,
			'merchant_reference'               => ($args->order->seller_id ? $args->order->seller_id.'-':'').$args->order->id,
			'merchant_return_url'              => $args->base.$this->app->ln."/direct/orders/orders?act=doMontonioAccept&action=return".$return_args,
			'merchant_notification_url'        => $args->base.$this->app->ln."/direct/orders/orders?act=doMontonioAccept&action=notify".$return_args,
			'payment_information_unstructured' => $args->paytext,
			//'preselected_aspsp'                => 'LHVBEE22',
			'preselected_locale'               => $ln,
			//'checkout_email'                   => 'vidmantas.work@gmail.com',
			'exp'                              => time() + (60 * 10), 
		];
		
		
		
		
		if($user && $user->email)
			$payment_data['checkout_email'] = $user->email;
			
		

		
		if($args->method ?? false){
			$payment_data['preselected_aspsp'] = $args->method;
		}
		


		

		
		//API V2
		$payload = [
		    'merchantReference' => $payment_data['merchant_reference'],
		    'returnUrl'         => $payment_data['merchant_return_url'],
		    'notificationUrl'   => $payment_data['merchant_notification_url'],
		    'currency'          => 'EUR',
		    'grandTotal'        => (float)$payment_data['amount'],
		    'locale'            => $payment_data['preselected_locale'],
		    /*
		    'billingAddress'    => [
			'firstName'    => 'CustomerFirst',
			'lastName'     => 'CustomerLast',
			'email'        => 'customer@customer.com',
			'addressLine1' => 'Kai 1',
			'locality'     => 'Tallinn',
			'region'       => 'Harjumaa',
			'country'      => 'EE',
			'postalCode'   => '10111',
		    ],
		    'shippingAddress'   => [
			'firstName'    => 'CustomerFirstShipping',
			'lastName'     => 'CustomerLastShipping',
			'email'        => 'customer@customer.com',
			'addressLine1' => 'Kai 1',
			'locality'     => 'Tallinn',
			'region'       => 'Harjumaa',
			'country'      => 'EE',
			'postalCode'   => '10111',
		    ],
		    'lineItems'         => [
			[
			    'name'       => 'Hoverboard',
			    'quantity'   => 1,
			    'finalPrice' => 99.99,
			],
		    ],
		     * 
		     */
		];

		// 2. Specify the Payment Method
		$payload['payment'] = [
		    'method'        => 'paymentInitiation',
		    'methodDisplay' => 'Pay with your bank',
		    'amount'        => (float)$payment_data['amount'], // Yes, this is the same as order->grandTotal.
		    'currency'      => 'EUR', // This must match the currency of the order.
		    'methodOptions' => [
			'paymentDescription' => $payment_data['payment_information_unstructured'],
			'preferredCountry'   => strtoupper($args->country ?? 'LT'),
			// This is the code of the bank that the customer chose at checkout.
			// See the GET /stores/payment-methods endpoint for the list of available banks.
			//'preferredProvider'  => 'HABALT22',
		    ],
		];		
		
		if($payment_data['preselected_aspsp'] ?? false)
			$payload['payment']['methodOptions']['preferredProvider'] = $payment_data['preselected_aspsp'];
		
		
		
		if($args->paytype=='montonio_cc'){
			$payload['payment']['method'] = "cardPayments";
			unset($payload['payment']['methodOptions']['preferredProvider']);
			
			
			if($args->method == 'wallet'){
				//google pay / apple pay
				$payload['payment']['methodOptions']['preferredMethod'] = 'wallet';
			}else{
				$payload['payment']['methodOptions']['preferredMethod'] = 'card';
			}
		}		

		
		
		if($this->app->user && $this->app->user->isRoot() || $_SERVER['REMOTE_ADDR']=='90.131.42.149' || $_SERVER['REMOTE_ADDR']=='88.223.24.240'){
			
			$payload = $this->rootConfirmJson($payload);
			if(!$payload)
				return false;
			
		}		
		
		$url = $api->getRedirectLink2($payload);
		
		
		Navigator::jump($url);

	}
	

	
	function doMontonioAcceptV2()
	{
		
		$orderid = $_GET['orderid'];
		$order = GW_Order_Group::singleton()->find(['id=?', $orderid]);	
		
		//default
		$cfg = $this->montonioCfg();

		//other seller
		$this->checkSellerCfg($order, $cfg);
		
		$api = new GW_PayMontonio_Api2($cfg);
		
		
		//https://docs.montonio.com/api/stargate/guides/orders#3-generating-the-jwt
		//Allowlist the following IPs to receive webhook notifications: 35.156.245.42 and 35.156.159.169
		
		
		/*
		if(!in_array($_SERVER['REMOTE_ADDR'], ['35.156.245.42','35.156.159.169'])){
			die("Montonio accept ip address whitelist problem");
		}
		
		*/
		

/*
if (
    $decoded->paymentStatus === 'PAID' &&
    $decoded->uuid === $montonioOrderId &&
    $decoded->accessKey === 'MY_ACCESS_KEY'
) {
    // Payment completed
} else {
    // Payment not completed
}		
	*/	
		
		$token = $api->decodeToken2($_GET['order-token']);
		$pay = $token['payload'];
		
		//d::dumpas($pay);
		
		$this->log(json_encode($pay));
		
		$log = new gw_payuniversal_log;
		$log->method = 'montonio';
		$log->order_id = $pay->merchant_reference;
		$log->data = json_encode($pay);
		$log->received_amount = $pay->grandTotal;
		$log->unique_key = 'M.'.$pay->uuid.'-'.$pay->paymentStatus;
		
		
		if(gw_payuniversal_log::singleton()->find(['unique_key=?', $log->unique_key])){
			$this->log("mokejimas neiskaitytas nes jau yra rastas paylogas $log->unique_key, gautas mokejimo paketas: ".json_encode($pay));
			
			GOTO sFinish;
		}
			
		$log->insert();
		
		
		$order = $this->getOrder(true);
				
		if($this->app->user && $this->app->user->isRoot()){
					
			$this->confirm("<pre>".json_encode($pay, JSON_PRETTY_PRINT).'</pre>');
		}
		
		if($pay->paymentStatus === 'PAID'){
			
			
			
			
			if(!$order)
				d::dumpas("MONTONIO ERROR NO ORDERID RECEIVED");
			
			
			$received_amount = $pay->grandTotal;
			
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
		

		sFinish:
			$this->redirectAfterPaymentAccept($order);
	}
	
	
	function doMontonioAccept()
	{
		$cfg = $this->montonioCfg();
		if($cfg->version == 2){
			$this->doMontonioAcceptV2();
		}else{
			$this->doMontonioAcceptV1();
		}		
	}
	
	function doMontonioPay($args){
		$cfg = $this->montonioCfg();
		if($cfg->version == 2){
			$this->doMontonioPayV2($args);
		}else{
			$this->doMontonioPayV1($args);
		}
	}

	function doMontonioPayV1($args) 
	{		
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
	
	function doMontonioAcceptV1()
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
	
	
	function doMontonioRetryProcess($log=false)
	{
		if(!$log)
			$log = gw_payuniversal_log::singleton()->find($_GET['id']);
		
		if($log->data_array['paymentStatus']!='PAID')
			return d::ldump("Skip $log->id. paymentStatus!=PAID");
		
		$order = GW_Order_Group::singleton()->find(['id=?', $log->order_id]);	
		
		$args = [
			    'id'=>$order->id,
			    'rcv_amount'=>$log->received_amount,
			    'pay_type'=>'montonio',
			    'log_entry_id'=>$log->id
			];
		
		$this->setMessage("Order {$order->id} retry {$log->received_amount} Eur");
		
		$markaspayd = $this->markAsPaydSystem($args);	
			
		$log->processed = 1;
		$log->updateChanged();
		
		if(isset($_GET['debug']))
			d::dumpas(['packet'=>$log, 'mark_as_payd'=>$markaspayd]);
	}
	
	function log($msg)
	{
		file_put_contents(GW::s('DIR/LOGS').'montonio.log', $msg, FILE_APPEND);
	}	
	

	
}
