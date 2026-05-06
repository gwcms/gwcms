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

	function buildMontonioMerchantReference($order)
	{
		$prefix = $order->seller_id ? $order->seller_id.'-' : '';
		$idx = 1;
		
		if(class_exists('GW_Order_Payment_Confirmation') && GW_Order_Payment_Confirmation::tableExists()){
			$idx += (int)GW_Order_Payment_Confirmation::singleton()->count(['order_id=?', (int)$order->id]);
		}else{
			$idx += (int)gw_payuniversal_log::singleton()->count(['method=? AND order_id=?', 'montonio', (int)$order->id]);
		}
		
		return $prefix.$order->id.'-p'.$idx.'-a'.date('YmdHis').mt_rand(100, 999);
	}

	function parseMontonioMerchantReferenceOrderId($reference)
	{
		$reference = (string)$reference;
		
		if(preg_match('/^(?:\d+-)?(\d+)-p\d+(?:-a\d+)?$/', $reference, $m))
			return (int)$m[1];
		
		if(preg_match('/^(\d+)$/', $reference, $m))
			return (int)$m[1];
		
		if(preg_match('/^\d+-(\d+)$/', $reference, $m))
			return (int)$m[1];
		
		if(preg_match('/(\d+)/', $reference, $m))
			return (int)$m[1];
		
		return 0;
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
			'merchant_reference'               => $this->buildMontonioMerchantReference($args->order),
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

		
		
		if($this->app->user && $this->app->user->isRoot() || GW::ip()=='90.131.42.149' || GW::ip()=='88.223.24.240'){
			
			$payload = $this->rootConfirmJson($payload);
			if(!$payload)
				return false;
			
		}		
		
		$url = $api->getRedirectLink2($payload);
		
		
		Navigator::jump($url);

	}
	

	
	function doMontonioAcceptV2()
	{
		$this->log($_SERVER['REQUEST_URI'] .'   '.GW::ip());
		
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
		if(!in_array(GW::ip(), ['35.156.245.42','35.156.159.169'])){
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
		$log->order_id = $this->parseMontonioMerchantReferenceOrderId($pay->merchant_reference);
		$log->data = json_encode($pay);
		$log->received_amount = $pay->grandTotal;
		$log->unique_key = 'M.'.$pay->uuid.'-'.$pay->paymentStatus;
		
		
		if($existing_log = gw_payuniversal_log::singleton()->find(['unique_key=?', $log->unique_key])){
			if($existing_log->processed){
				$this->log("mokejimas neiskaitytas nes jau yra rastas paylogas $log->unique_key, gautas mokejimo paketas: ".json_encode($pay));
				
				GOTO sFinish;
			}
			
			$this->log("mokejimas bus apdorotas is neprocessed paylogo $existing_log->id: ".json_encode($pay));
			$log = $existing_log;
		}else{
			$log->insert();
		}
		
		
		$order = $this->getOrder(true);
				
		if($this->app->user && $this->app->user->isRoot() && isset($_GET['debugconfirm'])){
					
			$this->confirm("<pre>".json_encode($pay, JSON_PRETTY_PRINT).'</pre>');
		}
		
		if($pay->paymentStatus === 'PAID'){
			if(!$order)
				d::dumpas("MONTONIO ERROR NO ORDERID RECEIVED");
			
			$received_amount = (float)$pay->grandTotal;
			$mark_amount = $received_amount;
			$is_root_test_cent_payment = $this->app->user && $this->app->user->isRoot() && $received_amount == 0.01;
				
			if($is_root_test_cent_payment){
				if(method_exists($order, 'recalcPaymentLedger'))
					$order->recalcPaymentLedger(false);
				
				$mark_amount = (float)$order->balance_amount > 0
					? (float)$order->balance_amount
					: (float)$order->amount_total;
			}
			
			$args = [
			    'id'=>$order->id,
			    'rcv_amount'=>$mark_amount,
			    'pay_type'=>'montonio',
			    'log_entry_id'=>$log->id
			];	
			
			if($log->test_ipn || (float)$log->received_amount != (float)$mark_amount || $is_root_test_cent_payment)
				$args['paytest']=1;

			
			
			if($this->app->user && $this->app->user->isRoot() && isset($_GET['debugconfirm'])){
				
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
		$this->doMontonioAcceptV2();
	}
	
	function doMontonioPay($args){
		$this->doMontonioPayV2($args);
	}
	
	
	function doMontonioRetryProcess($log=false)
	{
		if(!$log)
			$log = gw_payuniversal_log::singleton()->find($_GET['id']);
		
		if($log->data_array['paymentStatus']!='PAID')
			return d::ldump("Skip $log->id. paymentStatus!=PAID");
		
		$order = GW_Order_Group::singleton()->find(['id=?', $log->order_id]);	
		$received_amount = (float)$log->received_amount;
		$mark_amount = $received_amount;
		$is_root_test_cent_payment = $this->app->user && $this->app->user->isRoot() && $received_amount == 0.01;
		
		if($is_root_test_cent_payment){
			if(method_exists($order, 'recalcPaymentLedger'))
				$order->recalcPaymentLedger(false);
			
			$mark_amount = (float)$order->balance_amount > 0
				? (float)$order->balance_amount
				: (float)$order->amount_total;
		}
		
		$args = [
			    'id'=>$order->id,
			    'rcv_amount'=>$mark_amount,
			    'pay_type'=>'montonio',
			    'log_entry_id'=>$log->id
			];
		
		if($is_root_test_cent_payment)
			$args['paytest'] = 1;
		
		$this->setMessage("Order {$order->id} retry {$log->received_amount} Eur".($mark_amount != $received_amount ? " as $mark_amount Eur test payment" : ""));
		
		$markaspayd = $this->markAsPaydSystem($args);	
			
		$log->processed = 1;
		$log->updateChanged();
		
		if(isset($_GET['debug']))
			d::dumpas(['packet'=>$log, 'mark_as_payd'=>$markaspayd]);
	}
	
	function log($msg)
	{
		file_put_contents(GW::s('DIR/LOGS').'montonio.log', date('Y-m-d H:i:s').': '.$msg."\n", FILE_APPEND);
	}	
	

	
}
