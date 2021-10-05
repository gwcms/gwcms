<?php


class Module_OrderGroups extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	
	function init()
	{	
		$this->model = GW_Order_Group::singleton();
		
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		$this->app->carry_params['search']=1;
		$this->app->carry_params['composer_id']=1;
		$this->app->carry_params['clean']=1;
		
		if(isset($_GET['composer_id'])){
			$this->filters['composer_id'] = (int)$_GET['composer_id'];
			$this->list_params['paging_enabled']=0;	
		}
		
		$this->config =  new GW_Config($this->module_path[0].'/');	
	}
	

	
	
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields']['user_title'] = 'Lf';
		$cfg['fields']['changetrack'] = 'L';
		
		//d::dumpas($cfg);
					
		return $cfg;
	}
	
	function __eventAfterList(&$list)
	{		
		$this->attachFieldOptions($list, 'user_id', 'GW_User');

		foreach($list as $item)
		{
			$item->items_count = GW_Order_Item::singleton()->count(['group_id=?', $item->id]);
		}	
		
		if($list)
		foreach($list as $item)
			break;		
		
		if(isset($item) && $item){
			
			if($item->extensions['changetrack'])
				$item->extensions['changetrack']->prepareList($list);
		}		
	}
	
	

	
	function overrideFilterUser_title($value, $compare_type)
	{	
		$x=$this->__overrideFilterExObject("GW_User", "user_id", ["name","surname",'email'], $value, $compare_type);
		
		
		return $x;
	}
	
	/*
	function overrideFilterInstruments($value, $compare_type)
	{	
		d::Dumpas([$compare_type, $value]);
		
		$compare_type = "LIKE%,,%";
		
		return $x;
	}	*/
	
	
	
