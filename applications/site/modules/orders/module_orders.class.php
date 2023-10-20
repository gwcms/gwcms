<?php

class Module_Orders extends GW_Public_Module
{
	
	function init()
	{		
		$this->model = GW_Order_Group::singleton();
		
		
		$this->addRedirRule('/^doRevolut|^viewRevolut/i',['options','pay_revolut_module_ext']);	
		$this->addRedirRule('/^doMontonio/i',['options','pay_montonio_module_ext']);	
		$this->addRedirRule('/^doKevin/i',['options','pay_kevin_module_ext']);	
		$this->addRedirRule('/^doPaypal/i',['options','pay_paypal_module_ext']);	
		$this->addRedirRule('/^doPaysera/i',['options','pay_paysera_module_ext']);	
		$this->addRedirRule('/^doKlix/i',['options','pay_klix_module_ext']); //citadele card payment
		
		$this->config = new GW_Config('payments/');
		$this->config->preload('');
		$this->tpl_vars['pay_methods'] = json_decode($this->config->pay_types, 1);		
		
		parent::init();
		
		//$this->tpl_dir .= $this->module_name."/";

		
		
		
		

		
		$this->tpl_vars['breadcrumbs_attach'][] =  [
		    'title' => GW::ln('/m/VIEWS/orders'),
		    'url' => $this->app->buildUri('direct/orders/orders')
		];		
		
		
		$this->initFeatures();
		$this->tpl_vars['ecommerce_orders']=1;
		
		if($this->app->user && $this->config->testpay_user_id == $this->app->user->id)
			$this->can_do_test_pay = true;
				
	}	
	
	
	
	function getOrder($allowwithsecret=false)
	{
		$id = $_GET['id'] ?? false;
		
		if(!$id)		
			$id = $_GET['orderid'] ?? false;
		
		
		if($allowwithsecret && isset($_GET['key'])){
			$order = GW_Order_Group::singleton()->find(['id=? AND secret=?', $id, $_GET['key']]);
		}else{
			$order = GW_Order_Group::singleton()->createNewObject($id, true);
		}
				
		if(!$allowwithsecret && $order->user_id != $this->app->user->id){
			$this->setError("/m/ORDER_OWNER_ERROR");
			$this->app->jump("direct/orders/orders");
		}
		
		return $order;
	}
	
	function doOrderPay()
	{
		//$this->viewDefault();
		
		
		//$this->userRequired();
		
		$order = $this->getOrder(true);
		
		$this->prepareOrderForPay($order);
		
		
		$citems = $order->items;
	
		$pay_methods=json_decode($this->config->pay_types, 1);
		
		if(count($pay_methods) > 1 && !isset($_GET['type'])){
			$this->app->jump('direct/orders/orders/payselect',['id'=>$order->id,'orderid'=>$order->id]);
		}
		
		$type = $_GET['type'] ?? $pay_methods[0];
		
		
		
		
		
		//nurasyti suma nuo kupono jei jau pereina prie mokejimo
		if($order->amount_coupon)
		{
			$order->setCoupon(false, true);
		}		
		
		
		
		$args = (object)[
			'succ_url'=>'redirect_url=' . urlencode($this->buildURI('', ['absolute' => 1,'act'=>'doCompletePay','id'=>$order->id,'key'=>$order->secret])),
			'cancel_url'=>'redirect_url=' . urlencode($this->buildURI('', ['absolute' => 1,'act'=>'doCancelPay','id'=>$order->id])),
			'base'=> Navigator::getBase(true),
			'orderid'=>'order-'.$order->id,
			'paytext'=> GW::ln('/g/CART_PAY',['v'=>['id'=>$order->id]]),
			'payprice'=>$order->amount_total,
			'items_number'=>count($citems),
			'order'=>$order,
			'paytype'=>$type
		];			
		
		
		
		if($this->feat('mergepaymethods')){
			$order->pay_subtype = '';

			if(isset($_GET['method']) && $_GET['method']){
				$args->method = $_GET['method'];
				$order->pay_subtype = $args->method;
			}
		}
			
		$order->use_lang = $this->app->ln;
		$order->updateChanged();
		
		if($type=='paysera'){
			$this->doPayseraPay($args);		
		}elseif($type=='paypal'){			
			$this->doPayPal($args);
		}elseif($type=='kevin'){
			$this->doKevinPay($args);
		}elseif($type=='revolut' || $type=='revolut_cc'){
			header('Location:'.$this->buildURI('', ['absolute' => 1,'id'=>$order->id,'orderid'=>$order->id,'paymentselected'=>$type,'act'=>'doRevolut']));
		}elseif($type=='montonio' || $type=='montonio_cc'){
			$this->doMontonioPay($args);
		}elseif($type=='banktransfer'){
			$this->app->jump('direct/orders/orders/paybanktransfer',['id'=>$order->id,'orderid'=>$order->id]);
			
		}elseif($type=='zeroprice'){			
			if($order->amount_total>0){
				$this->setError(GW::ln('/m/ERROR_COUPON_AMOUNT_NOT_SUFFICIENT'));
				header('Location:'.$this->buildURI('', ['absolute' => 1,'id'=>$order->id,'key'=>$order->secret]));
				return true;
			}
			if($order->amount_coupon){
				$args = ['id'=>$order->id];
				$args['rcv_amount'] = $order->amount_total;
				$args['pay_type'] = 'couponpay';
						
				$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?act=doMarkAsPaydSystem&sys_call=1&'. http_build_query($args));
				d::dumpas($url);
				$this->setMessage($url);		
				header('Location:'.$this->buildURI('', ['absolute' => 1,'id'=>$order->id,'key'=>$order->secret]));
			}
			
			
		}else{
			d::dumpas("Unknown method $type");
		}
	}
	
	

	
	function redirectAfterPaymentAccept($order)
	{
		header('Location:'.$this->buildURI('', ['absolute' => 1,'act'=>'doCompletePay','id'=>$order->id,'key'=>$order->secret]));
	}
	
