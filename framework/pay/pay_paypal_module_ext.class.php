<?php

class pay_paypal_module_ext extends GW_Module_Extension
{
	
	function initCfg()
	{
		$cfg = new GW_Config("payments__payments_paypal/");	
		$cfg->preload('');

		return $cfg;
	}
	
	
	
	function doPayPal($args) {

		$order= $args->order;
		
		$cfg = $this->initCfg();		
		
		$test = $cfg->pay_test || $order->name == 'paytest' ;
		
		if($this->app->user && $cfg->test_user_group && in_array($cfg->test_user_group, $this->app->user->group_ids))
			$test = true;
		
		$test = false;
		
		$paypal_email = $test ? $cfg->paypal_test_email : $cfg->paypal_email;
		//$paypal_email = $cfg->paypal_email;
		
		$amount = $args->payprice;
		
		
		if($this->app->user->id==9){
			$amount = 0.01;
		}
		
		$vars = array(
		    'cmd' => "_xclick",
		    'business' => $paypal_email,
		    'item_number' => 1,
		    'amount' => $amount,
		    'currency_code' => $cfg->default_currency_code,
		    'notify_url' => $args->base.$this->app->ln."/direct/orders/orders?act=doPayPalAccept&orderid={$order->id}&id={$order->id}&action=callback",
		    'return' => $args->base.$this->app->ln."/direct/orders/orders?act=doPayPalAccept&orderid={$order->id}&id={$order->id}&action=accept",
		    'cancel_return' => $args->base.$this->app->ln."/direct/orders/orders?orderid={$order->id}&id={$order->id}",
		    'charset' => 'utf-8', //kazkodel neveikia, ir json neissaugo pareina windows-1252
		    //'cpp_header_image' => "{$args->base}application/site/assets/img/site_trans_400.png",
		    'item_name' => $args->paytext,
		);
		    
		if($this->app->user && $this->app->user->isRoot()){
			
			if($test)
				$this->setMessage('TEST REQUEST use user: sb-7j7yz6892417@personal.example.com pass: f-5TTe0m  5000eur balance 2021-11 created with lais...oriu@gmail.com https://developer.paypal.com/developer/accounts/');
			
			$vars = $this->rootConfirmJson($vars);
			if(!$vars)
				return false;
		}

		
		header('Location: https://www.' . ($test ? 'sandbox.' : '') . 'paypal.com/cgi-bin/webscr?' . http_build_query($vars));
		exit;
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


	
	function testNotification()
	{
		$json='{
        "mc_gross": "0.01",
        "protection_eligibility": "Eligible",
        "address_status": "confirmed",
        "payer_id": "3HCPUY6H88PVE",
        "address_street": "x",
        "payment_date": "13:13:19 Nov 17, 2021 PST",
        "payment_status": "Completed",
        "charset": "windows-1252",
        "address_zip": "08220",
        "first_name": "Vidmantas",
        "mc_fee": "0.01",
        "address_country_code": "LT",
        "address_name": "Vidmantas",
        "notify_version": "3.9",
        "custom": "",
        "payer_status": "verified",
        "business": "info@natos.lt",
        "address_country": "Lithuania",
        "address_city": "Vilnius",
        "quantity": "1",
        "verify_sign": "Ayr.0H5SRynFZy0WLOZruwM3q7ELAYxYA2uwro4KTEa8A9Fawa0Y3jID",
        "payer_email": "xxxxxxxxxx@gmail.com",
        "txn_id": "4D5747948W928911T",
        "payment_type": "instant",
        "last_name": "Norkus",
        "address_state": "",
        "receiver_email": "info@natos.lt",
        "payment_fee": "",
        "shipping_discount": "0.00",
        "insurance_amount": "0.00",
        "receiver_id": "55386SFH6KLKQ",
        "txn_type": "web_accept",
        "item_name": "CART_PAY",
        "discount": "0.00",
        "mc_currency": "EUR",
        "item_number": "1",
        "residence_country": "LT",
        "shipping_method": "Default",
        "transaction_subject": "",
        "payment_gross": "",
        "ipn_track_id": "f749505102871"
    }';
		$_POST = json_decode($json, true);
		
	}
	
