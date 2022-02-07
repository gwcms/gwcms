<?php

class pay_kevin_module_ext extends GW_Module_Extension
{
	
	function doKevinPay($args) 
	{
		//$this->userRequired();

		$cfg = new GW_Config("payments__payments_kevin/");	
		$cfg->preload('');
				
		
		if(isset($args->user)){
			$user = $args->user;
		}else{
			$user = $this->app->user;
		}
		
		
		$handler = $args->handler ?? "orders";
		
		//if($user->id == 9)
		//	$args->payprice= 0.01;		

	

		$options = ['error' => 'array', 'version' => '0.3'];

		$kevinClient = new Kevin\Client($cfg->clientId, $cfg->clientSecret, $options);


		if($user->id == 9)
			$args->payprice= 0.01;

		$attr = [
			'Redirect-URL' => $args->base.$this->app->ln."/direct/orders/orders?act=doKevinAccept&action=R",
			//testavau webhooka
			//'Redirect-URL' => $args->base.$this->app->ln."/direct/orders/orders?id={$args->order->id}&orderid={$args->order->id}",
		    'Webhook-URL' => $args->base.$this->app->ln."/direct/orders/orders?act=doKevinAcceptByOrder&id={$args->order->id}&orderid={$args->order->id}&key={$args->order->secret}&action=N",
		    'description' => $args->orderid,
		    'currencyCode' => 'EUR',
		    'amount' => $args->payprice,
		    'bankPaymentMethod' => [
			'endToEndId' => $args->order->id,
			'creditorName' => $cfg->creditorName,
			'creditorAccount' => [
			    'iban' => $cfg->creditorAccount_iban
			],
		    ],
		];
		$response = $kevinClient->payment()->initPayment($attr);
		
		if(isset($response['error'])){
			$errordetails = ['resp'=>$response, 'request'=>$attr];
			if($this->app->user && $this->app->user->isRoot()){
				d::dumpas($errordetails);
			}else{
				$opts=[
				    'subject'=>GW::s('PROJECT_NAME').' kevin create payment error',
				    'body'=>
				    '<pre>'.json_encode($errordetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)];
				
				GW_Mail_Helper::sendMailDeveloper($opts);
				$this->setError(GW::ln('/g/PAYMENT_GATEWAY_ERROR'));
				$this->jump();
			}
			
			
		}
		

		$log=GW_PayKevin_Log::singleton()->createNewObject();
		$log->order_id =  $args->order->id;
		$log->kevin_id = $response['id'];
		$log->bankStatus = $response['bankStatus'];
		$log->insert();
		
		header('Location: '.$response['confirmLink']);
		exit;
	/*
Array
(
    [id] => a419834275abfc0836e75f824f52b06004041602
    [bankStatus] => STRD
    [statusGroup] => started
    [confirmLink] => https://psd2.kevin.eu/login/a419834275abfc0836e75f824f52b06004041602
)
 */		

	}
	