	function markAsPaydSystem($args)
	{
		$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?act=doMarkAsPaydSystem&sys_call=1&'. http_build_query($args));
	}
	
	function doOrderPayRoot()
	{
		$this->userRequired();
		
		if(!$this->can_do_test_pay && !$this->app->user->isRoot())
			die('not permited');
		
		
		$order = $this->getOrder(true);
		
		//test case

		$args = ['id'=>$order->id];		
		$args['paytest']=1;
		$args['rcv_amount'] = $order->amount_total;


		$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?act=doMarkAsPaydSystem&sys_call=1&'. http_build_query($args));
		$this->setMessage($url);
		session_write_close();
		
		sleep(1);
		
		header('Location:'.$this->buildURI('', ['absolute' => 1,'act'=>'doCompletePay','id'=>$order->id,'key'=>$order->secret]));		
	}
	

	function expirityChecks($order)
	{
		foreach($order->items as $citem){
			if(($citem->obj->expirity_check_before_buy ?? false) && !$citem->obj->expirityCheck($citem)){
				
				
				$this->setError($citem->obj->title." - ".GW::ln('/m/EXPIRED').' #'.$citem->obj->id);
				
				
				$this->initLogger();
				$this->lgr->msg('EXPIRED CARTITEM: '. json_encode($citem->toArray()));
				
				$citem->delete();
				
				
				
				return false;
			}	
		}
		
		return true;
	}
	
	function prepareOrderForPay($order)
	{
		if($order->payment_status==7)
			return false;
			
		
		if(!$order->deliverable)
			$order->placed_time = date('Y-m-d H:i:s');
		
		$order->updateTotal();
		
		$order->setSecretIfNotSet();
		
		$citems = $order->items;
		

		//nebepridet pakartotinai
		if(!$order->items || ($order->amount_total <= 0 && !$order->amount_items))
		{
			$this->setMessage("/g/CART_EMPTY");
			$this->app->jump();
		}
		
		//nebepridet pakartotinai

		//extend expirity time // etc
		
		if(!$this->expirityChecks($order))
			$this->jump(''); // permes i pirma puslapi
		
		foreach($order->items as $citem){

			
			//d::ldump($citem);
			//d::dumpas($citem->obj);
			
			$citem->obj->fireEvent('PAY_START');
		}	
		
		//jei mokejima perdave kitam
		if(!$this->app->user)
			return true;
		
		//unset current user cart
		if($this->app->user->get('ext/cart_id') == $order->id){
			$order->open = 0;
			$order->updateChanged();
			$this->setMessage(GW::ln('/m/CART_CLOSED_YOU_CAN_FIND_IT_IN_MY_ORDERS'));
			$this->app->user->set('ext/cart_id', '');	
		}
	}
	
	
	function doSendPaymentInfo()
	{
		$email = $_POST['item']['email'];
		
	}
	
	function doOpenOrder()
	{
		$order = $this->getOrder();
		
		if($order->pay_status==7){
			
			$this->setError(GW::ln('/m/CANT_OPEN_PAYD_ORDERS'));
			
		}else{
			$order->open = 1;
			$order->pay_type = '';
			$this->app->user->set('ext/cart_id', $order->id);
			$order->updateChanged();
			
			$this->setMessage(GW::ln('/m/ORDER_IS_OPEN_AND_CAN_BE_MODIFIED'));
		}
		$this->app->jump(false);
		
	}
	
	function doCloseOrder()
	{
		$order = $this->getOrder();
		
		if($order->pay_status==7){
			
			$this->setError(GW::ln('/m/CANT_CLOSE_PAYD_ORDERS'));
			
		}else{
			$order->open = 0;
			$order->pay_type = '';
			$this->app->user->set('ext/cart_id', '');
			$order->updateChanged();
			
			$this->setMessage(GW::ln('/m/ORDER_IS_CLOSED_STILL_CAN_BE_REOPENED_LATER'));
		}
		$this->app->jump(false);		
	}
	
	
	function doCompletePay()
	{
		$order = $this->getOrder(true);
		$jumpargs = ['orderid'=>$order->id,'id'=>$order->id];
		
		if($order->payment_status==7){
			$this->setMessage(GW::ln('/m/PAYMENT_COMPLETE'));
			
			if($tmp=$this->app->sess('after_order_'.$order->id)){
				Navigator::jump($tmp['after_pay']);
			}
		}else{
			$jumpargs['paywait'] = 1;
		}
	
		if(!$this->app->user){
			$this->app->jump('/');
		}else{
			$this->app->jump('direct/orders/orders',$jumpargs);
		}
	}
	
	
	
	function doCancelPay()
	{
		$this->setMessage(GW::ln('/m/WHY_CANCEL_PAYMENT'));
	}
	
	function doCancelOrder()
	{
		$order = $this->getOrder();
				
		
		if($this->app->user->get('ext/cart_id') == $order->id){
			$this->app->user->set('ext/cart_id', '');
			$order->open = 0;
		}
		
		$order->fireEvent('BEFORE_CHANGES');
		
		
		$order->active = $_GET['state']==1 ? 0 : 1;
	
		
		$order->updateChanged();
		$this->app->jump('direct/orders/orders');
	}

	
	function viewPayBanktransfer()
	{
		$order = $this->getOrder();
		
		$this->prepareOrderForPay($order);
		
		
		$this->tpl_vars['item'] = $order;
	}
	
