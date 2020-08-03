<?php


class Module_Requests extends GW_Public_Module
{
	
	function init()
	{		
		$this->model = GW_Payments::singleton();
		
		parent::init();
		
		$this->tpl_dir .= $this->module_name."/";
		
		$this->tpl_vars['page_title'] = GW::ln("/m/VIEWS/payments");
		

		
		$this->config = new GW_Config($this->module_path[0] . '/');
		$this->config->preload('');			
	}
	
	//function viewDefault()
	//{
		
	//}	
	
	function viewDefault()
	{
		
		
		$request = GW_Payments::singleton()->find(['`key`=?', $_GET['key']]);
		
		$args = $_GET; unset($args['pay']);
		$payurl = $this->app->buildUri(false, $args);

		$this->tpl_vars['request'] = $request;
		$this->tpl_vars['payurl'] = $payurl;		
		
		if(!$request){
			//"Payment request is expired or link is invalid"
			$this->setError('/m/PAYMENT_INVALID');
			return;
		}
		
		if(isset($_GET['pay']) && $_GET['pay']==7){
			$this->setMessage(GW::ln('/m/PAYMENT_ACCEPTED'));
			return;
		}
				
		if($request->status==7){
			$this->setMessage(["text"=>GW::ln('/m/PAYMENT_ALREADY_PAYD'), 'type'=>GW_MSG_WARN]);
			return;
		}
		
		if($request->active!=1){
			$this->setError(GW::ln('/m/PAYMENT_DISABLED'));
			return;
		}		
		
		if(isset($_GET['pay']) && $_GET['pay']==6){
			$this->setMessage(["text"=>GW::ln('/m/PAYMENT_CANCELED'), 'type'=>GW_MSG_WARN]);
		
			return;
		}
		
		
		
		
		$this->doPay($request);
		
	}	

	
	function doPayPaysera($args) 
	{
		$request=$args->request;

		$cfg = new GW_Config("datasources__payments_paysera/");	
		$cfg->preload('');
		 
		$data = array(
		    'projectid' => $cfg->paysera_project_id,
		    'sign_password' => $cfg->paysera_sign_password,
		    'orderid' => $request->id,
		    'paytext' => $args->paytext,
		    //'p_firstname' => $order->name,
		    //'p_lastname' => $order->surname,
		    'p_email' => $request->customer_email,
		    'amount' => $request->amount * 100,
		    'currency' => "EUR",
		    'country' => 'LT',
		    'accepturl' => "{$args->base}service/paysera?action=accept&handler=payments&{$args->succ_url}7",
		    'cancelurl' => "{$args->base}service/paysera?action=cancel&handler=payments&{$args->cancel_url}6",
		    'callbackurl' => "{$args->base}service/paysera?action=callback&handler=payments",
		    'test' => $cfg->paysera_test || $request->title == 'paytest',
		);

		if($this->app->ln == 'ru')
			$data['lang'] = 'rus';
		
		if($this->app->ln == 'en')
			$data['lang'] = 'eng';
		
		//d::dumpas($data);


		WebToPay::redirectToPayment($data);
	}
	
	function doPayPal($args) {

		$order= $args->order;
		
		
		$cfg = new GW_Config("datasources__payments_paypal/");	
		$cfg->preload('');		

		$test = $cfg->pay_test || $order->name == 'paytest';
		$paypal_email = $test ? $cfg->paypal_test_email : $cfg->paypal_email;
		//$paypal_email = $cfg->paypal_email;
		
		$amount = $request->amount;
		
		if($test)
			$amount = 0.10;
		
		$vars = array(
		    'cmd' => "_xclick",
		    'business' => $paypal_email,
		    'item_number' => 1,
		    'amount' => $amount,
		    'currency_code' => $cfg->default_currency_code,
		    'notify_url' => "{$args->base}service/paypal?action=callback&handler=orders&orderid={$order->id}",
		    'return' => "{$args->base}service/paypal?action=accept&handler=orders&orderid={$order->id}&{$args->succ_url}6", //sugrazinti i paypal_return kuriame bus informuojama ar gautas mokejimas ar ne
		    'cancel_return' => "{$args->base}service/paypal?action=cancel&handler=orders&orderid={$order->id}&{$args->cancel_url}6", //sugrazinti i mokejimo zingsni
		    'charset' => 'utf-8',
		    'cpp_header_image' => "{$args->base}application/site/assets/img/natos_logo_big_trans_400.png",
		    'item_name' => $args->paytext,
		);
		    
		//d::dumpas($vars);



		header('Location: https://www.' . ($test ? 'sandbox.' : '') . 'paypal.com/cgi-bin/webscr?' . http_build_query($vars));
		exit;
	}

