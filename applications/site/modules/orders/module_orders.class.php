
<?php

class Module_Orders extends GW_Public_Module
{
	
	function init()
	{		
		$this->model = GW_Order_Group::singleton();
		
		parent::init();
		
		//$this->tpl_dir .= $this->module_name."/";

		
		
		
		
		$this->config = new GW_Config('payments/');
		$this->config->preload('');
		
		
		$this->tpl_vars['breadcrumbs_attach'][] =  [
		    'title' => GW::ln('/m/VIEWS/orders'),
		    'url' => $this->app->buildUri('direct/orders/orders')
		];		
		
		
		$this->initFeatures();
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
		$type = $_GET['type'] ?? $pay_methods[0];
		
		$args = (object)[
		    'succ_url'=>'redirect_url=' . urlencode($this->buildURI('', ['absolute' => 1,'act'=>'doCompletePay','id'=>$order->id,'key'=>$order->secret])),
		    'cancel_url'=>'redirect_url=' . urlencode($this->buildURI('', ['absolute' => 1,'act'=>'doCancelPay','id'=>$order->id])),
		    'base'=> Navigator::getBase(true),
		    'orderid'=>'order-'.$order->id,
		    'paytext'=> GW::ln('/g/CART_PAY',['v'=>['id'=>$order->id]]),
		    'payprice'=>$order->amount_total,
		    'items_number'=>count($citems),
		    'order'=>$order
		];			
			
		
		if($type=='paysera'){
			$this->doPayPaysera($args);		
		}elseif($type=='kevin'){
			$this->doPayKevin($args);
		}
	}
	