	function viewOtherPayee()
	{
		$order = $this->getOrder();
		
		$this->prepareOrderForPay($order);
		
		
		$this->tpl_vars['item'] = $order;
		
		
	}

	function doSendToOtherPayee()
	{
		$order = $this->getOrder();
		
		$vals = $_POST['item'];

		$permitfields =  array_flip(['keyval/otherpayee_email','keyval/otherpayee_msg']);
		$this->filterPermitFields($vals,$permitfields);
		
		foreach($vals as $key => $val)
			$order->set($key, $val);
		
		
		$order->setSecretIfNotSet();
		$order->updateChanged();
		
		$mailvars['view_invoice_link'] = $this->app->buildURI('direct/orders/orders/invoice', ['id'=>$order->id,'key'=>$order->secret,'download'=>1,'preinvoice'=>1],['absolute' => 1]);
		$mailvars['pay_link'] = $this->app->buildURI('direct/orders/orders', ['act'=>'doOrderPay','id'=>$order->id,'key'=>$order->secret],['absolute' => 1]);
		$mailvars['usertitle'] = $this->app->user->title;
		$mailvars['sitedomain'] = parse_url(GW::s('SITE_URL'), PHP_URL_HOST);
		$mailvars['sitetitle'] = GW::ln('/g/CONTACTS_COMPANY_NAME');
		$mailvars['order'] =$order;
		
		$response = $this->app->innerRequest("payments/ordergroups/invoicevars",['id'=>$order->id],[],['app'=>'admin','user'=>GW_USER_SYSTEM_ID]);	
	
		$tpl = file_get_contents(__DIR__.'/tpl/invoice_items.tpl');
		$orderinfo = GW_Mail_Helper::prepareSmartyCode($tpl, $response['vars']);
		
		$tpl=file_get_contents(__DIR__.'/tpl/otherpayee_mail.tpl');
		$mailvars['orderinfo']=$orderinfo;
		
		$html = GW_Mail_Helper::prepareSmartyCode($tpl, $mailvars);

		$response['vars']['preinvoice']=1;
		$invoice_html = GW_Mail_Helper::prepareSmartyCode($response['tpl'], $response['vars']);		
		$pdf=GW_html2pdf_Helper::convert($invoice_html, false);		
		$subject = GW::ln('/g/OTHER_PAYEE_SUBJECT',['v'=>['orderid'=>$order->id, 'usertitle'=>$mailvars['usertitle']]]);
		
		$opts=[
		    'to'=>$order->get('keyval/otherpayee_email'),
		    'subject'=>$subject,
		    'body'=>$html,
		    'attachments'=>['invoice_'.$mailvars['sitedomain'].'_'.$order->id.'.pdf'=>$pdf]
		];
			
		
		
		
		$status = GW_Mail_Helper::sendMail($opts);
		
		if(!$status){
			$this->setError(GW::ln('/m/ERROR_SENDING_PAYEE_MAIL'));
			$this->app->jump();
		}
		
		$order->set('adm_message', GW::ln('/m/PAYEE_MAIL_SENT_TO').' '.$order->get('keyval/otherpayee_email'));
		$order->updateChanged();
		
		$args  =$_GET;
		unset($args['act']);
		
		$this->app->jump('direct/orders/orders', ['orderid'=>$order->id]);
		
		exit;
	}
	
	function doSaveBankTransferDetails()
	{
		$order = $this->getOrder();
		
			
		$vals = $_POST['item'];
		
		//d::dumpas();
		/*
    [email] => info@voro.lt
    [name] => Vidmantas
    [surname] => Norkus
    [phone] => 860089089
    [city] => paytest
    [company] => 
    [company_code] => 
    [vat_code] => 
    [company_addr] => 
		 */
		
		
		$permitfields =  array_flip(['email','name','surname','phone','city','company','company_code','vat_code','company_addr']);
		$this->filterPermitFields($vals,$permitfields);
		
		$order->setValues($vals);
		$order->update();
		$this->app->jump(false, $_GET);
		
		//
	}
	

	function doCartItemRemove()
	{
		$this->userRequired();
		$cart = $this->getOrder();
				
		
		if(!$cart->open){
			$this->setError(GW::ln('/m/CART_IS_CLOSED'));
			return $this->app->jump();
		}
		
		//nebepridet pakartotinai
		if($cart->items)
			foreach($cart->items as $citem){
				if($citem->id == $_GET['ciid']){
					
					if(!$citem->can_remove){
						$this->setError(GW::ln('/m/CART_ITEM_NON_REMOVABLE'));
						return $this->app->jump();
					}
					
					$citem->delete();
					$cart->updateTotal();
					$this->setMessage(GW::ln('/m/CART_ITEM_WAS_REMOVED'));
					
					
					$citem->obj->fireEvent('CART_REMOVE');
				}
			}	
			
		$this->app->jump();
	}	
	
	
	function viewDefault()
	{
		$this->userRequired();
		
		$active = 1;
		
		if(isset($_GET['canceled'])){
			$active = 0;
		}else{
			$this->tpl_vars['canceled_count'] =  GW_Order_Group::singleton()->count(['user_id=? AND active=0', $this->app->user->id]);
		}
		
		
		
		$list = GW_Order_Group::singleton()->findAll(['user_id=? AND active=?', $this->app->user->id,$active],['order'=>'update_time DESC']);
		
		
		
		$this->tpl_vars['list'] = $list;
		
		
		$this->tpl_vars['admin_enabled'] = $_SESSION['site_auth']['admin_user_id'] ?? false || ($this->app->user && $this->app->user->isRoot());
	}
	
	
	function viewOrder()
	{
		$this->userRequired();
		
		
		
		
		$list = [GW_Order_Group::singleton()->find(['(user_id=? OR user_id=?) AND id=?', $this->app->user->id, $this->app->user->parent_user_id, $this->args['id']])];
		
		$this->tpl_name = "orders";
		
		$this->tpl_vars['list'] = $list;
		
		
		$this->tpl_vars['admin_enabled'] = $_SESSION['site_auth']['admin_user_id'] ?? false || ($this->app->user && $this->app->user->isRoot());	
		
	}