	function kevinUpdate($paylog, $wait=true)
	{
		$cfg = new GW_Config("payments__payments_kevin/");	
		$cfg->preload('');		
		$options = ['error' => 'array', 'version' => '0.3'];
		$kevinClient = new Kevin\Client($cfg->clientId, $cfg->clientSecret, $options);
		

		/*
 Array
(
    [id] => e1bbc2f3385c5d0b7af659d4357918f9b16a7180
    [bankStatus] => ACSC
    [statusGroup] => completed
    [amount] => 0.1
    [currencyCode] => EUR
    [description] => Test abc
    [bankPaymentMethod] => Array
        (
            [creditorName] => badmintonocentras
            [endToEndId] => 1
            [informationStructured] => 
            [creditorAccount] => Array
                (
                    [iban] => LT654010051005527062
                    [currencyCode] => EUR
                )

            [debtorAccount] => Array
                (
                    [iban] => LT417300010103766329
                    [currencyCode] => XXX
                )

            [bankId] => SWEDBANK_LT
            [paymentProduct] => DOMESTIC_SEPA_CREDIT_TRANSFERS
            [requestedExecutionDate] => 2021-10-06T14:40:44.000Z
        )

)
 */		
		
		$cnt=0;
		
		while($cnt<30){
			$response = $kevinClient->payment()->getPayment($paylog->kevin_id);
			
			//$this->log(json_encode(['date'=>date('Y-m-d H:i:s'),'response'=>$response], JSON_PRETTY_PRINT));
			if(!$wait || $response['statusGroup'] == 'completed')
				$this->log(json_encode(['date'=>date('Y-m-d H:i:s'),'response'=>$response], JSON_PRETTY_PRINT));			
			
			if($response['statusGroup'] == 'completed'){
				break;
			}
			


			if(!$wait)
				break;
			
			$this->log("waiting for {$paylog->kevin_id} $cnt/30 response {$response['statusGroup']}");
			
			sleep(3);
			$cnt++;
			
		}
		
		//uzkraukim loga patikrint ar dar nesuprocesintas
		$paylog->load();
		
		
		//jei cia atejo per narsykle ir yra jau laukimo procese
		//ir taip pat foninis pranesimas atejo apie bukles pakeitima
		//kad neivyktu dvigubas atnaujinimas
		if($paylog->processed ==1 || $paylog->order->payment_status == 7)
			return false;
		
		
		$paylog->wait = $cnt;
		
		

		
		$paylog->bankStatus = $response['bankStatus'];
		$paylog->statusGroup = $response['statusGroup'];
		$paylog->amount = $response['amount'];
		$paylog->currencyCode = $response['currencyCode'];
		$paylog->description = $response['description'];
		$paylog->pm_creditorName = $response['bankPaymentMethod']['creditorName'];
		$paylog->pm_endToEndId = $response['bankPaymentMethod']['endToEndId'];
		
		$paylog->pm_creditorAccount_iban = $response['bankPaymentMethod']['creditorAccount']['iban'];
		$paylog->pm_creditorAccount_currencyCode = $response['bankPaymentMethod']['creditorAccount']['currencyCode'];
		
		$paylog->pm_debtorAccount_iban = $response['bankPaymentMethod']['debtorAccount']['iban'] ?? '';
		$paylog->pm_debtorAccount_currencyCode = $response['bankPaymentMethod']['debtorAccount']['currencyCode'] ?? '';
		
		$paylog->pm_bankId = $response['bankPaymentMethod']['bankId'];
		$paylog->pm_paymentProduct = $response['bankPaymentMethod']['paymentProduct'];
		$paylog->pm_requestedExecutionDate = $response['bankPaymentMethod']['requestedExecutionDate'];
		
		
		
		$paylog->info = ($paylog->info ? $paylog->info.';':''). $_GET['action'].'_'.date('H:i:s');
		
		$paylog->updateChanged();
		
		
		
		if($response['statusGroup'] == 'completed' && $paylog->processed !=1) // isskaityti tik pilnai priimta mokejima ir tik viena karta
		{
			
			$order = $paylog->order;
		
			$args = [
			    'id'=>$paylog->order_id,
			    'rcv_amount'=>$paylog->amount,
			    'pay_type'=>'kevin',
			    'log_entry_id'=>$paylog->id
			];


			//test case
			if($paylog->amount=='0.01'){
				$args['paytest']=1;
				$args['rcv_amount'] = $order->amount_total;

				$paylog->test = 1;
				$paylog->updateChanged();
			}
		
			if($order->amount_total == $paylog->amount || $paylog->test ){
				$this->markAsPaydSystem($args);
			}else{
				$debugdata = ['response'=>$response,'paylog'=>$paylog->toArray()];
				$mail=[
					'subject'=>'Payment error amount_total in cart does not match kevin response',
					'body'=>"<pre>".json_encode($debugdata, JSON_PRETTY_PRINT)."</pre>"
				    ];
				GW_Mail_Helper::sendMailDeveloper($mail);
			}
			
			$paylog->processed = 1;
			$paylog->updateChanged();			
		}
		
	
		
		return $response;
	}
	
	function doKevinAccept()
	{
		$paymentId = $_GET['paymentId'];
		
		$this->logRequest();
		
		$paylog = GW_PayKevin_Log::singleton()->find(['kevin_id=?', $paymentId]);
		
		if(!$paylog){
			$this->setError(GW::ln('/m/INVALID_KEVIN_ARGS'));	
			$this->app->jump('direct/orders/orders');
		}
		
		//penkis kartus kad back pagalba neuzsoktu
		if($paylog->statusGroup!='completed' || $paylog->processed!=1)
			$response = $this->kevinUpdate($paylog);
		
		if($response['statusGroup'] == 'failed'){
			$this->setError(GW::ln('/m/PAYMENT_FAILED'));
		}elseif($response['statusGroup'] == 'completed'){
			sleep(2);
		}
		
		$this->redirectAfterPaymentAccept($paylog->order);

	}
	
	function logRequest()
	{
		$entry = json_encode(['date'=>date('Y-m-d H:i:s'),'get'=>$_GET,'post'=>$_POST,'server'=>$_SERVER], JSON_PRETTY_PRINT);
		$this->log($entry);
	}
	
	function log($msg)
	{
		file_put_contents(GW::s('DIR/LOGS').'kevin.log', $msg, FILE_APPEND);
	}
	
	function doKevinAcceptByOrder()
	{
		$order = $this->getOrder(true);
		
		$this->logRequest();
		
		if(!$order)
		{
			$this->log('ORDER not found');
		}
		
		$list = GW_PayKevin_Log::singleton()->findAll(['order_id=? AND processed=0', $order->id]);
		
		if(isset($_GET['debug']))
			d::dumpas($list);
		
		foreach($list as $paylog){
			$this->kevinUpdate($paylog, $wait=false);
		}
	}
	
}
