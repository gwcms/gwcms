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

		foreach(['user_username', 'user_name', 'user_surname', 'user_email'] as $field)
			$this->extra_cols[$field] = 1;

		if($this->feat('discountcode'))
			$this->extra_cols['discount_code'] = 1;


		if($this->feat('itax'))
			$this->addRedirRule('/^doItax|^viewItax/i','itax');
		
		if($this->feat('rivile'))
			$this->addRedirRule('/^doRivile|^viewRivile/i','rivile');	
		
		if($this->feat('dumbacc'))
			$this->addRedirRule('/^doDumbAccounting|^viewDumbAccounting/i','dumbaccounting');		
		
		

		$this->options['vatgroups'] = GW_VATgroups::singleton()->getOptions();
		
		$this->sellers_enabled = GW_Permissions::canAccess('payments/sellers',true, $this->app->user->group_ids, false);
		$this->item_remove_log=1;
	}
	

	
	
	
	
	function viewForm()
	{
		$vars = parent::viewForm();
		$item = $vars['item'] ?? false;
		
		if($item && $item->id){
			$params = [
				'select' => "a.*, usr.username, TRIM(CONCAT(COALESCE(usr.name,''), ' ', COALESCE(usr.surname,''))) as usertitle",
				'joins' => [
					['left', 'gw_users AS usr', 'a.user_id = usr.id'],
				],
				'order' => 'a.id DESC',
				'limit' => 10,
			];
			
			$this->tpl_vars['order_change_transactions'] = GW_Change_Transaction::singleton()->findAll(
				['order_id=?', (int)$item->id],
				$params
			);
		}
		
		return $vars;
	}
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields']['user_title'] = 'Lf';
		$cfg['fields']['changetrack'] = 'L';
		$cfg['fields']['item_lines'] = 'l';
		$cfg['fields']['ledger_count'] = 'L';
		$cfg['fields']['invoice_tpl_id'] = 'lof';

		if($this->feat('discountcode'))
			$cfg['fields']['discount_id'] = 'Lof';

		if($this->feat('itax')){
			$cfg["fields"]['itax_status_ex'] = 'Lof';
		}
		//d::dumpas($cfg);
		
		$cfg['filters']['user_id'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'customers/users'];
		$cfg['filters']['invoice_tpl_id'] = [
			'type'=>'select_ajax',
			'options'=>[],
			'preload'=>1,
			'modpath'=>'emails/email_templates',
			'source_args'=>['byid'=>1],
		];
		
		
		if($this->sellers_enabled)
			$cfg['filters']['seller_id'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'payments/sellers'];
					
		return $cfg;
	}

	function __eventBeforeListParams(&$params)
	{
		$params['select'] = $params['select'] ?? 'a.*';
		$params['select'] .= ', usr.username AS user_username, usr.name AS user_name, usr.surname AS user_surname, usr.email AS user_email';
		$params['select'] .= ', invoice_tpl_kv.value AS invoice_tpl_id, invoice_tpl.admin_title AS invoice_tpl_title, invoice_tpl.idname AS invoice_tpl_idname';
		$params['joins'] = $params['joins'] ?? [];
		$params['joins'][] = ['left', 'gw_users AS usr', 'a.user_id = usr.id'];
		$params['joins'][] = ['left', 'gw_generic_extended AS invoice_tpl_kv', "invoice_tpl_kv.own_table='gw_order_group' AND invoice_tpl_kv.owner_id=a.id AND invoice_tpl_kv.`key`='invoice_tpl_id'"];
		$params['joins'][] = ['left', 'gw_mail_templates AS invoice_tpl', 'invoice_tpl.id = CAST(invoice_tpl_kv.value AS UNSIGNED)'];

		if($this->feat('discountcode')){
			$params['select'] .= ', dc.code AS discount_code';
			$params['joins'][] = ['left', 'shop_discountcode AS dc', 'a.discount_id = dc.id'];
		}
	}

	function overrideFilterInvoice_tpl_id($value, $compare_type)
	{
		if($compare_type == 'LIKE'){
			return "(
				invoice_tpl_kv.value LIKE '%$value%'
				OR invoice_tpl.admin_title LIKE '%$value%'
				OR invoice_tpl.idname LIKE '%$value%'
			)";
		}

		return $this->buildCond('invoice_tpl_kv.value', $compare_type, $value, true, false);
	}

	function eventHandler($event, &$context)
	{
		switch($event){
			case 'AFTER_SEARCH_COND_BUILD':
				$search = GW_DB::escape($this->list_params['search'] ?? '');
				
				if($search !== ''){
					$context .= ($context ? ' OR ' : '')."
						a.id IN (
							SELECT ge.owner_id
							FROM gw_generic_extended AS ge
							LEFT JOIN gw_mail_templates AS mt ON mt.id = CAST(ge.value AS UNSIGNED)
							WHERE ge.own_table = 'gw_order_group'
								AND ge.`key` = 'invoice_tpl_id'
								AND (
									ge.value LIKE '%$search%'
									OR mt.admin_title LIKE '%$search%'
									OR mt.idname LIKE '%$search%'
								)
						)
					";
				}
			break;
		}
		
		return parent::eventHandler($event, $context);
	}


	protected function buildPaymentTrackContext($order)
	{
		$user = $this->app->user;
		$username = trim((string)($user->username ?? ''));
		
		if(!$username)
			$username = trim((string)$user->title);
		
		if(!$username)
			$username = 'user#'.(int)$user->id;
		
		$is_system = !empty($_GET['sys_call']);
		$pay_type = trim((string)($_GET['pay_type'] ?? $order->pay_type ?? ''));
		$action_type = $is_system ? 'automatic_payment_approval' : 'manual_payment_approval';
		
		if($is_system){
			$gateway = $pay_type ?: 'system';
			$note = "Automatic payment approval via {$gateway} by {$username}";
		}else{
			$note = "Manual payment approval by {$username}: funds received by bank transfer";
		}

		$entry = [
			'action_type' => $action_type,
			'context_obj_type' => 'gw_order_group',
			'context_obj_id' => (int)$order->id,
			'order_id' => (int)$order->id,
			'user_id' => (int)$user->id,
			'status' => 'started',
			'note' => $note,
			'meta' => [
				'order_id' => (int)$order->id,
				'user_id' => (int)$user->id,
				'pay_type' => $pay_type,
				'sys_call' => (int)$is_system,
			],
		];
		
		$tx = GW_Change_Transaction::singleton()->createNewObject($entry);
		$tx->insert();
		
		
		
		return [
			'id' => (int)$tx->id,
			'note' => $note,
			'transaction_id' => (int)$tx->id,
		];
	}
	
	protected function completeTrackContext($context, $status='completed', $meta=[])
	{
		$txid = (int)($context['id'] ?? $context['transaction_id'] ?? 0);
		
		if(!$txid)
			return false;
		
		$tx = GW_Change_Transaction::singleton()->find(['id=?', $txid]);
		
		if(!$tx)
			return false;
		
		$tx->status = $status;
		
		if($meta){
			$current_meta = (array)$tx->meta;
			$tx->meta = array_merge($current_meta, $meta);
		}
		
		$tx->updateChanged();
		return $tx;
	}

	protected function getLatestOrderTransaction($order)
	{
		if(!$order || !$order->id)
			return false;

		return GW_Change_Transaction::singleton()->find(
			['order_id=?', (int)$order->id],
			['order' => 'id DESC']
		);
	}

	protected function paymentSourceLogTable($pay_type)
	{
		switch($pay_type){
			case 'paysera':
				return 'gw_paysera_log';
			case 'paypal':
				return 'gw_paypal_log';
			case 'montonio':
			case 'revolut':
			case 'kevin':
				return 'gw_payuniversal_log';
		}
		
		return '';
	}
	
	protected function createOrderPaymentConfirmation($order, $direction, $amount, $opts=[])
	{
		$amount = round((float)$amount, 2);
		
		if(!$order || !$order->id || $amount <= 0)
			return false;
		
		if(!GW_Order_Payment_Confirmation::tableExists())
			return false;
		
		$pay_type = trim((string)($opts['source'] ?? $_GET['pay_type'] ?? $order->pay_type ?? 'manual'));
		$source_log_id = (int)($opts['source_log_id'] ?? $_GET['log_entry_id'] ?? $order->pay_confirm_id ?? 0);
		$source_log_table = $opts['source_log_table'] ?? $this->paymentSourceLogTable($pay_type);
		$received_at = $opts['received_at'] ?? $_GET['received_at'] ?? date('Y-m-d H:i:s');
		$reference = trim((string)($opts['reference'] ?? $_GET['reference'] ?? ''));
		
		$unique_key = $opts['unique_key'] ?? implode(':', [
			$pay_type ?: 'manual',
			$direction,
			(int)$order->id,
			$source_log_id,
			md5($amount.'|'.$received_at.'|'.$reference),
		]);
		
		if(GW_Order_Payment_Confirmation::singleton()->find(['unique_key=?', $unique_key]))
			return false;
		
		$confirmation = GW_Order_Payment_Confirmation::singleton()->createNewObject();
		$confirmation->setValues([
			'order_id' => (int)$order->id,
			'direction' => $direction,
			'status' => $opts['status'] ?? 'confirmed',
			'source' => $pay_type ?: 'manual',
			'source_log_table' => $source_log_table,
			'source_log_id' => $source_log_id,
			'unique_key' => $unique_key,
			'amount' => $amount,
			'currency' => $opts['currency'] ?? ($this->config->default_currency_code ?: 'EUR'),
			'received_at' => $received_at,
			'bank_account' => $opts['bank_account'] ?? $_GET['bank_account'] ?? '',
			'reference' => $reference,
			'comment' => $opts['comment'] ?? $_GET['payment_comment'] ?? '',
			'created_by' => (int)$this->app->user->id,
			'change_transaction_id' => (int)($opts['change_transaction_id'] ?? 0),
			'test' => (int)($opts['test'] ?? $order->pay_test ?? isset($_GET['paytest'])),
		]);
		$confirmation->insert();
		
		return $confirmation;
	}
	
	protected function getOwnBankAccountOptions()
	{
		$type = GW_Classificator_Types::singleton()->find(['`key`=?', 'own_bank_accounts']);
		
		if(!$type)
			return [];
		
		return GW_Classificators::singleton()->getAssoc(
			['key', 'title_'.$this->app->ln],
			['`type`=? AND active=1', $type->id]
		);
	}
	
	function doMigrateOrderGroupWithLedger()
	{
		if(!$this->app->user->isRoot())
			return $this->setError('Root only');
		
		if(!GW_Order_Payment_Confirmation::tableExists()){
			$this->setError('Ledger table not found. Run sql/2026-04-30-2 order payment ledger.sql first.');
			$this->jumpAfterSave();
		}
		
		$cfg = new GW_Config($this->module_path[0].'/');
		$existing = $cfg->get('migrateOrderGroupWithLedger');
		
		if($existing && !isset($_GET['force'])){
			$this->setMessage('Order group ledger migration already done: '.$existing);
			$this->jumpAfterSave();
		}
		
		$db = GW::db();
		$before = $db->fetch_row("SELECT COUNT(*) AS cnt FROM gw_order_payment_confirmation WHERE source='legacy'");
		$before_cnt = (int)($before['cnt'] ?? 0);
		
		$db->query("
			INSERT IGNORE INTO gw_order_payment_confirmation
				(order_id, direction, status, source, source_log_table, source_log_id, unique_key, amount, currency, received_at, reference, comment, created_by, test, insert_time, update_time)
			SELECT
				id,
				'payment',
				'confirmed',
				'legacy',
				CASE
					WHEN pay_type='paysera' THEN 'gw_paysera_log'
					WHEN pay_type='paypal' THEN 'gw_paypal_log'
					WHEN pay_type IN ('montonio','revolut','kevin') THEN 'gw_payuniversal_log'
					ELSE ''
				END,
				pay_confirm_id,
				CONCAT('legacy:payment:', id, ':', pay_confirm_id),
				ROUND(amount_total, 2),
				'EUR',
				COALESCE(pay_time, update_time, insert_time, NOW()),
				CONCAT('legacy marked as payd order #', id),
				'Legacy ledger entry: order was marked as payd before payment ledger existed',
				0,
				pay_test,
				NOW(),
				NOW()
			FROM gw_order_group
			WHERE payment_status IN (7,9) AND amount_total > 0
		");
		
		$db->query("
			INSERT IGNORE INTO gw_order_payment_confirmation
				(order_id, direction, status, source, source_log_table, source_log_id, unique_key, amount, currency, received_at, reference, comment, created_by, test, insert_time, update_time)
			SELECT
				id,
				'refund',
				'confirmed',
				'legacy',
				'',
				0,
				CONCAT('legacy:refund:', id),
				ROUND(amount_total, 2),
				'EUR',
				COALESCE(update_time, pay_time, insert_time, NOW()),
				CONCAT('legacy refunded order #', id),
				'Backfilled from gw_order_group.payment_status=9',
				0,
				pay_test,
				NOW(),
				NOW()
			FROM gw_order_group
			WHERE payment_status=9 AND amount_total > 0
		");
		
		$after = $db->fetch_row("SELECT COUNT(*) AS cnt FROM gw_order_payment_confirmation WHERE source='legacy'");
		$created = (int)($after['cnt'] ?? 0) - $before_cnt;
		
		$status_changes = 0;
		$order_ids = array_keys($db->fetch_assoc("SELECT DISTINCT order_id FROM gw_order_payment_confirmation WHERE source='legacy'"));
		$orders = $order_ids
			? GW_Order_Group::singleton()->findAll(GW_DB::inCondition('id', $order_ids))
			: [];
		
		foreach($orders as $order){
			$old_status = (int)$order->payment_status;
			$order->recalcPaymentLedger(false);
			
			if((int)$order->payment_status !== $old_status)
				$status_changes++;
			
			$order->updateChanged();
		}
		
		$message = 'done '.date('Y-m-d').' created '.$created.' ledger records, order payment status changes: '.$status_changes;
		$cfg->set('migrateOrderGroupWithLedger', $message);
		
		if(isset($_GET['sys_call'])){
			echo json_encode([
				'ok' => 1,
				'created' => $created,
				'status_changes' => $status_changes,
				'message' => $message,
			]);
			exit;
		}
		
		$this->setMessage('migrateOrderGroupWithLedger '.$message);
		$this->jumpAfterSave();
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
		
		if(!$list || !GW_Order_Payment_Confirmation::tableExists())
			return false;
		
		$ids = array_keys($list);
		$this->tpl_vars['ledger_counts'] = GW_Order_Payment_Confirmation::singleton()->countGrouped(
			'order_id',
			GW_DB::inCondition('order_id', $ids)
		);
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

	function getOptionsCfg()
	{
		$addusername = !empty($_GET['addusername']);
		
		$opts = [
			'joins'=>[],
			'order'=>'a.id DESC',
			'title_func'=>function($item) use ($addusername) {
			
			//d::dumpas($item);
				$title = $item->title;
				
				if($addusername && $item->user_id){
					$username = $item->username ?? '';
					
					
					if($username){
						$title .= ' - '.$username;
					}
				}
				
				return $title;
			},
			'search_fields'=>['a.id']
		];
		
		if($addusername){
			$opts['select'] = 'a.*, user.username as username';
			$opts['joins'][] = ['left', 'gw_users AS user', 'a.user_id = user.id'];
		}else{
			$opts['joins'][] = ['left', 'gw_users AS user', 'a.user_id = user.id'];
		}

		$opts['search_fields'][] = 'user.username';
		$opts['search_fields'][] = 'user.name';
		$opts['search_fields'][] = 'user.surname';
		$opts['search_fields'][] = 'user.email';

		if($this->feat('discountcode')){
			$opts['select'] = isset($opts['select'])
				? $opts['select'].', dc.code AS discount_code'
				: 'a.*, dc.code AS discount_code';
			$opts['joins'][] = ['left', 'shop_discountcode AS dc', 'a.discount_id = dc.id'];
			$opts['search_fields'][] = 'dc.code';
		}

		return $opts;
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
		if(method_exists($item, 'recalcPaymentLedger'))
			$item->recalcPaymentLedger(false);
		
		$fmtAmount = function($amount) {
			return number_format((float)$amount, 2, '.', '');
		};
		
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
		
		$idname = $item->get('keyval/invoice_tpl_id') ?: $this->modconfig->invoice_template;
		
		$tpl = GW_Mail_Template::singleton()->find(['`'.(is_numeric($idname) ? 'id' : 'idname').'`=?', $idname]);
		
		if(!$tpl){
			$this->setError("Nenurodytas sąskaitos šablonas, modulio žiūrėti nustatymuose");
			$this->jump();
		}
		
		$ln = $opts['ln'] ?? false;
		
		if(!$ln)
			$ln = $this->app->ln;
		
		
		
				
		//Since 2022 it is provided universal template for all languages
		if($tpl->get("ln_enabled_$ln")){
			$tpl_code = $tpl->get("body_$ln");
		}elseif($tpl->get("ln_enabled_lt")){
			$tpl_code = $tpl->get("body_lt");
		}else{
			//default language
			$tpl_code = $tpl->get("body_en");
		}
		
		
		
		
		$v =& $this->tpl_vars;
		
		$item->setSecretIfNotSet();
		
		
		$attachuservars = function(&$v, $user){
			$v['FULLNAME'] = $user->title;
			$v['CITY'] = mb_convert_case($user->city, MB_CASE_TITLE, 'UTF-8');
			$v['COUNTRY'] = GW_Country::singleton()->getCountryByCode($user->country, 'lt');
			$v['PHONE'] = $user->phone;
		};
		
		$translateKeyval = function($key, $fallback) {
			if(!$key){
				return GW::ln($fallback);
			}
			
			if($key[0] !== '/'){
				$key = '/'.$key;
			}
			
			return GW::ln($key);
		};
		

			
		$build = false;
		$v = [];
		
		if($item->pay_test)
			$v['PAY_TEST']=1;
		
		
		$order_amount_total = round((float)$item->amount_total, 2);
		$payd_amount = round((float)$item->payd_amount, 2);
		$balance_amount = round((float)$item->balance_amount, 2);
		
		if($payd_amount < 0)
			$payd_amount = 0;
		
		if($balance_amount < 0)
			$balance_amount = 0;
		
		$is_partial_payment = $payd_amount > 0 && $balance_amount > 0;
		$invoice_amount = $is_partial_payment ? $payd_amount : $order_amount_total;
		$payable_amount = $balance_amount > 0 ? $balance_amount : $order_amount_total;
		
		$v['PRICE'] = $item->amount_total;
		$v['PRICE_TEXT'] = GW_Sum_To_Text_Helper::sum2text($v['PRICE'], $ln);
		$v['ORDER_AMOUNT_TOTAL'] = $order_amount_total;
		$v['ORDER_AMOUNT_TOTAL_FMT'] = $fmtAmount($order_amount_total);
		$v['PAYD_AMOUNT'] = $payd_amount;
		$v['PAYD_AMOUNT_FMT'] = $fmtAmount($payd_amount);
		$v['BALANCE_AMOUNT'] = $balance_amount;
		$v['BALANCE_AMOUNT_FMT'] = $fmtAmount($balance_amount);
		$v['INVOICE_AMOUNT'] = $invoice_amount;
		$v['INVOICE_AMOUNT_FMT'] = $fmtAmount($invoice_amount);
		$v['INVOICE_AMOUNT_TEXT'] = GW_Sum_To_Text_Helper::sum2text($invoice_amount, $ln);
		$v['PAYABLE_AMOUNT'] = $payable_amount;
		$v['PAYABLE_AMOUNT_FMT'] = $fmtAmount($payable_amount);
		$v['PAYABLE_AMOUNT_TEXT'] = GW_Sum_To_Text_Helper::sum2text($payable_amount, $ln);
		$v['PARTIAL_PAYMENT'] = $is_partial_payment ? 1 : 0;
		$v['PAYD'] = $item->payd;

		$v['COMPANY'] = $item->company;
		$v['COMPANY_ID'] = $item->company_code;
		$v['COMPANY_VAT_ID'] = $item->vat_code;
		$v['COMPANY_ADDR'] = $item->company_addr;
		$v['CLIENT_NAME'] = trim($item->company ?: trim($item->name.' '.$item->surname));
		
		$v['INVOICE_NUM'] = trim(GW::ln('/g/PAYMENT_BANKTRANSFER_DETAILS_PREFIX')).'-'.$item->id;
		$v['EPC_REFERENCE'] = $v['INVOICE_NUM'];
		$v['DATE'] = $item->invoice_date ?: explode(' ',$item->pay_time)[0];
		
		if($v['DATE']=='0000-00-00' || !$v['DATE'])
			$v['DATE'] = explode(' ',$item->insert_time)[0];
		
		$v['DUE_DATE'] = $item->due_date;
		
		
		$v['ADM_MESSAGE'] = $item->adm_message;
		$v['EMAIL'] = isset($payconfirm->p_email) ? $payconfirm->p_email : $item->email;
		$v['ITEMS'] = [];
		$v['ORDERID'] = $item->id;
		$v['ORDER_STATUS'] = $item->status;
		$v['SECRET'] = $item->secret;
		$v['SITE_DOMAIN'] = parse_url(GW::s('SITE_URL'), PHP_URL_HOST);
		$v['PAY_LINK'] = GW::s('SITE_URL').$this->app->buildURI('direct/orders/orders', ['act'=>'doOrderPay','id'=>$item->id,'key'=>$item->secret],['app'=>"site"]);
			//$pdf=GW_html2pdf_Helper::convert($html, false);			
		$v['DISCOUNT_ID'] = $item->discount_id;
		
		$v['AMOUNT_SHIPPING'] = $item->amount_shipping;
		$v['AMOUNT_DISCOUNT'] = $item->amount_discount;
		$v['AMOUNT_COUPON'] = $item->amount_coupon;			
		$v['AMOUNT_ITEMS'] = $item->amount_items;
		$v['SELLER_REKVIZITAI_VATINVOICE'] = GW::ln("/M/orders/TKPC_MENUTURAS_PVM_REKVIZITAI");
		$v['SELLER_REKVIZITAI_PREINVOICE'] = $v['SELLER_REKVIZITAI_VATINVOICE'];
		$v['EPC_QR_URL'] = '';
		$v['EPC_QR_URL_HTML'] = '';
		
		if(GW::s('PROJECT_NAME') == 'artistdb'){
			$v['SELLER_REKVIZITAI_VATINVOICE'] = $translateKeyval(
				$item->get('keyval/artistdbseller_vatinvoice'),
				"/M/orders/TKPC_MENUTURAS_PVM_REKVIZITAI"
			);
			$v['SELLER_REKVIZITAI_PREINVOICE'] = $item->get('keyval/artistdbseller_preinvoice')
				? $translateKeyval($item->get('keyval/artistdbseller_preinvoice'), "/M/orders/TKPC_MENUTURAS_PVM_REKVIZITAI")
				: $v['SELLER_REKVIZITAI_VATINVOICE'];
		}

		$recipient_iban = trim((string)GW::ln('/g/CONTACTS_IBAN'));
		$recipient_name = trim((string)GW::ln('/g/CONTACTS_COMPANY_NAME'));

		if($recipient_iban && $recipient_iban !== '&nbsp;' && $recipient_name){
			$v['EPC_QR_URL'] = rtrim(GW::s('SITE_URL'), '/').'/tools/epc_generator?'.http_build_query([
				'recipient_iban' => $recipient_iban,
				'recipient_name' => $recipient_name,
				'amount' => $v['PAYABLE_AMOUNT_FMT'],
				'reference' => $v['EPC_REFERENCE'],
			]);
			$v['EPC_QR_URL_HTML'] = htmlspecialchars($v['EPC_QR_URL'], ENT_QUOTES, 'UTF-8');
		}
		
		$orderlink = GW::s('SITE_URL').$this->app->buildURI('direct/orders/orders', ['orderid'=>$item->id,'id'=>$item->id,'key'=>$item->secret],['app'=>"site"]);
		$v['ORDER_LINK'] = "<a href='$orderlink'>".GW_String_Helper::truncate($orderlink,50)."</a>";
		
		$v['CONTRACT_SIGN_URL'] = $item->contract_sign_url;
		$v['CONTRACT_SIGN_BUTTON'] = '';
		$v['CONTRACT_SIGN_BUTTONS'] = '';
		
		foreach($item->contract_links as $contract_link){
			$v['CONTRACT_SIGN_BUTTONS'] .=
				'<div style="text-align:center;margin:12px 0;">'.
				'<a href="'.htmlspecialchars($contract_link['url'], ENT_QUOTES, 'UTF-8').'" '.
				'style="display:inline-block;background:#f6a800;color:#111;text-decoration:none;padding:14px 26px;font-weight:bold;border-radius:3px;">'.
				htmlspecialchars($contract_link['caption'], ENT_QUOTES, 'UTF-8').
				'</a>'.
				'</div>';
		}
		
		$v['CONTRACT_SIGN_BUTTON'] = $v['CONTRACT_SIGN_BUTTONS'];
		
		if($opts['ORDER_DETAILS_HTML'] ?? false){
			$v['ORDER_DETAILS_HTML'] = $this->getOrderItems($item,true).$v['CONTRACT_SIGN_BUTTONS'];
		}
		
		
		
		
		if($this->feat('vat')){
			GW_VATgroups::singleton()->getOptionsNote();
			$v["VAT"]=1;
		}
		
		
		foreach($item->items as $oitem){
			
			$itm=[
			    'title'=> $oitem->invoice_line2, 
			    'type'=> $oitem->type, 
			    'qty'=>$oitem->qty, 
			    'unit_price'=>$oitem->unit_price, 
			    'total'=>$oitem->total,
			];
			
			if($oitem->is_expired)
				$itm['expired'] = $oitem->expires;
			
			
			//d::dumpas($oitem);
			
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
		
		$opts= [];
		if($_GET['ln']??false)
			$opts['ln']=$_GET['ln'];
		
		
		list($tpl_code, $v) = $this->initInvoiceVars($item, $opts);
			
		
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
		
		$pdf_opts = [];
		if(isset($_GET['test_dpi']) && (int)$_GET['test_dpi'] > 0)
			$pdf_opts['params']['dpi'] = (int)$_GET['test_dpi'];
		
		$pdf=GW_html2pdf_Helper::convert($html, false, $pdf_opts);
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

	function doCreateInvoice()
	{
		$form = [
			'fields' => [
				
				    'client_id' => [
					'type' => 'select_ajax',
					'title' => 'Klientas',
					'modpath' => 'customers/users',
					'preload' => 1,
					'options' => [],
					'required' => 1,
					'colspan'=>6
				],
				'invoice_date' => [
					'type' => 'date',
					'title' => 'Sąskaitos data',
					'default' => date('Y-m-d'),
					'required' => 1,
				],
				'payment_days' => [
					'type' => 'number',
					'title' => 'Mok. term. d.',
					'default' => 7,
					'min' => 0,
				],
			    
				'obj_type' => [
					'type' => 'select',
					'title' => 'Tipas',
					'options' => [
						'gw_oi_service' => 'Paslauga',
					],
					'default' => 'gw_oi_service',
					'required' => 1,
					'size'=>1,
				],
				'unit_price' => [
					'type' => 'number',
					'title' => 'Kaina',
					'step' => '0.01',
					'required' => 1,
				    'size'=>1,
				],
				'qty' => [
					'type' => 'number',
					'title' => 'Kiekis',
					'default' => 1,
					'step' => '0.01',
					'required' => 1,
				    'size'=>1,
				],
				'invoice_line2' => [
					'type' => 'text',
					'title' => 'Eilutė',
					'required' => 1,
				    'size'=>1,
				],
				'obj_type2' => [
					'type' => 'select',
					'title' => 'Tipas 2',
					'options' => [
						'gw_oi_service' => 'Paslauga',
					],
					'default' => 'gw_oi_service',
				],
				'unit_price2' => [
					'type' => 'number',
					'title' => 'Kaina 2',
					'step' => '0.01',
				],
				'qty2' => [
					'type' => 'number',
					'title' => 'Kiekis 2',
					'step' => '0.01',
				],
				'invoice_line22' => [
					'type' => 'text',
					'title' => 'Eilutė 2',
				],
			    

			],
			'cols' => 4,
		];

		foreach($form['fields'] as &$field)
			$field += ['notr' => 1, 'width_title' => '1%', 'width' => '100%'];
		unset($field);

		if(!($answers = $this->prompt($form, 'Pasirinkite klientą', ['method' => 'post', 'width' => '100%'])))
			return false;

		$client = GW_Customer::singleton()->find(['id=?', (int)$answers['client_id']]);

		if(!$client)
			return $this->setError('Klientas nerastas');

		$company = trim((string)$client->company_name);
		if(!$company)
			$company = trim($client->name.' '.$client->surname);

		$line2_vals = [
			'unit_price2' => trim((string)($answers['unit_price2'] ?? '')),
			'qty2' => trim((string)($answers['qty2'] ?? '')),
			'invoice_line22' => trim((string)($answers['invoice_line22'] ?? '')),
		];
		$line2_started = $line2_vals['unit_price2'] !== '' || $line2_vals['invoice_line22'] !== '';
		$line2_filled = count(array_filter($line2_vals, 'strlen')) == count($line2_vals);

		if($line2_started && !$line2_filled)
			return $this->setError('Antrai paslaugai reikia užpildyti kainą, kiekį ir sąskaitos eilutę');

		$order = GW_Order_Group::singleton()->createNewObject();
		$invoice_date = $answers['invoice_date'] ?: date('Y-m-d');
		$payment_days = trim((string)($answers['payment_days'] ?? ''));
		$due_date = $payment_days === '' ? null : date('Y-m-d', strtotime($invoice_date.' +'.(int)$payment_days.' days'));
		
		$order->setValues([
			'user_id' => (int)$client->id,
			'company' => $company,
			'need_invoice' => 1,
			'company_code' => $client->company_code,
			'company_addr' => $client->address,
			'name' => $client->name,
			'surname' => $client->surname,
			'email' => $client->email,
			'country' => $client->country,
			'city' => $client->city,
			'address_l1' => $client->address,
			'phone' => $client->phone,
			'delivery_opt' => 3,
			'use_lang' => $client->use_lang ?: $this->app->ln,
			'open' => 0,
			'active' => 1,
			'payment_status' => 0,
			'placed_time' => date('Y-m-d H:i:s'),
			'invoice_date' => $invoice_date,
			'due_date' => $due_date,
			'extra' => [
				'customer_id' => (int)$client->id,
				'business_vat_group' => $client->business_vat_group,
				'bank_account' => $client->bank_account,
			],
		]);
		$order->insert();

		$add_service = function($obj_type, $unit_price, $qty, $invoice_line2) use ($order) {
			$order_item = new GW_Order_Item();
			$order_item->setValues([
				'obj_type' => $obj_type,
				'obj_id' => 0,
				'qty' => (float)$qty,
				'qty_range' => $qty.';'.$qty,
				'unit_price' => (float)$unit_price,
				'invoice_line2' => $invoice_line2,
				'deliverable' => 0,
				'link' => '',
				'insert_change_track_context' => [
					'note' => 'Invoice service line created',
				],
			]);
			$order->addItem($order_item);
		};

		$add_service($answers['obj_type'], $answers['unit_price'], $answers['qty'], $answers['invoice_line2']);

		if($line2_filled)
			$add_service($answers['obj_type2'], $answers['unit_price2'], $answers['qty2'], $answers['invoice_line22']);

		$this->setMessage("Sukurtas užsakymas #{$order->id}: {$company}");
		$this->jump("payments/ordergroups/{$order->id}/form");
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
	
	function doRecalcOrderPayments()
	{
		$item = $this->getDataObjectById();
		$item->updateTotal();
		
		$this->setMessage('Užsakymo suma ir mokėjimų būklė perskaičiuota');
		$this->jumpAfterSave();
	}
	
	function registerManualPaymentRefund($order, $amount)
	{
		$amount = abs((float)$amount);
		
		if($amount <= 0){
			$this->setError('Grąžinimo suma turi būti didesnė už 0');
			$this->jumpAfterSave();
		}
		
		$track_context = $this->buildPaymentTrackContext($order);
		$this->createOrderPaymentConfirmation($order, 'refund', $amount, [
			'source' => 'manual',
			'bank_account' => $_GET['bank_account'] ?? '',
			'received_at' => $_GET['received_at'] ?? date('Y-m-d H:i:s'),
			'reference' => $_GET['reference'] ?? '',
			'comment' => $_GET['payment_comment'] ?? '',
			'change_transaction_id' => (int)($track_context['transaction_id'] ?? 0),
			'unique_key' => 'manual-refund:'.$order->id.':'.md5($amount.'|'.($_GET['received_at'] ?? '').'|'.($_GET['reference'] ?? '').'|'.microtime(true)),
		]);
		
		$order->recalcPaymentLedger(false);
		$order->updateChanged();
		$this->completeTrackContext($track_context, 'completed', [
			'payment_status' => (int)$order->payment_status,
			'refund_amount' => $amount,
		]);
		
		$this->setMessage('Grąžinimas užregistruotas mokėjimų žurnale');
		$this->jumpAfterSave();
	}
	
	
		function doMarkAsPayd()
		{		
			$item = $this->getDataObjectById();
			
			
			$query = $_GET['rcv_amount'] ?? false;
			
			if($query === false){
				$bank_accounts = $this->getOwnBankAccountOptions();
				
				if(!GW_Order_Payment_Confirmation::tableExists()){
					$this->setError('Ledger table not found. Run sql/2026-04-30-2 order payment ledger.sql first.');
					$this->jumpAfterSave();
				}
				
				if(!$bank_accounts){
					$this->setError('Pirma pridėkite bent vieną opciją: Datasources / Classificator types / Mano bankinės sąskaitos.');
					$this->jumpAfterSave();
				}
				
				$prompt_title = 'Užregistruokite mokėjimą. Šiuo metu balanso suma yra '
					.number_format((float)$item->balance_amount, 2, '.', '').' EUR';
				
				$form = [
					'fields'=>[
						'rcv_amount'=>[
							'type'=>'text',
							'required'=>1,
							'default'=>'',
							'note'=>'Teigiama suma registruoja įplauką, neigiama suma registruoja grąžinimą.',
						],
						'received_at'=>[
							'type'=>'date',
							'required'=>1,
							'default'=>date('Y-m-d'),
						],
						'bank_account'=>[
							'type'=>'select_ajax',
							'options'=>$bank_accounts,
							'modpath'=>'datasources/classificators',
							'source_args'=>['group'=>'own_bank_accounts', 'byKey'=>1],
							'after_input_f'=>'editadd',
							'preload'=>1,
							'empty_option'=>1,
							'required'=>1,
							'note'=>'Opcijos tvarkomos per Datasources / Classificator types / Mano bankinės sąskaitos.',
						],
						'reference'=>[
							'type'=>'text',
							'required'=>0,
						],
						'payment_comment'=>[
							'type'=>'textarea',
							'required'=>0,
						],
					],
					'cols'=>1
				];
			
					
			
				if(!($answers=$this->prompt($form, $prompt_title, ['method'=>'post'])))
					return false;	
	
				$_GET['rcv_amount'] = $query = $answers["rcv_amount"];
				$_GET['received_at'] = $answers['received_at'];
				$_GET['bank_account'] = $answers['bank_account'];
				$_GET['reference'] = $answers['reference'] ?? '';
				$_GET['payment_comment'] = $answers['payment_comment'] ?? '';
				$_GET['manual_ledger'] = 1;
			}
			
			if((float)$query < 0)
				return $this->registerManualPaymentRefund($item, $query);
		
		
		
		
		if($this->app->user->isRoot() && $query==777){
			$this->setMessageEx(['text'=>'No payment already accepted verification for root user (testing purposes)', 'type'=>GW_MSG_INFO]);
		}elseif($item->payment_status==7){
			$this->setError(GW::l('/m/PAYMENT_ALREADY_ACCEPTED'));
			$this->app->jump();
		}
		
	
		
		
		
		if($this->app->user->isRoot() && $query==777){
			$this->setMessageEx(['text'=>'No price verification for root user and code 777', 'type'=>GW_MSG_INFO]);
			$_GET['rcv_amount'] = $item->amount_total;
			}elseif($query != $item->amount_total && !isset($_GET['manual_ledger'])){
				$this->setError(GW::l('/m/RECEIVED_AMOUNT_DOES_NOT_MATCH'));
				$this->app->jump();
				return false;
			}
		
		//ta jau padaro doMarkAsPaydSystem
		//$item->payment_status=7;
		//$item->updateChanged();		
		
			$this->doMarkAsPaydSystem($item);
			
			$item = GW_Order_Group::singleton()->find(['id=?', $item->id]);
			$item->recalcPaymentLedger(false);
			
			if((float)$item->balance_amount > 0){
				$link = GW::s('SITE_URL').$this->app->ln.'/direct/orders/orders?'.http_build_query([
					'orderid' => $item->id,
					'id' => $item->id,
					'key' => $item->secret,
				]);
				
				$this->setMessageEx([
					'type' => GW_MSG_WARN,
					'text' => '<b>JŪS SUKŪRĖTE DALINĮ APMOKĖJIMĄ, KLIENTUI IKI PILNO APMOKĖJIMO TRŪKS '
						.number_format((float)$item->balance_amount, 2, '.', '')
						.' EUR.</b><br>Nusiųskite klientui nuorodą, kurioje jis galės apmokėti likutį: '
						.'<a target="_blank" href="'.$link.'">'.$link.'</a>',
				]);
			}
			
			//d::dumpas($item);
			
		$this->setMessage('/m/PAYMENT_APPROVED');
		$this->jumpAfterSave();
		
	}
	
	function doMarkAsRefund()
	{
		$item = $this->getDataObjectById();
		
		$this->doMarkAsRefundSystem($item);
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
		
		$track_context = $this->buildPaymentTrackContext($order);
		$order->fireEvent('BEFORE_CHANGES', $track_context);
		
		$log_entry_id = $_GET['log_entry_id'] ?? false;
		$rcv_amount = $_GET['rcv_amount'] ?? false;

		if($order->discount_id && $order->discountcode && (int)$order->discountcode->last_use_order_id === (int)$order->id){
			$order->setCoupon();
		}

		if($log_entry_id || isset($_GET['pay_type'])){
			$order->pay_type = $_GET['pay_type'];
		}
			
			
		if($rcv_amount != $order->amount_total && !isset($_GET['paytest']) && !isset($_GET['manual_ledger']) ){
			$order->status = "WrongAmount exp: $order->amount_total rcv: $rcv_amount";
			$order->payment_status = 8;
		}else{
			$order->payment_status = 7;
			$order->status = 4;// status for delivery tracking 4 - is accepted and processing
		}

		if((int)$order->payment_status === 7 && (float)$order->amount_coupon > 0 && $order->discount_id && $order->discountcode){
			$coupon = $order->discountcode;
			if((int)$coupon->last_use_order_id !== (int)$order->id){
				$coupon->fireEvent('BEFORE_CHANGES');
				if($coupon->singleuse)
					$coupon->used_amount = (float)$coupon->used_amount + (float)$order->amount_coupon;
				$coupon->use_count = (int)$coupon->use_count + 1;
				$coupon->last_use_order_id = $order->id;
				$coupon->updateChanged();
			}
		}

		if(isset($_GET['paytest']))
			$order->pay_test =1;	

		$order->pay_confirm_id = $log_entry_id;
		$order->pay_time = date('Y-m-d H:i:s');
		
		$this->createOrderPaymentConfirmation($order, 'payment', $rcv_amount ?: $order->amount_total, [
			'change_transaction_id' => (int)($track_context['transaction_id'] ?? 0),
			'received_at' => $order->pay_time,
		]);
		$order->recalcPaymentLedger(false);
		
		if((int)$order->payment_status === 7){
			foreach($order->items as $item){
				$obj = $item->obj;
				if($obj){
					$obj->orderItemPayd($item->unit_price, $item->qty, $order, $item, $track_context);
				}
			}
		}

		$order->updateChanged();
		$this->completeTrackContext($track_context, 'completed', [
			'payment_status' => (int)$order->payment_status,
			'pay_time' => $order->pay_time,
		]);
		
					
		
		//$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?id='.$order->id.'&act=doSaveInvoice&cron=1');	
		
		if($this->config->confirm_email_tpl && (int)$order->payment_status === 7){
			$lang = ($order->user ? $order->user->use_lang : false) ?: $order->use_lang ?: $this->app->ln ?: 'lt';
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
	
	function addCommentToObject($obj_type, $obj_id, $comment)
	{
		$c = GW_Comments::singleton()->createNewObject();
		$c->obj_type = $obj_type;
		$c->obj_id = $obj_id;
		$c->user_id = $this->app->user->id;
		$c->comment = $comment;
		$c->insert();
	}
	
	function doMarkAsRefundSystem($order=false)
	{
		if(!$order)
			$order = $this->getDataObjectById();
		
		if(!$order){
			$this->setError('Order not found');
			$this->app->jump();
		}
		
		if($order->payment_status==9 && !isset($_GET['debugrepeat'])){
			$this->setError('Order already refunded');
			$this->app->jump();
		}
		
		$order->fireEvent('BEFORE_CHANGES');
		
		$refund_item_ids = [];
		if($_GET['refund_item_ids'] ?? false){
			foreach(explode(',', $_GET['refund_item_ids']) as $id){
				$id = (int)trim($id);
				if($id){
					$refund_item_ids[$id] = $id;
				}
			}
		}
		
		$refund_amount = (float)($_GET['refund_amount'] ?? $order->amount_total);
		$refund_reference = $_GET['refund_reference'] ?? '';
		$refund_uuid = $_GET['refund_uuid'] ?? '';
		$refund_comment = $_GET['refund_comment'] ?? $order->get('extra/refund/pending_comment');
		
		$order->payment_status = 9;
		$order->status = 9;
		$order->active = 0;
		$order->open = 0;
		$order->set('extra/refund/executed_at', date('Y-m-d H:i:s'));
		$order->set('extra/refund/amount', $refund_amount);
		$order->set('extra/refund/refund_reference', $refund_reference);
		$order->set('extra/refund/uuid', $refund_uuid);
		$order->set('extra/refund/pending_comment', '');
		
		$track_context = $this->buildPaymentTrackContext($order);
		$this->createOrderPaymentConfirmation($order, 'refund', $refund_amount, [
			'source' => $order->pay_type ?: 'manual',
			'source_log_id' => (int)$order->pay_confirm_id,
			'reference' => $refund_reference,
			'comment' => $refund_comment,
			'change_transaction_id' => (int)($track_context['transaction_id'] ?? 0),
			'unique_key' => 'refund:'.$order->id.':'.($refund_uuid ?: $refund_reference ?: md5($refund_amount.'|'.date('Y-m-d H:i:s'))),
		]);
		
		$refunded_cnt = 0;
		$skipped_cnt = 0;
		
		foreach($order->items as $item){
			if($refund_item_ids && !isset($refund_item_ids[$item->id])){
				continue;
			}
			
			$obj = $item->obj;
			
			if($obj && method_exists($obj, 'orderItemRefund')){
				$obj->orderItemRefund($item->unit_price, $item->qty, $order, $item);
				$refunded_cnt++;
			}else{
				$skipped_cnt++;
				$msg = "Refund not completed for order item #{$item->id}: orderItemRefund() not implemented"
					.($obj ? " in ".get_class($obj) : ', related object not found');
				
				$this->addCommentToObject('gw_order_item', $item->id, $msg);
				$this->addCommentToObject('gw_order_group', $order->id, $msg);
			}
		}
		
		$order->recalcPaymentLedger(false);
		$order->active = 0;
		$order->open = 0;
		$order->updateChanged();
		$this->completeTrackContext($track_context, 'completed', [
			'payment_status' => (int)$order->payment_status,
			'refund_amount' => $refund_amount,
		]);
		
		$summary = "Refund marked on ".date('Y-m-d H:i:s')
			."\nAmount: ".number_format($refund_amount, 2, '.', '')." EUR"
			.($refund_uuid ? "\nUUID: ".$refund_uuid : '')
			.($refund_reference ? "\nRefund reference: ".$refund_reference : '')
			."\nRefunded items: ".$refunded_cnt
			."\nSkipped items: ".$skipped_cnt;
		
		if($refund_comment){
			$summary .= "\nDetails: ".$refund_comment;
		}
		
		$this->addCommentToObject('gw_order_group', $order->id, $summary);
		
		if(isset($_GET['sys_call'])){
			echo json_encode(['ok'=>1, 'order_id'=>$order->id, 'refunded_items'=>$refunded_cnt, 'skipped_items'=>$skipped_cnt]);
			exit;
		}
		
		if($tx = $this->getLatestOrderTransaction($order)){
			$link = $this->app->buildUri('datasources/changetransactions', [
				'transaction_id' => $tx->id,
				'clean' => 2,
			]);
			
			$this->setMessageEx([
				'text' => 'You may want to review or undo the latest transaction. '
					.'<a class="btn btn-xs btn-default iframe-under-tr" href="'.$link.'">Open latest transaction #'.$tx->id.'</a>',
				'type' => GW_MSG_WARN,
			]);
		}
		
		$this->setMessage("Refund completed for order #{$order->id}");
		$this->jumpAfterSave();
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
	
	
	
	function doOrderNotifyCustomer($order, $template_id)
	{
		
		list($invtpl, $vars) = $this->initInvoiceVars($order,['ORDER_DETAILS_HTML'=>1]);
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
		
		
		
		if($email!='vidmantas.work@gmail.com'){
			$opts['bcc'] = $this->config->confirm_email_bcc ?: GW_Mail_Helper::getAdminAddr();
			
			if($opts['bcc'] == '-')
				unset($opts['bcc']);
		}
		
		
		
		if($order->seller_id && $this->config->confirm_email_bcc!='-'){
			$opts['bcc'] = $order->seller->email;
		}
		
		$msg = GW::ln('/m/MESSAGE_SENT_TO',
			['v'=>[
			    'email'=>$email.(isset($opts['bcc']) ? ', '.$opts['bcc'] :'')
				]
			]);
		//$this->setMessage();
			
		if(isset($_GET['preview']))
			$opts['preview'] = 1;
		
		$ret = GW_Mail_Helper::sendMail($opts);
		
		if(isset($_GET['preview'])){
			$str= '<div style="padding:20px;border:1px solid silver;background-color:white">'.
				implode(',',$ret['to']).(isset($opts['bcc']) ? ', '.$opts['bcc'] :'').'<hr>'.$ret['subject'].'<hr>'.$ret['body'].
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
	
	function doOrderPaydNotifyUser()
	{		
		$template_id = $this->config->confirm_email_tpl;
		
		$order = $this->getDataObjectById();
		
		
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
		
		
		$this->doOrderNotifyCustomer($order, $template_id);
	}
	
	function doOrderStatusChangeNotifyUser()
	{
		$template_id = $this->config->statuschange_email_tpl;
		$order = $this->getDataObjectById();
		$this->doOrderNotifyCustomer($order, $template_id);
	}
	
	
	function getOrderItems($order, $export)
	{
		//$this->initOrderedItems($order);
		
		$this->tpl_vars['order'] = $order; 
		
		if(method_exists($order, 'recalcPaymentLedger'))
			$order->recalcPaymentLedger(false);
		
		$this->tpl_vars['payment_confirmations'] = method_exists($order, 'getPaymentConfirmations')
			? $order->getPaymentConfirmations()
			: [];
			
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
	
	
	
/*
 *	Siauliu banko |  montonio
            [0] => 2024.03.20
            [1] => O240801567268599
            [2] => Gautų mokėjimų eurais įskaitymas (SEPA)
            [3] => Uzsakymas nr 290 Mokėjimo identifikavimo numeris: Payment 
            [4] => DALIA .......
            [5] => Gautų mokėjimų eurais įskaitymas (SEPA)Uzsakymas nr 290 Mokėjimo identifikavimo numeris: Payment MOKĖTOJAS: DALIA ....... Sąskaita: LT55730XXXXXXXXXXXX Mokėtojo kredito įstaiga: SWEDBANK AB HABALT22XXX
            [6] => 
            [7] => 
            [8] => 
            [9] => 
            [10] => 80.00
 *  */	
	
	
	function doAddBankStatement(){
		$form = ['fields'=>['statement'=>[
		    'type'=>'textarea','width'=>'500px','height'=>'500px', 'required'=>1,
		    'note'=>'4 stulpelyje pavedimo priezastis, 7 gauta suma']],'cols'=>1];
		
		
		
		
		if(!($answers=$this->prompt($form, 'pateikite banko israsa tik israso eilutes pazymekite copy ir paste cia(veikia su siauliu banko statementu | montonio)', ['method'=>'post'])))
			return false;		
		
		
		$rows = explode("\n", $answers['statement']);
		
		$kiek = 0;
		$gwcmssumos = 0;
		$statementosumos = 0;
		
		foreach($rows as $row)
		{
			$row = explode("\t", $row);
			
			
			
			if(!preg_match('/Uzsakymas nr (\d+)/', $row[3], $matches)){
				$this->setError("Pašalinis įrašas <pre>". print_r($row, true) ."</pre>");
				continue;
			}
			
			
				
			$uzsakymonr = $matches[1];
			$amount = (float)trim($row[10]);
			
			$order = GW_Order_Group::singleton()->find(['id=?', $uzsakymonr]);
			if(!$order){
				$this->setError("Nerastas uzsakymas! $uzsakymonr ".implode('|', $row));
				continue;;
			}
			
			$extra = ['date'=>$row[0], 'payer'=>$row[4], 'amount'=>$amount];
			
			
			//d::dumpas($row[3]);
			
			if(preg_match('/Sąskaita: ([A-Z0-9]+)/', $row[5], $matches))
				$extra['payeriban']= $matches[1];
				
			if((float)$amount != (float)$order->amount_total){
				$extra['err'] = 'SUMOS!!!';
			}
			
			$order->extra = $extra;
			$order->updateChanged();
			
			$gwcmssumos += (float)$order->amount_total;
			$statementosumos += $amount;
			
			$kiek ++;
			
			//d::dumpas(['orderid'=>$uzsakymonr, 'data'=>$row, 'amount'=>$amount, 'order'=>$order, 'extra'=>$extra]);
		}
		
		$this->setMessage("Atnaujinta $kiek, sistemos sumos: $gwcmssumos, statemento sumos: $statementosumos");
		//d::dumpas();
	}
	
	function doProcessExpired()
	{
		$time = date('Y-m-d H:i:s');
		
		
		$expiredorders0 = GW_Order_Item::singleton()->findAll(['a.expires > "2000-01-01" AND a.expires < ?  AND ord.active=1 AND ord.payment_status=0', $time],[
		    'joins'=>[['left','gw_order_group AS ord','a.group_id = ord.id']],
		    'key_field'=>'group_id'
		    
		]);
		
		//d::dumpas( [array_keys($expiredorders0), GW_DB::inCondition('id', array_keys($expiredorders0)) ]);
				
		
		
		if(!$expiredorders0)
			d::dumpas('No expired records discovered for this time');
		
		
		$expire_email = GW_Mail_Template::singleton()->find(['idname=?', 'order_expired']);
		if(!$expire_email)
			d::dumpas('No expired order template found place template named: order_expired');
		
		$expiredorders = GW_Order_Group::singleton()->findAll(GW_DB::inCondition('id', array_keys($expiredorders0)));
		
		//d::dumpas( $expiredorders );
		//tie kurie pries amzinybe nustojo galiot kad nesiustu
		$recent = date('Y-m-d H:i:s', strtotime('-7 DAYS'));
		
		
		
		foreach($expiredorders as $order){
			
			
			if(isset($_GET['confirm']))
				unset($_GET['preview']);		

			if(!isset($_GET['preview'])){

				//2kartus kad nesiusti laisko
				
				$order->open = 0;
				$order->active = 0;
				$order->set("extra/expired", date('Y-m-d H:i:s'));
				
				if($order->insert_time > $recent)
					$order->set("extra/expired_mail", 1);
				
				$order->updateChanged();
			}		

			if($order->insert_time > $recent)
				$this->doOrderNotifyCustomer($order, $expire_email->id);			
			
		}	
		
	}	
	
}
