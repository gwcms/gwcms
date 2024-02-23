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
		
		if(isset($_GET['user_id'])){
			$this->filters['user_id'] = $_GET['user_id'];
			$this->userObj = GW_Customer::singleton()->createNewObject($_GET['user_id'], true);
		}
		
		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['user_id'] = 1;	
		
		
		$this->config =  new GW_Config($this->module_path[0].'/');	
		
		$this->addRedirRule('/^doMail|^viewMail/i',['mails','Module_Ordergroups_Mails']);	
		$this->initFeatures();
		

		if($this->feat('itax'))
			$this->addRedirRule('/^doItax|^viewItax/i','itax');		
		
		if($this->feat('rivile'))
			$this->addRedirRule('/^doRivile|^viewRivile/i','rivile');	

		$this->options['vatgroups'] = GW_VATgroups::singleton()->getOptions();
		
		$this->sellers_enabled = GW_Permissions::canAccess('payments/sellers',true, $this->app->user->group_ids, false);
		
	}
	

	
	
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields']['user_title'] = 'Lf';
		$cfg['fields']['changetrack'] = 'L';
		
		if($this->feat('itax')){
			$cfg["fields"]['itax_status_ex'] = 'Lof';
		}
		//d::dumpas($cfg);
		
		$cfg['filters']['user_id'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'customers/users'];
		
		
		if($this->sellers_enabled)
			$cfg['filters']['seller_id'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'payments/sellers'];
					
		return $cfg;
	}
	
	
	function prepareCounts($list)
	{
		/*
		$ids = array_keys($list);
		$counts = GW_Order_Item::singleton()->countGrouped('group_id', GW_DB::inCondition('group_id', $ids));
		
		foreach($counts as $id => $item)
			$list[$id]->items_count = $counts[$id];
		 * 
		 */
		//new version with sql UPDATE gw_order_group AS g SET itmcnt = (SELECT count(*) FROM gw_order_item AS i WHERE i.group_id=g.id);
		
		parent::prepareCounts($list);
	}	
	
	
	function __eventAfterList(&$list)
	{		
		$this->attachFieldOptions($list, 'user_id', 'GW_User');	
		
		if($this->sellers_enabled && ($this->list_config['display_fields']['seller_id'] ?? false) ){
			GW_Composite_Data_Object::prepareLinkedObjects($list,'seller');
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
	
	function initInvoiceVars($item, $opts=[])
	{
		
		$user =  $item->user;
		
		$payconfirm = $item->pay_confirm;
		
		
		//allow invoice vars even if no payconfirm presennt - for pre-invoice function
		/*
		if(!$payconfirm)
		{
			$this->setError("/m/NO_PAY_CONFIRM");
			$this->jump();
		}*/
		
		
		//d::dumpas(count($list));
		
		if($_GET['offset'] ?? false)
			$list = [$list[$_GET['offset']]];
		
		$idname = $this->modconfig->invoice_template;
		
		$tpl = GW_Mail_Template::singleton()->find(['`'.(is_numeric($idname) ? 'id' : 'idname').'`=?', $idname]);
		
		if(!$tpl){
			$this->setError("Nenurodytas sąskaitos šablonas, modulio žiūrėti nustatymuose");
			$this->jump();
		}
		
		
		
		
		//Since 2022 it is provided universal template for all languages
		if($tpl->get("ln_enabled_".$this->app->ln)){
			$tpl_code = $tpl->get("body_".$this->app->ln);
		}else{
			//default language
			$tpl_code = $tpl->get("body_lt");
		}
		
		
		
		
		$v =& $this->tpl_vars;
		
		$item->setSecretIfNotSet();
		
		
		$attachuservars = function(&$v, $user){
			$v['FULLNAME'] = $user->title;
			$v['CITY'] = mb_convert_case($user->city, MB_CASE_TITLE, 'UTF-8');
			$v['COUNTRY'] = GW_Country::singleton()->getCountryByCode($user->country, 'lt');
			$v['PHONE'] = $user->phone;
		};
		

			
		$build = false;
		$v = [];
		
		if($item->pay_test)
			$v['PAY_TEST']=1;
		
		
		$v['PRICE'] = $item->amount_total;
		$v['PRICE_TEXT'] = GW_Sum_To_Text_Helper::sum2text($v['PRICE'], 'lt');

		$v['COMPANY'] = $item->company;
		$v['COMPANY_ID'] = $item->company_code;
		$v['COMPANY_VAT_ID'] = $item->vat_code;
		$v['COMPANY_ADDR'] = $item->company_addr;
		
		$v['INVOICE_NUM'] = GW::ln('/g/PAYMENT_BANKTRANSFER_DETAILS_PREFIX').'-'.$item->id;
		$v['DATE'] = explode(' ',$item->pay_time)[0];
		
		if($v['DATE']=='0000-00-00')
			$v['DATE'] = explode(' ',$item->insert_time)[0];
		
		
		$v['EMAIL'] = isset($payconfirm->p_email) ? $payconfirm->p_email : $item->email;
		$v['ITEMS'] = [];
		$v['ORDERID'] = $item->id;
		$v['SECRET'] = $item->secret;
		$v['SITE_DOMAIN'] = parse_url(GW::s('SITE_URL'), PHP_URL_HOST);
		$v['PAY_LINK'] = GW::s('SITE_URL').$this->app->buildURI('direct/orders/orders', ['act'=>'doOrderPay','id'=>$item->id,'key'=>$item->secret],['app'=>"site"]);
			//$pdf=GW_html2pdf_Helper::convert($html, false);			
		$v['DISCOUNT_ID'] = $item->discount_id;
		
		$v['AMOUNT_SHIPPING'] = $item->amount_shipping;
		$v['AMOUNT_DISCOUNT'] = $item->amount_discount;
		$v['AMOUNT_COUPON'] = $item->amount_coupon;			
		$v['AMOUNT_ITEMS'] = $item->amount_items;
		
		$orderlink = GW::s('SITE_URL').$this->app->buildURI('direct/orders/orders', ['orderid'=>$item->id,'id'=>$item->id,'key'=>$item->secret],['app'=>"site"]);
		$v['ORDER_LINK'] = "<a href='$orderlink'>".GW_String_Helper::truncate($orderlink,50)."</a>";
		
		if($opts['ORDER_DETAILS_HTML'] ?? false){
			$v['ORDER_DETAILS_HTML'] = $this->getOrderItems($item,true);
		}
			
		if($this->feat('vat')){
			GW_VATgroups::singleton()->getOptionsNote();
		}
		
		foreach($item->items as $oitem){
			
			$itm=[
			    'title'=> $oitem->invoice_line2, 
			    'type'=> $oitem->type, 
			    'qty'=>$oitem->qty, 
			    'unit_price'=>$oitem->unit_price, 
			    'total'=>$oitem->total
			];
			
			if($this->feat('vat') && $oitem->vat_group){
				$itm["vat"]=$oitem->vat_title;
				$itm["vat_part"]=$oitem->vat_part;
				
				$v['VAT_GIDS'][$oitem->vat_group]=1;
			}
			
			$v['ITEMS'][] = $itm;
		}
		
		
		if($this->feat('vat') && isset($v['VAT_GIDS'])){
			$notes = GW_VATgroups::singleton()->getOptionsNote();
			
			
			$v['VAT_NOTES'] = array_intersect_key($notes, $v['VAT_GIDS']);
		}
		
		
		if($user->id){
			$attachuservars($v, $user);
			
			if(!$v['EMAIL'])
				$v['EMAIL'] = $user->email;
		}else{
			$v['FULLNAME'] = $item->name.' '.$item->surname;
			$v['PHONE'] = $user->phone;			
		}
		
		if($item->seller_id){
			$v['SELLER'] = $item->seller->title;
			$v['SELLER_ID'] = $item->seller->company_code;
			$v['SELLER_ADDR'] = $item->seller->address;
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
		$item = $this->getDataObjectById(true, false, GW_PERM_READ);
		list($tpl_code, $v) = $this->initInvoiceVars($item);
		
		if(isset($_GET['preinvoice']))
			$v['preinvoice']=1;				
		
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
	
	function viewPreinvoice()
	{
		$_GET['preinvoice']=1;
		$this->viewInvoice();
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
		$list = $this->model->findAll('payment_status=7');
		
		foreach($list as $item)
			$this->doSaveInvoice($item);
		
	}
	
	
	function doMarkAsPayd()
	{		
		$item = $this->getDataObjectById();
		
		
		$query = $_GET['rcv_amount'] ?? false;
		
		
		if($this->app->user->isRoot() && $query==777){
			$this->setMessageEx(['text'=>'No payment already accepted verification for root user (testing purposes)', 'type'=>GW_MSG_INFO]);
		}elseif($item->payment_status==7){
			$this->setError(GW::l('/m/PAYMENT_ALREADY_ACCEPTED'));
			$this->app->jump();
		}
		
	
		
		
		
		if($this->app->user->isRoot() && $query==777){
			$this->setMessageEx(['text'=>'No price verification for root user and code 777', 'type'=>GW_MSG_INFO]);
			$_GET['rcv_amount'] = $item->amount_total;
		}elseif($query != $item->amount_total){
			$this->setError(GW::l('/m/RECEIVED_AMOUNT_DOES_NOT_MATCH'));
			$this->app->jump();
			return false;
		}
		
		

		
		$item->fireEvent('BEFORE_CHANGES');
		
		//ta jau padaro doMarkAsPaydSystem
		//$item->payment_status=7;
		//$item->updateChanged();		
		
		$this->doMarkAsPaydSystem($item);
		
		$this->setMessage('/m/PAYMENT_APPROVED');
	}

	
	
	function doMarkAsPaydSystem($order=false)
	{		
		if(!$order)
			$order = $this->getDataObjectById();
		
		//if( ($_GET['pay_type']??false) == 'couponpay')
		//	d::dumpas([$order, $_GET]);
		
		if($order->payment_status==7 && !isset($_GET['debugrepeat'])){
			$this->setError(GW::l('/m/PAYMENT_ALREADY_ACCEPTED'));
			$this->app->jump();
		}	
		
		
		$order->fireEvent('BEFORE_CHANGES');
		
		$log_entry_id = $_GET['log_entry_id'] ?? false;
		$rcv_amount = $_GET['rcv_amount'] ?? false;
		
		if($log_entry_id || isset($_GET['pay_type'])){
			$order->pay_type = $_GET['pay_type'];
		}
			
			
		if($rcv_amount != $order->amount_total){
			$order->status = "WrongAmount exp: $cart->amount_total rcv: $rcv_amount";
			$order->payment_status = 8;
		}else{
			$order->payment_status = 7;
			$order->status = 4;// status for delivery tracking 4 - is accepted and processing
		}

		foreach($order->items as $item){
			$obj = $item->obj;
			if($obj){
				$obj->orderItemPayd($item->unit_price, $item->qty, $order, $item);
			}
		}

		if(isset($_GET['paytest']))
			$order->pay_test =1;	

		$order->pay_confirm_id = $log_entry_id;
		$order->pay_time = date('Y-m-d H:i:s');

		$order->updateChanged();
		
					
		
		//$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?id='.$order->id.'&act=doSaveInvoice&cron=1');	
		
		if($this->config->confirm_email_tpl){
			$lang = $order->user->use_lang ?: $order->use_lang;
			$url=Navigator::backgroundRequest("admin/$lang/payments/ordergroups?id={$order->id}&act=doOrderPaydNotifyUser&cron=1");	
			
			if($this->app->user->isRoot()){
				$this->setMessage("Bg call for mail notification: $url");
			}
		}else{
			if($this->app->user->isRoot())
				$this->setMessageEx(['text'=>'No notification email', 'type'=>GW_MSG_WARN]);
		}
		
		
		
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
		
		//tik pagrindinis
		if($this->sellers_enabled)
			$conds[0].=" AND seller_id=0";		
				
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
		
		
		
		list($invtpl, $vars) = $this->initInvoiceVars($order,['ORDER_DETAILS_HTML'=>1]);
		
		if(isset($_GET['confirm']))
			unset($_GET['preview']);		
		
		if(!isset($_GET['preview'])){
		
			//2kartus kad nesiusti laisko
			if($order->mail_accept && !isset($_GET['confirm']) ){	

				$this->setError("Already sent");
				$this->jump();

			}else{

				$order->set('mail_accept',1);
				$order->updateChanged();
			}
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
			$opts['bcc'] = $this->config->confirm_email_bcc ?: GW_Mail_Helper::getAdminAddr();
		
		
		
		if($order->seller_id){
			$opts['bcc'] = $order->seller->email;
		}
		
		$msg = GW::ln('/m/MESSAGE_SENT_TO',
			['v'=>[
			    'email'=>$email.', '.$opts['bcc']
				]
			]);
		//$this->setMessage();
			
		if(isset($_GET['preview']))
			$opts['preview'] = 1;
		
		$ret = GW_Mail_Helper::sendMail($opts);
		
		if(isset($_GET['preview'])){
			$str= '<div style="padding:20px;border:1px solid silver;background-color:white">'.
				implode(',',$ret['to']).'<hr>'.$ret['subject'].'<hr>'.$ret['body'].
			'</div>';
			
			$alreadysent= ($order->mail_accept?'yes':'no');
			$this->confirm("Confirm send, already sent? {$alreadysent} <hr>{$str}");
		}
		
		
		
		if(isset($_GET['sys_call'])){
			echo json_encode(['resp'=>$msg]);
			exit;
		}else{
			$this->setMessage($msg);
			$this->jump();
		}
	}
	
	
	function getOrderItems($order, $export)
	{
		//$this->initOrderedItems($order);
		
		$this->tpl_vars['order'] = $order; 
			
		if($export){
			$this->tpl_vars['export'] = 1;
			
			$this->tpl_file_name = $this->tpl_dir.'oitems';
			
			if(isset($_GET['tpl'])){
				$tplname = preg_replace('/[^a-z0-9_]/','', $_GET['tpl']);
				$this->tpl_file_name = $this->tpl_dir.$tplname;
			}			
			
			$html = $this->processTemplate(false, true);
			

								
			if($export==='json'){
				echo json_encode(['html'=>$html]);
				exit;
			}else{
				return $html;
			}			
		}	
	}
	
	function viewOitems()
	{
		$item = $this->getDataObjectById();
		$this->getOrderItems($item, $_GET['export']??false);		
	}	
	
}
