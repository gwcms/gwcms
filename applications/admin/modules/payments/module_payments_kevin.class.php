<?php


class Module_Payments_Kevin extends GW_Common_Module
{	
	
	function init()
	{	
		$this->model = GW_PayKevin_Log::singleton();
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
	
	function initKevin()
	{
		$cfg = new GW_Config("payments__payments_kevin/");	
		$cfg->preload('');		
		$options = ['error' => 'array', 'version' => '0.3'];
		$kevinClient = new Kevin\Client($cfg->clientId, $cfg->clientSecret, $options);
		
	
		return $kevinClient;	
	}
	
	
	function doRefund()
	{
		
		$item =  $this->getDataObjectById();
		
		d::dumpas($item);
		
		$paymentId = 'your-payment-id';
		$attr = [
		    'amount' => '1.00',
		    'Webhook-URL' => 'https://yourapp.com/notify'
		];
		$response = $kevinClient->payment()->initiatePaymentRefund($paymentId, $attr);
	}
	
	function doUpdate()
	{
		
		$paylog =  $this->getDataObjectById();
		$paymentId = $paylog->kevin_id;
		$kevinClient = $this->initKevin();
		$response = $kevinClient->payment()->getPayment($paymentId);	
		
		if($response['id']!=$paymentId){
			$this->setError("$paymentId response error");
			$this->setError("<pre>".json_encode($response, JSON_PRETTY_PRINT)."</pre>");
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
		
		
		
		$this->jump();
		
	}	
}