	/*
	function viewInvoice()
	{
		$order = $this->getOrder();
		$invoicedir = GW::s('DIR/REPOSITORY') . (GW_Config::singleton()->get("payments__ordergroups/invoice_directory_name") ?: 'invoices').'/';
		$fname="invoice-{$order->id}";
			
		shell_exec("cd '$invoicedir' && unzip '$fname.zip'");
		
		$html = file_get_contents($invoicedir."$fname.html");
		unlink($invoicedir."$fname.html");
		
		$pdf=GW_html2pdf_Helper::convert($html, false);

		header('Content-type: application/pdf');
		echo $pdf;		
		
		exit;
	}*/
	
	function viewPrepareInvoice()
	{
		$order = $this->getOrder();
		
		$this->prepareOrderForPay($order);
		
		
		$this->tpl_vars['item'] = $order;		
	}
	
	function viewInvoice()
	{
		$order = $this->getOrder(true);
		$response = $this->app->innerRequest("payments/ordergroups/invoicevars",['id'=>$order->id],[],['app'=>'admin','user'=>GW_USER_SYSTEM_ID]);	
		
		//if(isset($_GET['debug']))
		//	d::ldump($response);
				
		$vars = $response['vars'];
		
		if(isset($_GET['preinvoice']))
			$vars['preinvoice']=1;
		
		
		if($this->feat('vat'))
			$vars['VAT']=1;
		
		if(isset($_GET['vars']) && $this->app->user->isRoot())
			d::dumpas(['response'=>$response]);		
		
		$html = GW_Mail_Helper::prepareSmartyCode($response['tpl'], $vars);
		
		
		
		//$tmp = $this->mute_errors; $this->mute_errors = true;
		if(isset($_GET['head'])){
			//enable ln trans
			echo $this->smarty->fetch('head.tpl');
			echo $this->smarty->fetch('foot.tpl');
		}
		
		if(isset($_GET['html']))
			die($html);
		
		
		

		
		$pdf=GW_html2pdf_Helper::convert($html, false);
		//$this->mute_errors=$tmp;
		
		
		if(isset($_REQUEST['download'])){
			header("Content-Type: application/x-download");	
			$prfx = isset($_GET['preinvoice'])?'pre':'';
			$filename=GW::ln('/g/PAYMENT_BANKTRANSFER_DETAILS_PREFIX').$order->id.'_'.$prfx.'invoice.pdf';
			header('Content-Disposition: attachment; filename="'.$filename.'";');
			header("Accept-Ranges: bytes");
		}else{		
			header('Content-type: application/pdf');
		}
		
		echo $pdf;
		exit;	
	}
	
	function doInitCart()
	{
		if(GW::$globals['site_cart'] ?? FALSE)
			return GW::$globals['site_cart'];
		
		
		if(!$this->app->user)
			return false;
			
		$cart = $this->app->user->getCart();	
		
		
		if(!$cart)
			return new GW_Order_Group;
		
		$this->cart = $cart;
		
		$items = $cart->items;
		
		if($items)
		foreach($items as $item){
			
			if($item->expires && strpos($item->expires, "0000-00-00")===false ){
				
				if($item->expires_secs < 0){
					$this->setError($item->title.'  - '.GW::ln('/m/CART_ITEM_EXPIRED'));
					$item->delete();
					unset($mycart[$idx]);
				}
			
			}
		}
		
		GW::$globals['site_cart'] = $cart;
		$this->order = $cart;
		
		return $cart;
	}
		
	
	
	function doSaveDelivery()
	{
		//$this->cartSave2LongStorage();
		$order = $this->doInitCart();
		
		
		$vals = $_POST['order'];
		unset($vals['user_id']);
		unset($vals['pay_confirm_id']);
		unset($vals['id']);
		unset($vals['payment_status']);
		unset($vals['status']);
		unset($vals['adm_processed']);
		unset($vals['secret']);
		unset($vals['admin_note']);
		
		
	
		
		$order->setValues($vals);
		
		$order->validate();
		$order->updateChanged();
		
		
		if($order->delivery_opt==1){
			$this->doCalcDelivery($order);
		}else{
			$order->amount_shipping = 0;
		}	
		
		
		
		
			

		
		$order->placed_time = date('Y-m-d H:i:s');
		$order->need_invoice = $_POST['order']['need_invoice'] ? 1 : 0;
		
			
		if($order->validate()){
			$order->status = 2;
			
			$order->save();
		}else{
	
			$this->setErrorItem($_POST['order'], 'delivery');
			
			foreach($order->errors as $error)
				$this->setError($error);			
			
			$order->save();
			$this->app->jump(false, ['step'=>2]);
		}		
		
		
		if($this->order->amount_coupon){
			$this->order->setCoupon(); // update coupon use its maximum
		}
		
		$order->updateTotal();
		

		///d::dumpas($this);
		$this->app->jump(false, ['step'=>3]);
	}	
	
	
	function viewCart()
	{		
		$order = $this->doInitCart();
		
		
		if(!$this->expirityChecks($order))
			$this->app->jump();//should return back
	
		
		
		if(!$order->items && $step!=3){
			$this->app->jump('direct/orders/orders');
		}
		
		if($order->deliverable)
			$this->expandDeliveryOpts();
		
		$this->tpl_vars['page_title'] = GW::ln("/m/SHOPPING_CART");
	
		$step = ($_GET['step'] ?? false);
		$this->tpl_vars['step'] = $step;
		
		
		
		
		if($step==2){
				
			$method = "deliveryView".$this->config->delivery_algo;
			
			if(method_exists($this, $method)){
				$this->$method($order);
			}else{
				$this->setError("Please check admin panel. Pick delivery algorithm");
			}
		}
		$this->tpl_vars['item'] = $order;
		
		$this->tpl_vars['order'] = $order;
	}	
	
	
	function deliveryViewNatos($order)
	{
		$erritem = $this->getErrorItem('delivery');

		if($erritem){
			$order->setValues($erritem);
		}else{


			if(!$order->name || !$order->email)
			if($last = GW_Order_Group::singleton()->find(['user_id=? AND reuse_addr=1', $this->app->user->id])){

				$fields = 'email,name,surname,company,phone,country,region,city,address_l1,postcode,company_code,vat_code,company_addr';
				$fields = explode(',',$fields);

				foreach($fields as $field)

					$order->set($field,$last->$field);	

			}
		}


		if($this->config->international_delivery)
			$this->options['country'] = GW_Country::singleton()->getOptions($this->app->ln == 'lt' ? 'lt': 'en');			
	}
	
