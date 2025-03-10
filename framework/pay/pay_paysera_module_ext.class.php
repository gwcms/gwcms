<?php

//du kart tas pats merchant_reference negali but naudojamas

class pay_paysera_module_ext extends GW_Module_Extension
{
	
	function getPayseraCfg()
	{
		$cfg = new GW_Config("payments__payments_paysera/");	
		$rows = $cfg->preload('');
		
		GW_Array_Helper::restruct2MultilevelArray($rows);

		return (object)$rows['payments__payments_paysera'];
	}
	
	
	function checkSellerCfg($order, &$cfg)
	{
		

		
		if($order->seller_id){
			
			list($project_id, $secret_key) = explode('|',$order->seller->get('keyval/paysera_config'));
			$cfg->paysera_project_id = $project_id;
			$cfg->paysera_sign_password = $secret_key;
			
			//d::dumpas($cfg);
		}
				
	}	
	
	function doPayseraPay($args) 
	{
		//$this->userRequired();

		$cfg = $this->getPayseraCfg();
		
		//d::ldump($args->order->toArray());
		
		$this->checkSellerCfg($args->order, $cfg);
		
		
		
		if(isset($args->user)){
			$user = $args->user;
		}else{
			$user = $this->app->user;
		}
		
		
		$handler = $args->handler ?? "orders";
		
		//if($user->id == 9)
		//	$args->payprice= 0.01;		
		
		
		$returnarg =  ['id'=>$args->order->id,'orderid'=>$args->order->id,'key'=>$args->order->secret];
		
		if(GW::s('MULTISITE') && GW::s("MAIN_HOST")!=$_SERVER['HTTP_HOST']){
			
			$returnarg['host'] = $args->base;
			$args->base = "https://".GW::s("MAIN_HOST").'/';
		}
		

		
		$test=isset($_GET['testu6s15g19t8']) || $cfg->paysera_test || $args->order->email=='paytest@gw.lt' || $args->order->city == 'paytest' || ($user && ($user->city=="paytest" || $user->id==9));
				
		$data = array(
		    'projectid' => $cfg->paysera_project_id,
		    'sign_password' => $cfg->paysera_sign_password,
		    'orderid' => $args->orderid.($test?'-TEST'.date('His'):"-".date('His')), //ausrinei kad veiktu "-".rand(0,9) 2021-01-12
		    'paytext' => $args->paytext,
		    'p_firstname' => $user && $user->name ? $user->name : $args->order->name,
		    'p_lastname' => $user && $user->surname ? $user->surname : $args->order->surname,
		    'p_email' => $user && $user->email ? $user->email : $args->order->email,
		    'amount' => $args->payprice * 100,
		    'currency' => $cfg->default_currency_code,
		    'country' => 'LT',
		    'accepturl' => $args->base.$this->app->ln."/direct/orders/orders?act=doPayseraAccept&action=return&".http_build_query($returnarg),
		    'cancelurl' => $args->base.$this->app->ln."/direct/orders/orders?".http_build_query($returnarg),
		    'callbackurl' => $args->base.$this->app->ln."/direct/orders/orders?act=doPayseraAccept&action=notify&".http_build_query($returnarg),
		    'test' => $test,
		    'seller_id'=>$args->order->seller_id
		);
		    
		if($args->method ?? false){
			$data['payment'] = $args->method;
		}
		
		
		if(($this->app->user && $this->app->user->isRoot()) || $args->order->email=='debug@gw.lt' ){
						
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
		if(isset($returnarg['host']))
			header("Referrer-Policy: no-referrer");
		
		
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
	
		//multisite is pagrindinio hosto peradresuoti atgal i reprezentacini
		
		if(isset($_GET['host'])){
			$action = $_GET['action'] ?? false;;
			
			if( !$action  != 'notify'){
				
				$current_url = $_SERVER['REQUEST_URI'];

				// Parse the URL into components
				$parsed_url = parse_url($current_url);

				// Parse the query string into an associative array
				parse_str($parsed_url['query'] ?? '', $query_params);

				// Remove the specific argument (e.g., 'host')
				unset($query_params['host']);
				$query_params['original_host'] = $_SERVER['HTTP_HOST'];

				// Rebuild the query string without the removed parameter
				$new_query = http_build_query($query_params);

				// Rebuild the URL
				$new_url = $parsed_url['path'] . ($new_query ? '?' . $new_query : '');				
				
				
				header('Location: '.$_GET['host'].$new_url);
				exit;
			}
			
		}
		
		
		$this->log($_SERVER['REQUEST_URI']);
		
		ob_start();
		
		$cfg = $this->getPayseraCfg();	
		$order = $this->getOrder(true);
		$this->checkSellerCfg($order, $cfg);
		
		
		$this->log(['seller_id'=>$order->seller_id, 'loadconfig_for_project'=>$cfg->paysera_project_id]);
		
		
		try {
			$response = WebToPay::checkResponse($_GET, [
			    'projectid' => $cfg->paysera_project_id,
			    'sign_password' => $cfg->paysera_sign_password,
			    'log' => GW::s('DIR/LOGS') . 'paysera.log'
			]);
			
			if($this->app->user && $this->app->user->isRoot()){

			//	$this->confirm("<pre>".json_encode($response, JSON_PRETTY_PRINT).'</pre>');
			}
		
			$this->log(['get_action'=>$_GET['action']] + (array)$response);
			
		} catch (Exception $e) {
			
			
			$errtxt = $e->getMessage();
			//if($_GET['action']=='callback'){
				
			//}
			$data = ['server'=>$_SERVER, 'error'=>$errtxt, '_POST'=>$_POST ?? [], '_GET'=>$_GET ?? []];
			
			
			/*
			if($_SERVER['REMOTE_ADDR'] == '84.15.236.87'){
				d::dumpas($data);
			}*/
			
			
			$this->log($data);
			
			$data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			
			if( $errtxt!='Expected status code 1' && ($_GET['action'] ?? false) != 'return' ){
				$opts=[
				    'subject'=>GW::s('PROJECT_NAME').' paysera error',
				    'body'=>"<pre>".$data."</pre>"
				];
				GW_Mail_Helper::sendMailDeveloper($opts);				
			}
			
			$logvals=['action'=>$_GET['action'], 'orderid'=>$_GET['orderid'], 'paytext'=>$errtxt,'handler_state'=>666,'ip'=>$_SERVER['REMOTE_ADDR']];
			$log_entry=GW_Paysera_Log::singleton()->createNewObject($logvals);
			$log_entry->insert();
			
			
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
		
		$response['ip'] = $_SERVER['REMOTE_ADDR'];
		$logvals = array_intersect_key($response, GW_Paysera_Log::singleton()->getColumns());	
		$logvals['action'] = $_GET['action'];
		$log_entry=GW_Paysera_Log::singleton()->createNewObject($logvals);
		$log_entry->orderid = $_GET['orderid'];
		$log_entry->insert();			
		
		$data = $response;
		$action = $_GET['action'];


		$p = explode('-',$data['orderid']);
		$id = $_GET['orderid'];
		

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
			    'id'=>$order->id,
			    'rcv_amount'=>$log_entry->amount / 100,
			    'log_entry_id'=>$log_entry->id,
			    'pay_type'=>'paysera'
			];
			
			if($data['test'] != '0'){
				$args['paytest'] = 1;
			}
			
			if($logvals['p_email']=='vidmantasss.norkus@gw.lt' && $logvals['amount']==1){
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
				
				$opts=[
				    'subject'=>GW::s('PROJECT_NAME').' paysera error 2',
				    'body'=>$data
				];
				GW_Mail_Helper::sendMailDeveloper($opts);
			}
			
			die('OK');//atsakas payserai kad viskas ok
		}
			
		

		$this->redirectAfterPaymentAccept($order);
	}
	
	
	
	function doPayseraRetryProcess()
	{
		$log_entry = GW_Paysera_Log::singleton()->find($_GET['id']);
		$order = GW_Order_Group::singleton()->find(['id=?', $log_entry->orderid]);	
		
		$args = [
			    'id'=>$order->id,
			    'rcv_amount'=>$log_entry->amount / 100,
			    'pay_type'=>'paysera',
			    'log_entry_id'=>$log_entry->id
			];
		
		$markaspayd = $this->markAsPaydSystem($args);	
			
		//$log->processed = $log->processed+1;
		//$log->updateChanged();
		
		d::dumpas(['packet'=>$log_entry, 'mark_as_payd'=>$markaspayd]);
	}	
	
}
