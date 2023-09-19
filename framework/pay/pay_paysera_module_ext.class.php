<?php

//du kart tas pats merchant_reference negali but naudojamas

class pay_paysera_module_ext extends GW_Module_Extension
{
	
	function getPayseraCfg()
	{
		$cfg = new GW_Config("payments__payments_paysera/");	
		$cfg->preload('');
		return $cfg;
	}
	
	function doPayseraPay($args) 
	{
		//$this->userRequired();

		$cfg = $this->getPayseraCfg();
		
		if(isset($args->user)){
			$user = $args->user;
		}else{
			$user = $this->app->user;
		}
		
		
		$handler = $args->handler ?? "orders";
		
		//if($user->id == 9)
		//	$args->payprice= 0.01;		
		
		$test=isset($_GET['testu6s15g19t8']) || $cfg->paysera_test || $args->order->city == 'paytest' || $user->city=="paytest" || $user->id==9;
				
		$data = array(
		    'projectid' => $cfg->paysera_project_id,
		    'sign_password' => $cfg->paysera_sign_password,
		    'orderid' => $args->orderid.($test?'-TEST'.date('His'):"-".date('His')), //ausrinei kad veiktu "-".rand(0,9) 2021-01-12
		    'paytext' => $args->paytext,
		    'p_firstname' => $user->name,
		    'p_lastname' => $user->surname,
		    'p_email' => $user->email,
		    'amount' => $args->payprice * 100,
		    'currency' => $cfg->default_currency_code,
		    'country' => 'LT',
		    'accepturl' => $args->base.$this->app->ln."/direct/orders/orders?act=doPayseraAccept&action=return&id={$args->order->id}&orderid={$args->order->id}&key={$args->order->secret}",
		    'cancelurl' => $args->base.$this->app->ln."/direct/orders/orders?orderid={$args->order->id}&id={$args->order->id}",
		    'callbackurl' => $args->base.$this->app->ln."/direct/orders/orders?act=doPayseraAccept&action=notify&id={$args->order->id}&orderid={$args->order->id}&key={$args->order->secret}",
		    'test' => $test,
		);
		    
		if($args->method ?? false){
			$data['payment'] = $args->method;
		}
		
		
		if($this->app->user && $this->app->user->isRoot()){
						
			$data = $this->rootConfirmJson($data);
			if(!$data)
				return false;
			
		}	

		//d::dumpas($data);
				    
		if($this->app->ln == 'ru')
			$data['lang'] = 'rus';
		
		if($this->app->ln == 'en')
			$data['lang'] = 'eng';
		
		
		///d::dumpas($data);

		WebToPay::redirectToPayment($data);
		exit;
	}
	
	function log($msg){
		
		if(is_array($msg))
			$msg= json_encode ($msg);
		
		file_put_contents(GW::s('DIR/TEMP').'paysera.log', date('Ymd H:i:s'). ' '.$msg."\n\n", FILE_APPEND);
	}

	function doPayseraAccept()
	{
		ob_start();
		
		$cfg = $this->getPayseraCfg();	
		
		try {
			$response = WebToPay::checkResponse($_GET, array(
			    'projectid' => $cfg->paysera_project_id,
			    'sign_password' => $cfg->paysera_sign_password,
			    'log' => GW::s('DIR/LOGS') . 'paysera.log'
			));
			
			if($this->app->user && $this->app->user->isRoot()){

			//	$this->confirm("<pre>".json_encode($response, JSON_PRETTY_PRINT).'</pre>');
			}
		
			$this->log(['get_action'=>$_GET['action']] + (array)$response);
			
		} catch (Exception $e) {
			
			

			//if($_GET['action']=='callback'){
				
			//}
			$data = ['uri'=>$_SERVER['REQUEST_URI'], 'error'=>$e->getMessage(), '_POST'=>$_POST ?? []];
			
			
			$this->log($data);
			
			$data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			
			$opts=[
			    'subject'=>GW::s('PROJECT_NAME').' paysera error',
			    'body'=>$data
			];
			GW_Mail_Helper::sendMailDeveloper($opts);				
			
			
			$this->redirectAfterPaymentAccept($order);
			///header('Location: '.$_GET['redirect_url']);
			//$this->app->jump();
			exit;
		}
		
		
		if(GW_Paysera_Log::singleton()->find(['orderid=? AND `action`=?', $response['orderid'],$_GET['action']]))
		{
			//GW_Message::singleton();
			//notify someone about intereestin thing
		}

		$logvals = array_intersect_key($response, GW_Paysera_Log::singleton()->getColumns());	
		$logvals['action'] = $_GET['action'];
		$log_entry=GW_Paysera_Log::singleton()->createNewObject($logvals);
		$log_entry->insert();			
		
		$data = $response;
		$action = $_GET['action'];


		$p = explode('-',$data['orderid']);
		$id = $p[1];
		

		if ($data['type'] !== 'macro') {
			die('macro payments not accepted');
		}			
		
		$order = $this->getOrder(true);
		
		if(!$order){
			$this->log($msg="PAYSERA ERROR NO ORDERID RECEIVED");
			
			d::dumpas($msg);	
		}
		//
		if($action=='notify')
		{	
			$args = [
			    'id'=>$id,
			    'rcv_amount'=>$log_entry->amount / 100,
			    'log_entry_id'=>$log_entry->id,
			    'pay_type'=>'paysera'
			];
			
			if($data['test'] != '0'){
				$args['paytest'] = 1;
			}
			
			
			$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?act=doMarkAsPaydSystem&sys_call=1&'. http_build_query($args));
			
			$this->log($url);
			
			
			$log_entry->handler_state = 7;
			$log_entry->update();	

			
		
		}else{
			//nothing
		}
				
		if($_GET['action']=='notify'){
			
			
			
			$out = ob_get_contents();
			ob_end_clean();
			if($out){
				
				$this->log("Unexpexted output: $out");
				
				$opt=[
				    'subject'=>GW::s('PROJECT_NAME').' paysera error 2',
				    'body'=>$data
				];
				GW_Mail_Helper::sendMailDeveloper($opts);
			}
			
			die('OK');//atsakas payserai kad viskas ok
		}
			
		

		$this->redirectAfterPaymentAccept($order);
	}
	
}