/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */	
	
	function initInvoiceVars($item)
	{
		
		$user =  $item->user;
		
		$payconfirm = $item->pay_confirm;
		if(!$payconfirm)
		{
			$this->setError("/m/NO_PAY_CONFIRM");
			$this->jump();
		}
		
		
		//d::dumpas(count($list));
		
		if($_GET['offset'] ?? false)
			$list = [$list[$_GET['offset']]];
		
		$tpl = GW_Mail_Template::singleton()->find(['idname=?', $this->modconfig->invoice_template]);
		
		if(!$tpl){
			$this->setError("Nenurodytas sąskaitos šablonas, modulio žiūrėti nustatymuose");
			$this->jump();
		}
		
		$tpl_code = $tpl->get("body_lt");
		
		
		$v =& $this->tpl_vars;
		
		if(!$item->secret){
			$item->secret = GW_String_Helper::getRandString(8,GW_String_Helper::$simple);
			$item->updateChanged();
		}
		
		
		$attachuservars = function(&$v, $user){
			$v['FULLNAME'] = $user->title;
			$v['CITY'] = mb_convert_case($user->city, MB_CASE_TITLE, 'UTF-8');
			$v['COUNTRY'] = GW_Country::singleton()->getCountryByCode($user->country, 'lt');
			$v['PHONE'] = $user->phone;
		};
		

			
		$build = false;
		$v = [];
		$v['PRICE'] = $item->amount_total;
		$v['PRICE_TEXT'] = GW_Sum_To_Text_Helper::sum2text($v['PRICE'], 'lt');

		$v['COMPANY'] = $item->company;
		$v['COMPANY_ID'] = $item->company_code;
		$v['COMPANY_VAT_ID'] = $item->vat_code;
		$v['COMPANY_ADDR'] = $item->company_addr;
		
		$v['INVOICE_NUM'] = GW::ln('/g/PAYMENT_BANKTRANSFER_DETAILS_PREFIX').'-'.$item->id;
		$v['DATE'] = explode(' ',$item->insert_time)[0];
		$v['EMAIL'] = $payconfirm->p_email ?: $item->email;
		$v['ITEMS'] = [];
		$v['ORDERID'] = $item->id;
		$v['SECRET'] = $item->secret;
		$v['SITE_DOMAIN'] = parse_url(GW::s('SITE_URL'), PHP_URL_HOST);
			//$pdf=GW_html2pdf_Helper::convert($html, false);			
			
		
		
			
		foreach($item->items as $oitem){
			
			$v['ITEMS'][] = [
			    'title'=> $oitem->invoice_line, 
			    'type'=> $oitem->type, 
			    'qty'=>$oitem->qty, 
			    'unit_price'=>$oitem->unit_price, 
			    'total'=>$oitem->total
			];
		}
		
		if($user->id){
			$attachuservars($v, $user);
			
			if(!$v['EMAIL'])
				$v['EMAIL'] = $user->email;
		}else{
			$v['FULLNAME'] = $item->name.' '.$item->surname;
			$v['PHONE'] = $user->phone;			
		}
		
		return [$tpl_code, $v];
	}
	
	function viewInvoiceVars()
	{
		$item = $this->getDataObjectById();
		list($tpl_code, $v) = $this->initInvoiceVars($item);
		
		
		die(json_encode(['tpl'=>$tpl_code, 'vars'=>$v], JSON_PRETTY_PRINT));
	}
	
	function viewInvoice()
	{
		$item = $this->getDataObjectById();
		list($tpl_code, $v) = $this->initInvoiceVars($item);
			
			
			
		$html = GW_Mail_Helper::prepareSmartyCode($tpl_code, $v);
		
		
		
		$tmp = $this->mute_errors; $this->mute_errors = true;
		
		if(isset($_GET['html']))
			die($html);
		
		$pdf=GW_html2pdf_Helper::convert($html, false);
		$this->mute_errors=$tmp;

		header('Content-type: application/pdf');
		echo $pdf;
		exit;		
	}


	function doSaveInvoice($item=false)
	{
		if(!$item){
			$item = $this->getDataObjectById();
			$die=1;
		}else{
			$die=0;
		}
		
		list($tpl_code, $v) = $this->initInvoiceVars($item);
		
		$item->invoicevars = json_encode($v);
		$item->updateChanged();
		
		$dir = GW::s('DIR/REPOSITORY') . ($this->modconfig->invoice_directory_name ?: 'invoices').'/';
		
		@mkdir($dir);
		
		$html = GW_Mail_Helper::prepareSmartyCode($tpl_code, $v);
		$fname="invoice-{$item->id}";
		file_put_contents($dir.$fname.'.html', $html);
		
		shell_exec($cmd="cd '$dir' && unlink '$fname.zip' ; zip -Z bzip2 '$fname.zip' '$fname.html' && unlink '$fname.html'");
		
		
		if($die)
			exit;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
	}
	
	function doUpdateInvoices()
	{
		$list = $this->model->findAll();
		
		foreach($list as $item)
			$this->doSaveInvoice($item);
		
	}
	
	
	function doMarkAsPayd()
	{		
		$item = $this->getDataObjectById();
		
	
		$query = $_GET['rcv_amount'] ?? false;
		
		
		if($query != $item->amount_total)
		{
			$this->setError(GW::l('/m/RECEIVED_AMOUNT_DOES_NOT_MATCH'));
			$this->app->jump();
			return false;
		}
		
		

		
		$item->fireEvent('BEFORE_CHANGES');
		
		$item->payment_status=7;
		$item->updateChanged();		
		
		$this->doMarkAsPaydSystem($item);
		
		$this->setMessage('/m/PAYMENT_APPROVED');
	}

	
	
	function doMarkAsPaydSystem($order=false)
	{
		
		file_put_contents(GW::s('DIR/TEMP').'doMarkAsPaydSystem_test', json_encode($_GET));
		
		if(!$order)
			$order = $this->getDataObjectById();
		
		$log_entry_id = $_GET['log_entry_id'] ?? false;
		$rcv_amount = $_GET['rcv_amount'] ?? false;
		
		if($log_entry_id){
			
			$log_entry = GW_Paysera_Log::singleton()->find(['id=?', $log_entry_id]);
			$order->pay_type = 'paysera';
		}else{
			$log_entry = false;
		}
			
			
		if($rcv_amount != $order->amount_total){
			$order->status = "WrongAmount exp: $cart->amount_total rcv: $rcv_amount";
			$order->payment_status = 8;
		}else{
			$order->payment_status = 7;
		}

		foreach($order->items as $item){
			$obj = $item->obj;
			if($obj){
				$obj->orderItemPayd($item->unit_price, $item->qty, $log_entry);
			}
		}

		if(isset($_GET['paytest']))
			$order->pay_test =1;	

		$order->pay_confirm_id = $log_entry_id;
		$order->pay_time = date('Y-m-d H:i:s');

		$order->updateChanged();
		
		
		file_put_contents(GW::s('DIR/TEMP').'doMarkAsPaydSystem_test2', json_encode($order->toArray()));
			
			
		$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?id='.$order->id.'&act=doSaveInvoice&cron=1');	
		
		if($this->config->confirm_email_tpl)
			$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?id='.$order->id.'&act=doOrderPaydNotifyUser&cron=1');		
		
		return false;
	}
	
	
	function viewPaymentSummary()
	{
		$this->config = new GW_Config($this->module_path[0].'/');
		
		if(isset($_GET['date_from'])){
			$date_from = $_GET['date_from'];
			$this->config->date_from = $date_from;
		}elseif($this->config->date_from){
			$date_from=$this->config->date_from;
		}else{
			$date_from=date('Y-m-d', strtotime('-1 YEAR'));
		}
		
		if(isset($_GET['date_to'])){
			$date_to = $_GET['date_to'];
			$this->config->date_to = $date_to;
		}elseif($this->config->date_to){
			$date_to=$this->config->date_to;
		}else{
			$date_to=date('Y-m-d');
		}
		$this->tpl_vars['date_from'] = $date_from;
		$this->tpl_vars['date_to'] = $date_to;		
		
		$conds = ['payment_status=7 AND pay_test=0'];
				
		$conds[] = GW_DB::prepare_query(['pay_time >= ?', $date_from]);
		$conds[] = GW_DB::prepare_query(['pay_time <= ?', $date_to." 23:59"]);		
	
		
		
		$list = $this->model->findAll(implode(' AND ', $conds),['order'=>'id DESC','key_field'=>'id']);
		$order_ids = array_keys($list);
		

		
		
		
		$orderitems = GW_Order_Item::singleton()->findAll(GW_DB::inCondition('group_id', $order_ids));
		
		$this->tpl_vars['list'] = $list;
		$this->tpl_vars['orderitems']=GW_Array_Helper::groupObjects($orderitems,'group_id');
	}
	
	
	function doOrderPaydNotifyUser()
	{		
		$template_id = $this->config->confirm_email_tpl;
		
		
		$order = $this->getDataObjectById();
		
		
		list($invtpl, $vars) = $this->initInvoiceVars($order);
		
		
		//2kartus kad nesiusti laisko
		if($order->mail_accept){
			$this->setError("Already sent");
			return false;
		}else{

			$order->set('mail_accept',1);
			$order->updateChanged();
		}
		
		//$response = [];
		
		//$orderlink = 'https://natos.lt/lt/direct/products/orders/list?id='.$order->id;
			
		//if(!$order->user_id){
		//	$orderlink .= '&uid='.$order->secret;
		//}
		

		
		
			
		//$filename = "NATOS_ORDER_".$vars['ORDERID'].'.pdf';
		//$pdf =  $this->getOrderedItems($order, ['returnoutput'=>1]);
		
		$email = $order->email;
		if(!$email && $order->user && $order->user->email)
			$email = $order->user->email;
		
		
		
		$opts = [
		    'to'=>$email,
		    'tpl'=>GW_Mail_Template::singleton()->find($template_id),
		    'vars'=>$vars,
		    //'attachments'=>[$filename=>$pdf]
		];
		
		
		
		if($email!='vidmantas.work@gmail.com')
			$opts['bcc'] = GW_Mail_Helper::getAdminAddr();
		
		$msg = GW::ln('/m/MESSAGE_SENT_TO',['v'=>['email'=>$email]]);
		//$this->setMessage();
		
		GW_Mail_Helper::sendMail($opts);
		
		if(isset($_GET['sys_call'])){
			echo json_encode(['resp'=>$msg]);
			exit;
		}else{
			$this->setMessage($msg);
			$this->jump();
		}
	}
	
	
	
}
