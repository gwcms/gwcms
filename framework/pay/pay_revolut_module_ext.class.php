<?php

class pay_revolut_module_ext extends GW_Module_Extension
{
	
	function revolutApi()
	{
		$cfg = new GW_Config("payments__payments_revolut/");	
		$cfg->preload('');

		$api = new GW_PayRevolut_Api($cfg->api_key, $cfg->sandbox);	
		return [$cfg, $api];
	}
	
	function doRevolut()
	{
		$order = $this->getOrder(true);
		
		//$return_args="&id={$order->id}&orderid={$order->id}&key={$order->secret}";
		
		//$args=(object)['base'=>'https://shop.drpaulclayton.eu/'];
		
		//$return_url = $args->base.$this->app->ln."/direct/orders/orders?act=doRevolutAccept&action=return".$return_args;
		//
		
		$payment_data = ['amount'=>$order->amount_total*100,'currency'=>'EUR'];
		//$payment_data['redirectUrls']=['success'=>$return_url, 'failure'=>$return_url, 'cancel'=>$return_url];

			
		if($this->app->user && $this->app->user->isRoot()){
			
			$payment_data = $this->rootConfirmJson($payment_data);
			if(!$payment_data)
				return false;
			
		}		
		
		$key = 'pay_revolut_'.$order->id.'_'.$order->amount_total;
		if(false && ($tmp = $this->app->sess($key)) && ($revolog = new GW_PayRevolut_Log($tmp, true)) && $revolog->public_id){
			
			
			//d::dumpas($revolog);
		}else{
			

			list($cfg, $api) = $this->revolutApi();
			
			//https://developer.revolut.com/docs/merchant/create-order
			$revoresponse = $api->request('POST','orders', $payment_data);
			
			
			
			$revolog = new GW_PayRevolut_Log;
			$revolog->order_id = $order->id;
			$revolog->remote_id = $revoresponse['id'];
			$revolog->public_id = $revoresponse['public_id'];
			$revolog->state = $revoresponse['state'];
			$revolog->created_at = $revoresponse['created_at'];
			$revolog->updated_at = $revoresponse['updated_at'];
			$revolog->amount = $revoresponse['order_amount']['value']/100;
			$revolog->currency = $revoresponse['order_amount']['currency'];
			$revolog->remote_id = $revoresponse['id'];
			$revolog->email = $this->app->user ? $this->app->user->email : '';
			$revolog->test = $cfg->sandbox;
			$revolog->checkout_url = $revoresponse['checkout_url'];
			$revolog->insert();
			
			if($this->app->user && $this->app->user->isRoot()){
				$this->setMessage("<pre>".json_encode($revoresponse, JSON_PRETTY_PRINT)."</pre>");
			}
			
			$this->app->sess($key, $revolog->id);
			
			//Navigator::jump($revoresponse['checkout_url']);
		}
		
		$this->tpl_vars['revolog'] = $revolog;
	}
	
	
	function doRevolutAccept()
	{
		
		$revolog = GW_PayRevolut_Log::singleton()->find(["id=?", $_GET['id']]);
		$order = $revolog->order;
	
		$resp = $this->revolutUpdate($revolog);
		$payment = $resp['payments'][0];
		
		$authorised_amount = $payment['authorised_amount']['value']/100;
		$received_amount = $payment['settled_amount']['value']/100;
		
		
		if($this->app->user && $this->app->user->isRoot()){
			
			$this->confirm("<pre>".json_encode(['response'=>$resp, 'logrecord'=>$revolog->toArray()], JSON_PRETTY_PRINT).'</pre>');
		}
		
		if($resp['state']=='COMPLETED'){
			//d::dumpas([$order->amount_total, $received_amount]);
			
			if($order->amount_total != $authorised_amount){
				$debugdata = ['response'=>$resp,'paylog'=>$revolog->toArray()];
				$mail=[
					'subject'=>'Payment error amount_total in cart does not match revolut response',
					'body'=>"<pre>".json_encode($debugdata, JSON_PRETTY_PRINT)."</pre>"
				    ];
				GW_Mail_Helper::sendMailDeveloper($mail);	
				return $this->setError('Payment error amount_total in cart does not match revolut response / this prolbem was reported');
			}
		
			$extra = $order->extra;
			$extra['received_amount'] = $received_amount;
			
			
			$args = [
			    'id'=>$order->id,
			    'rcv_amount'=>$authorised_amount,
			    'pay_type'=>'revolut',
			    'log_entry_id'=>$revolog->id
			];		
			
			if($revolog->test)
				$args['paytest']=1;
			
			
			$this->markAsPaydSystem($args);
		}
		
		sleep(2);
		
		$this->redirectAfterPaymentAccept($order);
	}
	
	
	
	function revolutUpdate($revolog)
	{
		list($cfg, $api) = $this->revolutApi();
		
		$resp = $api->request('GET','orders/'.$revolog->remote_id); 
		
		$payment = $resp['payments'][0];
		
		$revolog->payment_method = $payment['payment_method']['type'];
		$revolog->payment_id = $payment['id'];
		
		$revolog->customer_id = $resp['customer_id'];
		$revolog->created_at = $resp['created_at'];
		$revolog->updated_at = $resp['updated_at'];
		$revolog->completed_at = $resp['completed_at'];
		$revolog->email = $resp['email'];
		$revolog->state = $resp['state'];
		
		if($payment['payment_method']['type']=='CARD'){
			$tmp = $payment['payment_method']['card'];
			$revolog->card_bin =  $tmp['card_bin'];
			$revolog->card_country =  $tmp['card_country'];
			$revolog->card_last_four =  $tmp['card_last_four'];
			$revolog->card_expiry =  $tmp['card_expiry'];
			$revolog->cardholder_name =  $tmp['cardholder_name'];
			$revolog->card_brand =  $tmp['card_brand'];
			$revolog->checks =  json_encode($tmp['checks']);			
		}
		
		
		$revolog->update();
		
		
		$received_amount = $payment['settled_amount']['value']/100;
		return $resp;
	}

	function viewRevolutOrders()
	{
		
		if(!$this->app->user || $this->app->user->isRoot())
			return $this->setError('No access');
		
		$resp = $this->revolutApi()->request('GET','orders'); 
		
		
		
		
		d::dumpas($resp);
	}
	
}
