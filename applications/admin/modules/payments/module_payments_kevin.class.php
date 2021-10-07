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
}