	function doPaypalAccept()
	{

		$cfg = $this->initCfg();
		

		$debug = ['get'=>$_GET, 'post'=>$_POST, 'remote_addr'=>$_SERVER['REMOTE_ADDR'], 'time'=>date('Y-m-d H:i:s')];
		file_put_contents(GW::s('DIR/LOGS').'paypal2.log', date('H:i:s').':::'.json_encode($debug, JSON_PRETTY_PRINT), FILE_APPEND);		
		
		file_put_contents(GW::s('DIR/LOGS').'paypaltest_json.log', json_encode($_POST));
		file_put_contents(GW::s('DIR/LOGS').'paypaltest_serialize.log', serialize($_POST));		
		
		
		
		if(isset($_GET['testcase'])){
			$this->testNotification();			
		}
		
		$response = $_POST;
		$response['orderid']=$_GET['orderid'];
			
			
		$action = $_GET['action'] ?? false;
		
		
		if($action=='cancel')
		{
			//skip
		}elseif($action=='accept'){
			//uzlaikyt siekt tiek kad spetu notify suveikt
			
			
			sleep(2);
			//kad redirektas ivyktu
			$order = $this->getOrder(true);			
			
			//tiesiog nieko nedarom pereis prie redirect
		}elseif($action=='callback'){
			
			$debug = ['get'=>$_GET, 'post'=>$_POST, 'remote_addr'=>$_SERVER['REMOTE_ADDR'], 'time'=>date('Y-m-d H:i:s')];
			$this->log(json_encode($debug, JSON_PRETTY_PRINT));

		
			$logvals = array_intersect_key($response, GW_Paypal_Log::singleton()->getColumns());
			
			$extra = $response;
			foreach($logvals as $key => $val)
				unset($extra[$key]);
			
						
			$logvals['extra'] = $extra;
			$logvals['action'] = $_GET['action'];

			
			$log_entry=GW_Paypal_Log::singleton()->createNewObject($logvals);
			$log_entry->insert();
			
			$order = $log_entry->order;
			
			//d::dumpas($order);
			
			
			if ($_POST['payment_status'] != 'Completed'){
				$this->log("KILLED - Payment status accept only completed");
				
				die('Payment status accept only completed');
			}
				
			if(!$this->verifyMessage()){
				$details = json_encode(['verify_url'=>$_GET['REQ_DOMAIN'], 'response'=>$_GET['VERIFIED_STR']]);
				$this->log("KILLED - Payment verify failed details:($details)");
				
				die('Payment verify failed');
			}			
			
			$received_amount = $logvals['mc_gross'];
			
			
			if($logvals['payer_email']!='laiskonoriu@gmail.com' && ($order->amount_total != $received_amount || $logvals['mc_currency']!='EUR')){
				$debugdata = ['response'=>$logvals,'paylog'=>$log_entry->toArray()];
				$mail=[
					'subject'=>'Payment error amount_total in cart does not match revolut response',
					'body'=>"<pre>".json_encode($debugdata, JSON_PRETTY_PRINT)."</pre>"
				    ];
				GW_Mail_Helper::sendMailDeveloper($mail);	
				return $this->setError('Payment error amount_total in cart does not match revolut response / this prolbem was reported');
			}
				

			$args = [
			    'id'=>$order->id,
			    'rcv_amount'=>$order->amount_total,
			    'pay_type'=>'paypal',
			    'log_entry_id'=>$log_entry->id
			];	
			
						
			
			
			if($log_entry->test_ipn || $order->amount_total != $received_amount)
				$args['paytest']=1;

			$this->markAsPaydSystem($args);
			
		}
				
		$this->redirectAfterPaymentAccept($order);
	}
	
	function log($message)
	{
		file_put_contents(GW::s('DIR/LOGS').'paypal.log', '['.date('Y-m-d H:i:s').'] '.$message."\n", FILE_APPEND);
	}
}