	function deliveryViewOrderPrint($order)
	{
		$erritem = $this->getErrorItem('delivery');

		if($erritem){
			$order->setValues($erritem);
		}else{


			if(!$order->name || !$order->email)
			if($last = GW_Order_Group::singleton()->find(['user_id=? AND reuse_addr=1', $this->app->user->id])){

				$fields = 'email,name,surname,company,phone,country,region,city,address_l1,postcode,company_code,vat_code,company_addr';
				$fields = explode(',',$fields);

				foreach($fields as $field)

					$order->set($field,$last->$field);	

			}

		}
		
		$this->options['country'] = GW_Country::singleton()->getOptions($this->app->ln == 'lt' ? 'lt': 'en');
		
		
		if(isset($_GET['deliverycountry'])){
			
			$this->order->amount_shipping = $this->doCalcDeliveryOrderPrint($order, $_GET['deliverycountry']);
			
		}else{
			$this->order->amount_shipping = 0;
		}
		
		//siuntimas
		$this->order->delivery_opt=1;
		
		
		$this->order->updateChanged();
	}	
	
	
	function doCalcDeliveryOrderPrintEx($order, $countryid, $item, $qty)
	{
		$executers=Shop_Executors::singleton()->findAll(['active=1']);
		
		$options = [];
		
		foreach($executers as $ex){
			if($ex->serv_countries){
				$countries = json_decode($ex->serv_countries, true);
				$countries = array_flip($countries);
				
				if(!isset($countries[$countryid])){
					continue;
				}
			}
			
			
			$prices  = Shop_ShipPrice::singleton()->findAll(['owner_id=?', $ex->id], ['key_field'=>'id']);
			
			foreach($prices as $price){
				if($price->qty_min <= $qty && $price->qty_max >= $qty){
					$options[$ex->id.'_'.$price->id] = $price->price;
				}
			}
			
		}
		
		$ship_price = min($options);
		
		$options = array_flip($options);
		
		
		$selected_executor = $options[$ship_price];
		
		list($executerid, $priceid) = explode('_', $selected_executor);
		$order->extra = ['executerid'=>$executerid, 'shippriceid'=>$priceid];
		
		
		$item->executor_id = $executerid;
		$item->updateChanged();
		
		return $ship_price;
		//
	}
	