	function doPay($req)
	{		
		
		$args = (object)[
		    'succ_url'=>'redirect_url=' . urlencode($this->app->buildURI('direct/payments/requests', ['key'=>$req->key,'pay'=>7,'absolute' => 1])),
		    'cancel_url'=>'redirect_url=' . urlencode($this->app->buildURI('direct/payments/requests', ['key'=>$req->key,'pay'=>6,'absolute' => 1])),
		    'base'=> Navigator::getBase(true),
		    'request'=>$req,
		    'paytext'=>$req->id . ' ' . $req->title,
		    'items_number'=>1,
		];
		
		
		switch($_GET['gw'] ?? 'paysera'){
			case '2':
				$this->doPayPal($args);
			break;
			case '3':
				$this->doPayCC($args);
			case '4':
				$order->pay_type = 4;
				$order->pay_status = 0;
				$order->updateChanged();
				
				$this->__sendProformaInvoiceMail($order);
				
				$url = $this->app->buildURI('direct/products/orders/paywiretransfer',['id'=>$order->id]);
				
				
				header('Location: '.$url);
				exit;
			break;		
			case '1':
			default:
				$this->doPayPaysera($args);
		}
	}

	
	
	function viewPayWireTransfer()
	{
		$order = $this->getDataObjectById();
		$this->tpl_vars['amount_total'] = $order->amount_total;
		$this->tpl_vars['order'] = $order;
		$this->tpl_vars['item'] = $order;
		$this->initPaycc();
	}
	
	function doSaveWireTransferConfirm()
	{
		$order = $this->getDataObjectById();
		$order->wiretransfer_userconfirm = strip_tags($_POST['item']['wiretransfer_userconfirm']);
		
		if($order->status < 3 && $order->wiretransfer_userconfirm > 0)
			$order->status=3;		
		
		$order->updateChanged();

		
		$this->jump('direct/products/orders/list', ['id' => $order->id]);
		
	}
	
	function doPayCC($args)
	{
		$this->jump('direct/products/orders/paycc',['id'=>$args->order->id]);
	}
	
	
	function initPaycc()
	{
		$this->getList(['extra_cond'=>'id='.(int)$_GET['id'], 'item_id'=>$_GET['id']]);	
	}
	
	function viewPayCC()
	{
		$this->tpl_vars['breadcrumbs_attach'][] =  ['title'=>GW::ln('/m/PAY_CREDIT_CARD')];
		
		
		if($tmp = $this->getErrorItem('formcc')){
			$this->tpl_vars['item'] = (object)$tmp;
		}
		
		$this->initPaycc();
	}
	
	
	function sendAdminNewOrder($order)
	{
		$subj = "Moketi kortele - Nuskaityti suma";
		
		$opts = ['subject'=>'laiskas - bandymas', 'body'=>'labadiena'];
		$r = GW_Mail_Helper::sendMailAdmin($opts);
	}
	
