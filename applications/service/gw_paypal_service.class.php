<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gw_paysera_service
 *
 * @author wdm
 */
class gw_paypal_service 
{
	public $error=false;
	public $redirect_url=false;
	
	function init()
	{
		$this->app->initDB();
		$this->app->loadConfig();
	}

	
	function checkIsProcessed()
	{
		return GW_PayPal_Log::singleton()->find(['orderid=? AND `action`=? AND handler=? AND ipn_track_id=?', $_GET['orderid'],$_GET['action'],$_GET['handler'],$_POST['ipn_track_id']]);
	}
	
	
	function verifyMessage()
	{
		$url = isset($_POST['test_ipn']) && $_POST['test_ipn'] ?'www.sandbox.paypal.com':'www.paypal.com';
		$vrf = file_get_contents('https://'.$url.'/cgi-bin/webscr?cmd=_notify-validate', false, stream_context_create(array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\nUser-Agent: MyAPP 1.0\r\n",
			'method'  => 'POST',
			'content' => http_build_query($_POST)
		)
		)));

		$_GET['REQ_DOMAIN'] = $url;
		$_GET['VERIFIED_STR'] = $vrf;
		
		return $vrf=='VERIFIED';		
	}
	
	function processRedirectUrl()
	{
		if(isset($_GET['redirect_url'])){
			header('Location: '.$_GET['redirect_url']);
			exit;
		}
	}
	
	function process()
	{
		$debug = ['get'=>$_GET, 'post'=>$_POST, 'remote_addr'=>$_SERVER['REMOTE_ADDR'], 'time'=>date('Y-m-d H:i:s')];
		file_put_contents('/tmp/natos_paypal_log', json_encode($debug, JSON_PRETTY_PRINT), FILE_APPEND);
		
		
		$cfg = new GW_Config('datasources__payments_paypal/');
		$cfg->preload('');
		
		$response = $_POST;
		$response['orderid']=$_GET['orderid'];
			
		
		
		if($_GET['action']=='cancel')
		{
			$this->handlerOrders($response, 'cancel');
		}elseif($_GET['action']=='accept'){
			//tiesiog nieko nedarom pereis prie redirect
		}else{
			
			if ($_POST['payment_status'] != 'Completed'){
				file_put_contents('/tmp/natos_paypal_log', "KILLED - Payment status accept only completed\n", FILE_APPEND);
				die('Payment status accept only completed');
			}
				
			if(!$this->verifyMessage()){
				$details = json_encode(['verify_url'=>$_GET['REQ_DOMAIN'], 'response'=>$_GET['VERIFIED_STR']]);
				
				file_put_contents('/tmp/natos_paypal_log', "KILLED - Payment verify failed details:($details)\n", FILE_APPEND);
				die('Payment verify failed');
			}
			
			//if($this->checkIsProcessed())
			//	die('Payment already accepted');
						
			$logvals = array_intersect_key($response, GW_Paypal_Log::singleton()->getColumns());
			
			$extra = $response;
			foreach($logvals as $key => $val)
				unset($extra[$key]);
			
			$logvals['extra'] = $extra;
			$logvals['action'] = $_GET['action'];
			$logvals['handler'] = $_GET['handler'];
			$logvals['handler_state'] = $this->{'handler'.$_GET['handler']}($response, $_GET['action']);

			$log_entry=GW_Paypal_Log::singleton()->createNewObject($logvals);
			$log_entry->insert();
		}
		
		
		$this->processRedirectUrl();

		if($this->error)
			die($this->error);

		echo 'OK';
	}
	
	function handlerOrders($data, $action)
	{
		
		$order = Shop_Orders::singleton()->find(['id=?', $data['orderid']]);
		
		if($action=='callback')
		{
			$order->pay_type = 2;
			$order->pay_status = 7;
			$order->pay_time = date('Y-m-d H:i:s');
			$order->status = 3; //apmoketas
		}elseif($action=='cancel'){
			$order->pay_status = 0;
		}
		
		if(isset($data['test_ipn']) && $data['test_ipn'] == '1')
			$order->pay_test =1;
		
		$order->updateChanged();
		
		return 1;
	}
}
