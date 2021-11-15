<?php

class pay_kevin_module_ext extends GW_Module_Extension
{
	
	function doPayKevin($args) 
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
		    'Redirect-URL' => $args->base.$this->app->ln."/direct/orders/orders?act=doPayKevinAccept",
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
	
	function doPayKevinAccept()
	{
		$paymentId = $_GET['paymentId'];
		
		$entry = json_encode(['date'=>date('Y-m-d H:i:s'),'get'=>$_GET,'post'=>$_POST,'server'=>$_SERVER], JSON_PRETTY_PRINT);
		file_put_contents(GW::s('DIR/LOGS').'kevin.log', $entry, FILE_APPEND);
		
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
			$response = $kevinClient->payment()->getPayment($paymentId);
			
			if($response['statusGroup'] == 'completed'){
				break;
			}
			
			if($response['statusGroup'] == 'failed'){
				$this->setError(GW::ln('/m/PAYMENT_FAILED'));
				header('Location:'.$this->buildURI('', ['id'=>$order->id]));
				exit;
			}	
			
			sleep(1);
			$cnt++;
			
		}
		
		
		$paylog = GW_PayKevin_Log::singleton()->find(['kevin_id=?', $paymentId]);
		$paylog->wait = $cnt;
		
		
		if(!$paylog){
			$this->setError(GW::ln('/m/INVALID_KEVIN_ARGS'));
			
			$this->app->jump('direct/orders/orders');
		}
		
		$paylog->bankStatus = $response['bankStatus'];
		$paylog->statusGroup = $response['statusGroup'];
		$paylog->amount = $response['amount'];
		$paylog->currencyCode = $response['currencyCode'];
		$paylog->description = $response['description'];
		$paylog->pm_creditorName = $response['bankPaymentMethod']['creditorName'];
		$paylog->pm_endToEndId = $response['bankPaymentMethod']['endToEndId'];
		
		$paylog->pm_creditorAccount_iban = $response['bankPaymentMethod']['creditorAccount']['iban'];
		$paylog->pm_creditorAccount_currencyCode = $response['bankPaymentMethod']['creditorAccount']['currencyCode'];
		
		$paylog->pm_debtorAccount_iban = $response['bankPaymentMethod']['debtorAccount']['iban'];
		$paylog->pm_debtorAccount_currencyCode = $response['bankPaymentMethod']['debtorAccount']['currencyCode'];
		
		$paylog->pm_bankId = $response['bankPaymentMethod']['bankId'];
		$paylog->pm_paymentProduct = $response['bankPaymentMethod']['paymentProduct'];
		$paylog->pm_requestedExecutionDate = $response['bankPaymentMethod']['requestedExecutionDate'];
		$paylog->updateChanged();
		
		$order = GW_Order_Group::singleton()->find($paylog->order_id);
		
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

		if($order->amount_total == $paylog->amount || $paylog->test){
			$this->markAsPaydSystem($args);
		}else{
			$debugdata = ['response'=>$response,'paylog'=>$paylog->toArray()];
			$mail=[
				'subject'=>'Payment error amount_total in cart does not match kevin response',
				'body'=>"<pre>".json_encode($debugdata, JSON_PRETTY_PRINT)."</pre>"
			    ];
			GW_Mail_Helper::sendMailDeveloper($mail);
		}
		
		sleep(2);
		
		$this->redirectAfterPaymentAccept($order);

	}
	
}