	function doSaveCc()
	{
		$vals = $_POST['item'];
		$errors = false;
		$this->initPaycc();
		$order= $this->tpl_vars['order'];
		$paycc = GW_Pay_Creditcard::singleton()->createNewObject();
				
		
		$paycc->setValues([
		    'order_id'=>$order->id,
		    'amount'=>$order->amount_total,
		    'name'=>$vals['name'],
		    'surname'=>$vals['surname'],
		    'num_cvc_exp'=>$vals['number'].','.$vals['cvc'].','.$vals['expires'],
		    'card_type'=>$vals['type']
		]);
		
		if(!$paycc->validate()){
			$this->setItemErrors($paycc);
			$this->setErrorItem($vals, "formcc");
			$this->jump(false, $_GET);
		}else{
			$paycc->insert();
			$paycc->crypt();
			
			$order->pay_type = 3;
			$order->pay_status = 5;
			$order->pay_confirm_id = $paycc->id;
			$order->updateChanged();
			
			//$this->sendAdminNewOrder($order);
			$this->__sendOrderAccept($order);
		}				
		
		$this->setMessage('/m/CREDIT_CARD_CHARGE_FORM_SAVED');
		//d::dumpas("allgood all saved, now need to jump to orders list with order id {$order->id}");
		$this->jump('direct/products/orders',['id'=>$order->id]);
	}


	
	function __sendOrderAccept($order) 
	{		
		$result = Navigator::sysRequest('admin/lt/products/orders',['act'=>'doSendOrderAccepted','id'=>$order->id]);
		
		$this->setMessage($result->resp);
	}	

	
	
	
	function getOrderedItems($order, $args)
	{
		$reqargs = ['export'=>'json','id'=>$order->id];
		if(isset($args['req_args']))
			$reqargs = array_merge($reqargs, $args['req_args']);
			
		$result = Navigator::sysRequest('admin/lt/products/orders/oitems',$reqargs);
		$html = $result->html ?? $result->raw_response;
		
		if(isset($args['debug']))
			die(htmlspecialchars ($html));

		if(isset($args['justhtml']))
			return $html;

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
	
	function doPrint()
	{
		$order = $this->getDataObjectById();	
		$this->getOrderedItems($order, $_GET);
		//d::dumpas($html);		
	}
	
	
	function jump($path=false, $args=[])
	{
		$this->app->jump($path, $args);
	}
	
	function doDownloadPdfs()
	{
		$cond = $this->getOwnerCond();
		$order = Shop_Orders::singleton()->find($cond.' AND '. GW_DB::prepare_query(['id=?', $_GET['id']]));	
		
		if($order->pay_status!=7){
			$this->setError("Cant download pdf scores, payment is still in waiting state");
			$this->jump(false);
		}
		
			
		$ids = array_keys($order->getOrderedItems());
					
		if(!$ids){
			$this->setError("Cant download pdf scores, no scores found");
			$this->jump(false);			
		}
		
		$enatos = Nat_Products::singleton()->findAll(GW::db()->inCondition('id', $ids). " AND enatos=1" , ['key_field'=>'id']);
		
		if(!$enatos){
			$this->setError("Cant download pdf scores, no e-scores found");
			$this->jump(false);			
		}
		
		$ids = array_keys($enatos);
		
		
		$result = Navigator::sysRequest('admin/lt/products/products',[
		    'act'=>'doBuildDownload',
		    'ids'=>implode(',', $ids), 
		    'name'=>($order->company ? $order->company.' ' :'').$order->name.' '.$order->surname,
		    'email'=>$order->email,
		    'order_id'=>$order->id
		]);
		
		if(!$result->filepath){
			$this->setError("Cant download pdf scores, system failure, please contact admin info@natos.lt");
			
			
			$this->jump(false);
		}
		
		header('Content-type:  application/zip');
		header('Content-disposition: attachment; filename="'.basename($result->filepath).'"');
		echo file_get_contents($result->filepath);
		exit;
		//d::dumpas($result);			
	}
	
	
	function __sendProformaInvoiceMail($order) 
	{
		$template_id = $this->config->proforma_invoice_default;

		$oi_html = $this->getOrderedItems($order,['justhtml'=>1, 'req_args'=>['tpl'=>'oitems_onerow'] ]);
		
		$vars = [
		    'INVOICE_NUM' => date('Ymd') . '-' . $order->id,
		    'DATE' => date('Y-m-d'),
		    'PAY_TILL' => date('Y-m-d', strtotime('+3 day')),
		    'FULLNAME' => $order->name . ' ' . $order->surname,
		    'PRICE' => $order->amount_total . ' ' . $this->config->default_currency_code,
		    'PRICE_SHIPPING'=> $order->amount_shipping . ' ' . $this->config->default_currency_code,
		    'PRICE_TEXT' => GW_Sum_To_Text_Helper::sum2text($order->amount_total, $this->app->ln),
		    'PART_COUNTRY' => GW_Country::singleton()->getCountryByCode($order->country, $this->app->ln == 'lt' ? 'lt' : 'en'),
		    'PART_CITY' => $order->city,
		    'ORDERED_ITEMS' => $oi_html,
		    'PAY_REASON_MESSAGE'=>"Natos{$order->id}",
			    
		];

		$r = $this->__sendMail2User($template_id, $vars);
	}
	
	function __sendMail2User($tpl_id, $vars, $contest=false) 
	{
		$tpl = GW_Mail_Template::singleton()->find($tpl_id);
				
		$opts = [
		    'to' => $this->app->user->email,
		    'tpl' => $tpl,
		    'vars' => $vars
		];
		
	
		//d::dumpas($opts);
				
		GW_Mail_Helper::sendMail($opts);

		return true;
	}	
	
}