	function doCalcDeliveryOrderPrint($order, $country)
	{
		$country = GW_Country::singleton()->find(['code=?', $country]);
		
			
		$sum =0;
		
		foreach($order->items as $oi){
			if($oi->context_obj_type!='shop_products')
				continue;
			$sum += $this->doCalcDeliveryOrderPrintEx($order, $country->id, $oi, $oi->qty);
			
		}
		
				

		return $sum;
	}
	
	
	function applyDiscountCode($order)
	{
		if($discode = $_POST['discountcode'] ?? false){
			
			$curdate = date('Y-m-d');
			//nuolaidu kuponai
			$dc = Shop_DiscountCode::singleton()->find([
			    'code=? AND active=1 AND user_id=0 AND used=0 AND products!="" AND valid_from<=? AND expires>=?', $discode, $curdate, $curdate
			]);
			
		
			
			if($dc){
				$discount_productids = array_flip($dc->product_ids);
				
				//gauti produktu sarasa
				foreach($order->items as $oi)
					if(isset($discount_productids[$oi->obj_id]))
						$applicatable_prods[]=$oi->id;
				

				
				
				if(!$applicatable_prods){
					$this->setError(GW::ln('/m/DISCOUNT_CODE_IS_CORRECT_BUT_NO_PRODS_APPLICATABLE'));
				}else{
					$order->discount_id = $dc->id;
					$order->updateChanged();
					if($dc->singleuse){
						$dc->used  = 1;
						$dc->user_id = $this->app->user ? $this->app->user->id : -1;
					}
					$dc->use_count = $dc->use_count+1;
					$dc->update();
					
					$this->setOrderedItemPrices($order);
				}				
			}
			
			
			//dovanu kuponai 
			$coupon = Shop_DiscountCode::singleton()->find(['code=? AND active=1 AND products="" AND limit_amount-used_amount > 0 ', $discode]);


			if($coupon){
				$order->setCoupon($coupon);
			}
								
			if(!$coupon && !$dc)
				$this->setError(GW::ln('/m/INVALID_DISCOUNT_CODE'));
				
						
		}		
	}
	
	
	//is natos.lt
	//panasu kad po kiekvieno veiksmo su krepseliu buna perskaiciuojamos kainos
	//vietas kuriose perskaiciuojama paziuret natos.lt/applications/site/modules/products
	function setOrderedItemPrices($order)
	{
		$applicatable_prods = [];
		$discountcode = false;
		$price_total = 0;
		$discount_total = 0;
		
		if($order->discount_id){
			$discountcode = $order->discountcode;
			$applicatable_prods = array_flip($discountcode->product_ids);
			//d::dumpas($dc);
			
			//d::dumpas(['applicatable'=>$applicatable_prods, 'cartitems'=>$order->items]);
		}
		
		
		
		foreach($order->items as $order_itm){
			
			
			
			//galima butu salygini koda irasyt su expressions
			/*
				{if $elm->conditions}
					{*$elm->conditions*}
					{*var_dump(GW_Expression_Helper::singleton()->evaluate($elm->conditions, $answerarr))*}
					
					{if !GW_Expression_Helper::singleton()->evaluate($elm->conditions, $answerarr)}
						{continue}
					{/if}
				{/if}						
			*/
							
			
			if(!isset($applicatable_prods[$order_itm->obj_id]) || $order_itm->obj_type!=$discountcode->obj_type){
				
				//reset discounts
				$order_itm->discount = 0;
				
			}else{
	
				if($discountcode->percent){
					$order_itm->discount = round($order_itm->unit_price * $discountcode->percent/100, 2);
				}
								
				
			}
			
			$order_itm->updateChanged();
		}
		
		//d::dumpas($this->cart_data['cart']);
		
		
		$order->updateTotal();
		$order->updateChanged();
	}	
	
	
	function doUnsetDiscount()
	{
		$order = $this->doInitCart();
		
		if(!$order->discount_id){
			
			$this->setError("Discount not set");
			$this->app->jump();
		}elseif(!$order->open){
			$this->setError("Cant unset discount order is closed");
			$this->app->jump();
		}else{
			$dc = $order->discountcode;
			$order->discount_id = 0;
			$order->amount_coupon = 0;
			
			
			$this->setOrderedItemPrices($order);
			
			$order->updateTotal();
			
			$dc->user_id = 0;
			$dc->used = 0;
			$dc->updateChanged();

			
			
			$this->setMessage(GW::ln('/m/DISCOUNT_CODE_UNSET_SUCCESS'));
			$this->app->jump();
		}
	}	
	
	
	function doSaveCart()
	{
		
		$order = $this->doInitCart();
		$order->use_lang = $this->app->ln;
		
		$vals = $_POST['cart'] ?? false;
				
		
		foreach($order->items as $citem)
		{
			if(!isset($vals[$citem->id])){
				$citem->delete();
			}else{
				if(isset($vals[$citem->id]['qty'])){
					$citem->qty = $vals[$citem->id]['qty'];
					$citem->update();
				}
			}
		}
		$order->updateTotal();
		
		
		if(isset($_POST['discountcode']) && $this->feat('discountcode'))
			$this->applyDiscountCode($order);
		
		
		$this->app->jump(false, ['step'=>$_POST['step']]);
	}



	
	
	
	function doSaveBankTransferConfirm()
	{
		$item = $this->getOrder();
		$vals = $_POST['item'];
		

		
		$permitfields =  array_flip(['pay_user_msg']);
		$this->filterPermitFields($vals,$permitfields);	
		
		$item->pay_type = "banktransfer";
		$item->setValues($vals);
		
		
		
		$this->prepareOrderForPay($item);
		
		$extra = (object)$item->extra;
		$extra->bt_confirm = date('Y-m-d H:i');
		$extra->bt_confirm_cnt = ($extra->bt_confirm_cnt??0) +1;
		$item->extra = $extra;		
		
		$item->updateChanged();
		

		
		$orderlink = GW::s("SITE_URL")."admin/lt/payments/ordergroups/".$item->id."/form";
		
		$text = "Vartotojas {$this->app->user->title} praneša apie atliktą mokėjimą<br>";
		$text = "Peržiūrėti ir įskaityti mokėjimą galima admin aplinkoje <a href='$orderlink'>$orderlink</a><br />";
		
		if($item->pay_user_msg)
		{
			$msg = htmlspecialchars($item->pay_user_msg);
			$text.="<hr>Vartotojo žinutė apie mokėjimo atlikimą: <b>$msg</b>";
		}		
		
		if($item->banktransfer_confirm)
		{
			$img = $item->banktransfer_confirm;
			$img_url = GW::s("SITE_URL")."tools/img/{$img->key}";
				
			$text.="<br><hr>Prisegtas bankinio pavedimo <a href='{$img_url}'>patvirtinimas</a>. ";
			$text.="<br><img src='{$img_url}' style='max-width: 500px'>";
		}

			
		$mail=[
		    'subject'=>'Bankinis pavedimas '.$item->id. " nuo ".$this->app->user->title,
		    'body'=>$text
		];
		
		GW_Mail_Helper::sendMailAdmin($mail);			
		//GW_Mail_Helper::sendMailDeveloper($mail);
		$item->status = 3; //	Waiting wire transfer confirm
		$item->updateChanged();
		
			
		
		$this->setMessage(GW::ln('/m/MESSAGE_SENT_YOUR_PAYMENT_WILL_BE_VERIFIED_SOON'));
		
		if($tmp=$this->app->sess('after_order_'.$item->id)){
			Navigator::jump($tmp['after_bank_transfer_confirm']);
		}
		
		$this->app->jump(false, $_GET);
	}
	