	function prepareOrderForPay($order)
	{
		$order->updateTotal();
		
		$order->setSecretIfNotSet();
		
		$citems = $order->items;
		

		//nebepridet pakartotinai
		if(!$order->items || $order->amount_total <= 0)
		{
			$this->setMessage("/g/CART_EMPTY");
			$this->app->jump();
		}
		
		//nebepridet pakartotinai

		//extend expirity time // etc
		
		foreach($order->items as $citem){
			if(($citem->obj->expirity_check_before_buy ?? false) && !$citem->obj->expirityCheck()){
				$this->setError($citem->obj->title." - ".GW::ln('/m/EXPIRED'));
				$this->jump('');
			}
			
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
	
	
	function doCompletePay()
	{
		$order = $this->getOrder(true);
		
		if($order->payment_status==7){
			$this->setMessage(GW::ln('/g/PAYMENT_COMPLETE'));
		}else{
			$this->setMessage(GW::ln('/g/PAYMENT_PROCESSING'));
		}
	
		if(!$app->user){
			$this->app->jump('/');
		}else{
			$this->app->jump('direct/orders/orders',['orderid'=>$order->id,'id'=>$order->id]);
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
		
		$order->active = false;
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
		$cart = $this->order;

		
		
		//nebepridet pakartotinai
		if($cart->items)
			foreach($cart->items as $citem){
				if($citem->id == $_GET['id']){
					
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
		
		$list = GW_Order_Group::singleton()->findAll(['user_id=? AND active=?', $this->app->user->id,$active],['order'=>'id DESC']);
		
		
		
		$this->tpl_vars['list'] = $list;
		
		
		$this->tpl_vars['admin_enabled'] = $_SESSION['site_auth']['admin_user_id'] ?? false;
	}
	
	function doPayPaysera($args) 
	{
		//$this->userRequired();

		$cfg = new GW_Config("payments__payments_paysera/");	
		$cfg->preload('');
		
		
		if(isset($args->user)){
			$user = $args->user;
		}else{
			$user = $this->app->user;
		}
		
		
		$handler = $args->handler ?? "orders";
		
		//if($user->id == 9)
		//	$args->payprice= 0.01;		
		
		$test=isset($_GET['testu6s15g19t8']) || $cfg->paysera_test || $args->order->city == 'paytest' || $user->city=="paytest";
				
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
		    'accepturl' => "{$args->base}service/paysera?action=accept&handler=$handler&{$args->succ_url}",
		    'cancelurl' => "{$args->base}service/paysera?action=cancel&handler=$handler&{$args->cancel_url}",
		    'callbackurl' => "{$args->base}service/paysera?action=callback&handler=$handler",
		    'test' => $test,
		);

		//d::dumpas($data);
				    
		if($this->app->ln == 'ru')
			$data['lang'] = 'rus';
		
		if($this->app->ln == 'en')
			$data['lang'] = 'eng';
		
		
		///d::dumpas($data);

		WebToPay::redirectToPayment($data);
		exit;
	}

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
		    'Redirect-URL' => $args->base.$this->app->ln."/direct/orders/orders?act=doAcceptKevin",
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
	
	function doAcceptKevin()
	{
		$paymentId = $_GET['paymentId'];
		
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
			$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?act=doMarkAsPaydSystem&sys_call=1&'. http_build_query($args));
		}else{
			$debugdata = ['response'=>$response,'paylog'=>$paylog->toArray()];
			$mail=[
				'subject'=>'Payment error amount_total in cart does not match kevin response',
				'body'=>"<pre>".json_encode($debugdata, JSON_PRETTY_PRINT)."</pre>"
			    ];
			GW_Mail_Helper::sendMailDeveloper($mail);
		}
		
		sleep(2);
		
		header('Location:'.$this->buildURI('', ['absolute' => 1,'act'=>'doCompletePay','id'=>$order->id,'key'=>$order->secret]));

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
		
		
		
		$vars = $response['vars'];
		
		if(isset($_GET['preinvoice']))
			$vars['preinvoice']=1;
		
		$html = GW_Mail_Helper::prepareSmartyCode($response['tpl'], $vars);
		
		
		
		//$tmp = $this->mute_errors; $this->mute_errors = true;
		
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
		if($GLOBALS['site_cart'] ?? FALSE)
			return $GLOBALS['site_cart'];
		
		
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
		
		$GLOBALS['site_cart'] = $cart;
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
		
			
		/*iskelti deliverio apskaiciavimo funkcija i konfiga ? */
		if($order->delivery_opt==1){
			$order->amount_shipping = $this->config->delivery_lt;
		}else{
			$order->amount_shipping = $this->config->delivery_no;
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
		
		$order->updateTotal();

		///d::dumpas($this);
		$this->app->jump(false, ['step'=>3]);
	}	
	
	function viewCart()
	{		
		$order = $this->doInitCart();
		
		
		
		if(!$order->items && $step!=3){
			$this->app->jump('direct/orders/orders');
		}
		
		//$this->expandDeliveryOpts();
		$this->tpl_vars['page_title'] = GW::ln("/m/SHOPPING_CART");
	
		$step = ($_GET['step'] ?? false);
		$this->tpl_vars['step'] = $step;
		
		
		
		
		if($step==2){
	
	
			$erritem = $this->getErrorItem('delivery');
		
			if($erritem){
				$order->setValues($erritem);
			}else{



				if($last = GW_Order_Group::singleton()->find(['user_id=? AND reuse_addr=1', $this->app->user->id])){

					$fields = 'email,name,surname,company,phone,country,region,city,address_l1,postcode,company_code,vat_code,company_addr';
					$fields = explode(',',$fields);

					foreach($fields as $field)
						$order->set($field,$last->$field);	

				}
				
			}




			if($this->config->international_delivery)
				$this->options['country'] = GW_Country::singleton()->getOptions($this->app->ln == 'lt' ? 'lt': 'en');	
			
			
			$this->tpl_vars['item'] = $order;
		}
		
		
	}	
	
	
	function doSaveCart()
	{
		
		$cart = $this->doInitCart();
		
		$vals = $_POST['cart'] ?? false;
				
		
		foreach($cart->items as $citem)
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
		$cart->updateTotal();
		
		
		
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
		
		$extra = $item->extra;
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
		
		
			
		
		$this->setMessage(GW::ln('/m/MESSAGE_SENT_YOUR_PAYMENT_WILL_BE_VERIFIED_SOON'));
		
		$this->app->jump(false, $_GET);
	}
	
}