	function doTestAccept()
	{
		$data = json_encode(['get'=>$_GET, 'post'=>$_POST, 'server'=>$_SERVER,'date'=>date('Y-m-d H:i:s')], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		file_put_contents(GW::s('DIR/TEMP').'test_pay_accept', $data);
		
		d::dumpas($data);
	}
	
	
	function sortByField($field, $list, $prioritized_field_vals)
	{
			$grouped_by_field = [];
			
			foreach($list as $item){
				$grouped_by_field[$item->$field][] = $item;
			}
			
			$sorted_by_field =  [];
			
			foreach($prioritized_field_vals as $fieldval)	
				if(isset($grouped_by_field[$fieldval])){
					$sorted_by_field[$fieldval] = $grouped_by_field[$fieldval];
					unset($grouped_by_field[$fieldval]);
				}
				
			$list1 = [];
			
			foreach($sorted_by_field as $key => $sublist)
				foreach($sublist as $item)
					$list1[]=$item;
			
			foreach($grouped_by_field as $key => $sublist)
				foreach($sublist as $item)
					$list1[]=$item;
			
			return $list1;
	}
	
	function prepareMergedPay($amount)
	{
		$cfg = new GW_Config('payments__mergedpaymethods/');
		$cfg->preload('');
		$default_country = $cfg->get('default_country_'.$this->app->ln) ?: 'LT';
		$country = $_GET['paycountry'] ?? $default_country;
			
	
			
		$list0 = GW_Pay_Methods::singleton()->findAll(
			['active=1 AND (country=? OR country="" OR country="oth") AND (min_amount=0 OR min_amount <= ?) AND (max_amount=0 OR max_amount>?)', $country, $amount, $amount],
			['priority ASC']
		);
		$list = [];
		
		
		$disabled_group = array_flip((array)json_decode($cfg->get('disabled_group'), true));
		if($disabled_group){
			foreach($list0 as $idx => $item)
				if(isset($disabled_group[$item->group]))
					unset($list0[$idx]);
		}		
		
		
		$cols=array_flip(GW_Country::singleton()->getColumns());
		
		
		$extracond = isset($cols['fake'])?'fake=0' : false;
		
		$countries0 = GW_Country::singleton()->getOptions($this->app->ln == 'lt' ? 'lt': 'en', $extracond);	

		$countries = [];
		$active_country = GW_Pay_Methods::singleton()->getDistinctVals('country');
		foreach($active_country as $cc)
			$countries[strtoupper($cc)] = $countries0[strtoupper($cc)] ?? $cc;
		
		
		
		
		if($cfg->all_countries){
			$countries = [];
			
			
			foreach($countries0 as $cc => $title)
				$countries[strtoupper($cc)] = $title;			
			
		}
		
	
		
		return ['methods'=>$list0,'country_opt'=>$countries,'country'=>$country];
	}
	
	
	function expandDeliveryOpts()
	{
		$opts = [];
		
		
		
		$this->tpl_vars['delivery_opts'] = $opts;
	}
	
	

	function doCalcDelivery($order)
	{
		
		$data = $this->app->innerRequest("payments/delivery",
			['format'=>'json','act'=>'doCalculateDelivery','order_id'=>$order->id],[],
			['app'=>'admin', 'user'=> GW_USER_SYSTEM_ID]
		);
		
		if($data['response_format_error'])
			d::dumpas($data);
		
		$order->amount_shipping = $data['amount_shipping'];
	}
	
	function setOrUpdateDelivery()
	{
		//2020-11-29
		$this->order->user_id = $this->app->user->id;
		
			
		d::dumpas();
		
		
		if($this->order->delivery_opt==1){
			$this->doCalcDelivery();
		}else{
			$this->order->amount_shipping = 0;
		}		
		
		$this->order->placed_time = date('Y-m-d H:i:s');
		
		$this->order->need_invoice = ($_POST['order']['need_invoice'] ?? false) ? 1 : 0;
		
		
		
		
		if($this->order->amount_coupon){
			$this->order->setCoupon(); // update coupon use its maximum
		}
		
		$this->order->calcAmountTotal();
		$this->setOrderedItemPrices();		
		$this->order->updateChanged();
	}
	
	function doSaveDelivery2()
	{
		//$this->cartSave2LongStorage();
		$this->doInitCart();
		
				
		$this->order->setValues($_POST['order']);
		
		$this->setOrUpdateDedoSalivery();
		
		
		if($this->order->validate()){
			$this->order->status = 2;
			
			$this->order->updateChanged();
		}else{
	
			$this->setErrorItem($_POST['order'], 'delivery');
			
			foreach($this->order->errors as $error)
				$this->setError($error);			
			
			$this->order->save();
			$this->app->jump(false, ['step'=>2]);
		}		
		
		
		

		

		///d::dumpas($this);
		$this->app->jump(false, ['step'=>3]);
	}




	
	function processGiftCoupons($products, $oitems, $order)
	{
		
		$config = new GW_Config('products/');
	
		
		$giftcoupontype=$config->giftcoupon_prodtype;
		$dc0 = Shop_DiscountCode::singleton();
		$codesall = [];
		
		$oitemsbyprod = [];
		foreach($oitems as $oi)
			if($oi->obj_type=='nat_products' && $oi->obj->prodtype_id == $giftcoupontype){
				$p = $oi->obj;
				
				//jei jau issaugoti kodukai tai pasiims is duombazes, 
				//jei sita praleist tai susigeneruos kas kart vis nauji kodai, dideli nuostoliai butu...
				if($codes = $order->get("keyval/codes_{$p->id}")){
					$codesall[$p->id] = explode(',',$codes);
					//d::ldump("{$p->id} skip $codes");
					continue; //important
				}
				//$order->set('keyval/test','fa32da1fa6sd51');
	
				$codes = [];
				$cids = [];
				
				for($i=0;$i<$oi->qty;$i++){
					$code =  $dc0->getUniqueCode(8);
					$coupon = $dc0->createNewObject();
					$coupon->code = $code;
					$coupon->limit_amount = $oi->unit_price;
					$coupon->used_amount = 0;
					$coupon->percent = 100;
					$coupon->active = 1;
					$coupon->user_id = $this->app->user ? $this->app->user->id : 0;
					$coupon->insert();
					$codes[] = $code;
					$cids[] = $coupon->id;
					
				}
				
				$codesall[$p->id] = $codes;				
				
				$order->set("keyval/codes_{$p->id}", implode(',', $codes));	
				$oi->keyval->coupon_codes = implode(',',$cids);
			}
		
		
		return $codesall;
	}
	
	function doDownload()
	{
		
		$order = $this->getOrder(true);

		if($order->payment_status!=7 && $order->amount_total){
			$this->setError("Cant download pdf scores, payment is still in waiting state");
			$this->jump(false);
		}
		
		$oitems = $order->items;
		$ids = [];
		foreach($oitems as $oi)
			$ids[]=$oi->obj_id;
		
		
					
		if(!$ids){
			$this->setError("Cant download pdf scores, no scores found");
			$this->jump(false);			
		}
		
		$enatos = Nat_Products::singleton()->findAll(GW::db()->inCondition('id', $ids). " AND enatos=1" , ['key_field'=>'id']);
		
		$giftcoupons = $this->processGiftCoupons($enatos, $oitems, $order);
		

		
		if(!$enatos){
			$this->setError("Cant download pdf scores, no e-scores found");
			$this->jump(false);			
		}
		
		$ids = array_keys($enatos);
		
		$name = ($order->company ? $order->company.' ' :''). ($order->name ? $order->name.' '.$order->surname : $order->user->title);
		
		//d::dumpas($name);
		
		$result = Navigator::sysRequest('admin/lt/products/items',[
		    'act'=>'doBuildDownload',
		    'ids'=>implode(',', $ids), 
		    'name'=>$name,
		    'giftcoupons' => json_encode($giftcoupons),
		    'email'=>$order->email ?: $order->user->email,
		    'order_id'=>$order->id
		]);
		
		
	
		if(!$result->filepath){
			$this->setError("Cant download pdf scores, system failure, please contact admin info@natos.lt");
			
			
			if($this->app->user && $this->app->user->isRoot()){
				d::dumpas($result,['hidden'=>'root user debug reply']);
			}
			
			
			$this->jump(false);
		}
		ob_clean();
		header('Content-type:  application/zip');
		header('Content-disposition: attachment; filename="'.basename($result->filepath).'"');
		echo file_get_contents($result->filepath);
		exit;
		//d::dumpas($result);			
	}	
	
	
	function getOrderedItems($order, $args)
	{
		$reqargs = ['export'=>'json','id'=>$order->id];
		if(isset($args['req_args']))
			$reqargs = array_merge($reqargs, $args['req_args']);
			
		$result = Navigator::sysRequest('admin/lt/payments/ordergroups/oitems',$reqargs);
		$html = $result->html ?? $result->raw_response;
		
		
		if(isset($args['debug']))
			die(json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

		if(isset($args['justhtml']))
			return $html;
		
		if(isset($args['justhtml1']))
			die($html);		
		
		if(isset($args['returnoutput'])){
			return GW_html2pdf_Helper::convert($html, false);

		}elseif($args['viewable']){
			Header('Content-type: application/pdf');
			echo GW_html2pdf_Helper::convert($html, false);
			exit;
		}else{
			GW_html2pdf_Helper::convert($html);
			exit;
		}		
	}
	
	function doOrderSummary()
	{
		$order = $this->getDataObjectById();	
		$this->getOrderedItems($order, $_GET);
		//d::dumpas($html);		
	}	
	
	
	
	
	function viewPayEmbed()
	{

		$order = GW_Order_Group::singleton()->find(['id=?', $this->args['id']]);
		
	

		$this->tpl_vars['item'] = $order;
		$this->tpl_vars['order'] = $order;		
	}
	
}



/*
 * per daug sudetinga manau
//priority gateway
			$priority_gateway = json_decode($cfg->get('priority_gateway'), true);
			$priority_group = json_decode($cfg->get('priority_group'), true);
			$disabled_group = array_flip(json_decode($cfg->get('disabled_group'), true));
			
			$list1 =  $this->sortByField('gateway', $list0, $priority_gateway);
			$list2 = [];
			
			foreach($list1 as $item){
				$list2[$item->aliaskey ?: $item->key] = $item;
			}			
			
			
			
			$list3=  $this->sortByField('group', $list2, $priority_group);
			

			if($disabled_group){
				foreach($list3 as $idx => $item)
					if(isset($disabled_group[$item->group]))
						unset($list3[$idx]);
			}
			
			
			$list4 = [];
			foreach($list3 as $item)
				$list4[$item->gateway][$item->group][] = $item;
	
			
			//d::dumpas($list4);
			
			$list5 = [];
			foreach($list4 as $gatewaylist){
				foreach($gatewaylist as $grouplist){
					
					GW_Array_Helper::objSortByField('priority', $grouplist);
					//d::dumpas($grouplist);
					$list5 = array_merge($list5, $grouplist);
				}
			}

			
			return $list5;
 */