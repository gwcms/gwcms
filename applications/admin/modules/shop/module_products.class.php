<?php


class Module_Products extends GW_Common_Module
{
	use Module_Import_Export_Trait;

	public $dynamic_fields = true;
	/**
	 * @var GW_Product
	 */

	function init()
	{
		$this->initLogger();

		$this->config = new GW_Config($this->module_path[0].'/');

		
		$this->initFeatures();
		
		parent::init();
		$this->model = Shop_Products::singleton();
		$this->mod_fields = GW_Adm_Page_Fields::singleton()->findAll(['parent=?', $this->model->table],['key_field'=>'fieldname']);
		
		
	
		$this->list_params['paging_enabled']=1;
		
		$this->addRedirRule('/^doImport|^viewImport/i','import');
		
		
		//is import in progress
		
		
		if(isset($_GET['parent_id'])){
			$this->filters['parent_id']=$_GET['parent_id'];
			$this->parent = Shop_Products::singleton()->find(['id=?', $this->filters['parent_id']]);
		}
		
		
		if($this->filters['parent_id'] ?? false){
			$this->list_params['paging_enabled']=false;
		}
		
		
		$this->app->carry_params['skipsearch'] = 1;
		$this->app->carry_params['parent_id'] = 1;
		$this->app->carry_params['mods'] = 1;
		
		
		if($this->feat('vatgroups'))
			$this->options['vatgroups'] = GW_VATgroups::singleton()->getOptions();

	}

	function __eventAfterList($list)
	{
		GW_Composite_Data_Object::prepareLinkedObjects($list, 'typeObj');
		
		
		
		//perziuret nes atsirado an common_modukle initDynFieldsList metodas
		$sources=[];
		$dynfieldsopts = [];
		
		foreach($this->mod_fields as $field){
			if($field->inp_type=="select_ajax"){
				$sources[$field->modpath][] = $field->fieldname;
			}
		}
		
		foreach($sources as $modpath => $fields){
			
			$model = $this->mod_fields[ $fields[0] ]->modelFromModpath();
			
			foreach($fields as $field)
				$dynfieldsopts[$field] = $model;
		}
		
		//d::Dumpas($dynfieldsopts);
		
		
		
		$ids = array_keys($list);
		
		if(isset($this->features['modifications'])){
			$cnts = Shop_Products::singleton()->getModCounts($ids);
			foreach($cnts as $pid => $cnt)
				$list[$pid]->mod_count = $cnt;
		}
		
		
		$dynopts = [];
		
		foreach($list as $item){
			foreach($dynfieldsopts as $field => $class)
				if($item->$field)
					$dynopts[$class][$item->$field]=1;
		}
		
		foreach($dynopts as $class => $ids){
			$ids = array_keys($ids);
			$this->options[$class] = $class::singleton()->findAll(GW_DB::inCondition ('id', $ids),['key_field'=>'id']);
		}
				
		$this->tpl_vars['dynfieldopts']=$dynfieldsopts;
		
		

		
				
		
		
		//$pieces0 = IPMC_Competition_Pieces::singleton();
		if(isset($_GET['parent_id'])){
			//$this->list_config['display_fields']['title']=0;
			$fields = array_flip($this->list_config['dl_fields']);
			unset($fields['title']);
			$this->list_config['dl_fields'] = array_keys($fields);
			$this->tpl_vars['dl_fields'] = $this->list_config['dl_fields'];
		}
	}
		
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		$cfg['fields']["image"] = "L";
		
		if(isset($this->features['modifications']))
			$cfg['fields']["mod"] = "L";
		
		
		if(isset($_GET['mods'])){
			//unset($cfg['fields']["mod"]);
			$cfg['fields']["modif_title"] = "L";
		}
		
		$cfg['inputs']['seller_id']=['type'=>'select_ajax', 'modpath'=>"payments/sellers", 'empty_option'=>1,'options'=>[],'preload'=>1];		
		
		
		$cfg['fields']["vat_group"] = $this->feat('vatgroups') ? "Lof": 'lof';
		
		
		$cfg['fields']["changetrack"] = "L";
		$cfg['fields']["orders"] = "L";
		
		
		return $cfg;
	}		
	
	
	function doCreateModification()
	{
		$item = $this->getDataObjectById();
		$mod = $this->model->createNewObject();
		$mod->parent_id = $item->id;
		$mod->title = "Modification of ".$item->title;
		//$mod->modif_title = "";
		
		//d::dumpas($mod);
		$mod->insert();
		//d::dumpas($mod);
		$this->setMessage("Mofication was created");
		
		
		Navigator::jump($this->buildUri("$mod->id/form", ['parent_id'=>$item->id]));
	}
	
	function __eventBeforeListParams(&$params)
	{
		//modifikacijoms
		if(!isset($_GET['parent_id']))
			$params['conditions']="parent_id=0";
	}	
	
	
	
	
	
	function createOrder($answers)
	{
		$price = $answers['price'];
		$item = Shop_Products::singleton()->find(['price=?', $price]);		
		
		$order = GW_Order_Group::singleton()->createNewObject();
		$order->extra = ['cardnr'=>$answers['cardnr'],'receiptnr'=>$answers['receiptnr']];
		$order->delivery_opt = 3;
		//$order->pay_type = 5;
		$order->payment_status = 7;;
		$order->status = 7;
		$order->insert();
		
		if($answers['insert_time'])
			$order->saveValues(['insert_time'=>$answers['insert_time']]);
		
		
		$url = $this->app->buildUri('direct/shop/products/p',['id'=>$item->id],['app'=>'site']);
		
		$cartitem = new GW_Order_Item;
		$cartitem->setValues([
			'obj_type'=>'shop_products',
			'obj_id'=>$item->id,
			'qty' => 1,
			'unit_price'=>$price,
			//'context_obj_id'=>$user->id,
			//'context_obj_type'=>'gw_customer'
			'qty_range'=>"1;1",
			'deliverable'=>10, //real item
			'link' =>$url
		]);
		
		$order->addItem($cartitem);			
	}
	
	function doCreateOrderByPrice()
	{
		$form = ['fields'=>[
		    'price'=>['type'=>'text', 'required'=>1],
		    "cardnr"=>['type'=>'text', 'required'=>1],
		    "receiptnr"=>['type'=>'text', 'required'=>1]
		],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, "Add order with 1 item, product will be identified by price")))
			return false;
		
		
		
		$this->createOrder($answers);	

		d::dumpas([$answers,$item, $order, $order->errors,GW::db()->last_query]);
	}
	

	function doOrdersImportSwedXml()
	{
		$form = ['fields'=>[
		    'file'=>['type'=>'file', 'required'=>1],
		],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, "Attach xml file")))
			return false;		
		
		$file = $answers['file']['tmp_name'];
		
		$tree = GW_XML::xmlToArray(file_get_contents($file));
		$transactions = GW_XML::simpleXmlArrFixList($tree['company']['outlet']['terminal']['batch']['card_group']['transaction']);
		//$flat = GW_Array_Helper::arrayFlattenSep('/', $tree);

		foreach($transactions as $tr){
			$answers = [
			    'price'=>$tr['paym_amount'],
			    'cardnr'=>$tr['hidden_pan'],
			    'receiptnr'=>$tr['stan'],
			    'insert_time'=>$tr['local_date'].' '.$tr['local_time'],
			];
			
			$this->createOrder($answers);
			
			$this->setMessage("Price: {$answers['price']}; Cardnr: {$answers['cardnr']}; ReceiptNr: {$answers['receiptnr']}, Time: {$answers['insert_time']}");
		}

		
	}
	
	function getOptionsCfg()
	{
		$opts = [
		    //$item->get('title') // blogai - nes hackas su modifikacijom per __get eina, paliekam $item->title workaroundas
		    'title_func'=>function($item){ return $item->title;  },
		    'search_fields'=>['title','modif_title']
		];	
		    
		if(isset($_GET['parent_id'])){
			$opts['condition_add'] = "parent_id=".(int)$_GET['parent_id'];
		}

		return $opts;	
	}	
	
	
	
	
	
	
	function generatePasscode($item, $product)
	{		
		//not ttlock api config present
		if(!ttlock_api::singleton()->init()->cfg->client_id)
			return false;
		

		
		$code = rand(10000,99999);
		
		$start = strtotime($product->date.' '.$product->start_time.' -20 minutes');
		$end = strtotime($product->date.' '.$product->end_time);
		
		$code=ttlock_api::singleton()->init()->addRandomPasscodeStore($start,$end,'order/'.$item->id);
		$item->set('keyval/door_code_id', $code->id);
		
		
		if($code)
			return true;
	}
	
	
	function doAfterBuyAdminEmail()
	{
		$item = GW_Order_Item::singleton()->find(['id=?', $_GET['id']]);
		$product = $item->obj;	
		$template_id = $this->config->notify_admin_template_id;
		
		if(!$template_id){
			$this->setError("No notify admin mail templateid not configured");
			return false;
		}
		
		
		$item->order->setSecretIfNotSet();
		
		$vars['ordereditem'] = $item;
		$vars['product'] = $product;
		$vars['SITE_DOMAIN'] = parse_url(GW::s('SITE_URL'), PHP_URL_HOST);
		$vars['CLIENT_TITLE'] = $item->order->user->title;
		$vars['order'] = $item->order;
		$vars['buyer'] = $item->order->user;
			
		
		$tmp = $this->app->buildURI('direct/orders/orders/statuschange', 
			['id'=>$item->order->id,'key'=>$item->order->secret, 'executor_id'=>$item->executor_id],
			['app'=>"site"]);
		$vars['STATUS_CHANGE_URL'] = GW::s('SITE_URL').trim($tmp,'/'); //workaround kad nuimt pradzioje /
			
		
		
		
		$to = $product->modval('notify_admin');
		
		//d::dumpas(GW_Mail_Template::singleton()->find($template_id));
		
		$opts = [
		    'to'=>$to,
		    'tpl'=>GW_Mail_Template::singleton()->find($template_id),
		    'vars'=>$vars,
		    //'attachments'=>[$filename=>$pdf]
		];
		

		$file_link = GW::s('SITE_URL');
		
		
		
		$msg = GW::ln('/g/MESSAGE_SENT_TO',['v'=>['email'=>$to]]);
		//$this->setMessage();\
		
		
		//$opts['dryrun'] = 1;
		
		$status = GW_Mail_Helper::sendMail($opts);
		
		
		//d::Dumpas($opts);
		
		$item->set('keyval/email_admin',$status);
		
			
		
		if(isset($_GET['sys_call'])){
			echo json_encode(['resp'=>$msg]);
			exit;
		}else{
			$this->setMessage($msg);
			$this->jump();
		}
	}
	
	
	function doAfterBuyEmail()
	{		
		
		$item = GW_Order_Item::singleton()->find(['id=?', $_GET['id']]);
		
		$product = $item->obj;
		
		$template_id = $product->modval("after_buy_email_tpl");
		
		if(!$template_id)
			return false;
		
		
		$to = $item->order->user->email;
		$vars['item'] = $item;
		$vars['product'] = $product;
		$vars['SITE_DOMAIN'] = parse_url(GW::s('SITE_URL'), PHP_URL_HOST);
		$vars['CLIENT_TITLE'] = $item->order->user->title;
				
		
		if($this->feat('ttlock') && $product->date && $product->start_time && $product->end_time){
			if(!$this->generatePasscode($item, $product)){
				$opt=[
				    'subject'=>GW::s('PROJECT_NAME').' nepavyko sugeneruoti kodo',
				    'body'=>"order: {$item->order->id}, recipient:$to order_item_id: {$item->id} product: {$product->title}"
				];
				    
				GW_Mail_Helper::sendMailDeveloper($opt);
				return false; // nepavyko sugeneruot kodo
			}
			
			$vars['access_code'] = $item->get('door_code');
		}				
		
		$opts = [
		    'to'=>$to,
		    'tpl'=>GW_Mail_Template::singleton()->find($template_id),
		    'vars'=>$vars,
		    //'attachments'=>[$filename=>$pdf]
		];
		
		
		
		
		$msg = GW::ln('/g/MESSAGE_SENT_TO',['v'=>['email'=>$to]]);
		//$this->setMessage();
		
		$status = GW_Mail_Helper::sendMail($opts);
		
		$item->set('keyval/email',$status);
		
			
		
		if(isset($_GET['sys_call'])){
			echo json_encode(['resp'=>$msg]);
			exit;
		}else{
			$this->setMessage($msg);
			$this->jump();
		}
	}	
	
	
	function doAfterBuyExecutorEmail()
	{		
		
		$item = GW_Order_Item::singleton()->find(['id=?', $_GET['id']]);
		
		$product = $item->obj;
		
		$template_id = $product->modval("executor_after_buy_email_tpl");
		
		if(!$template_id){
			$this->setError("No executor mail templateid not configured");
			return false;
		}
		
		
		if(!$item->executor_id){
			$this->setError("No executor assigned");
			return false;
		}
		
		$executor = Shop_Executors::singleton()->find($item->executor_id);
		
		$item->order->setSecretIfNotSet();
		
		$vars['ordereditem'] = $item;
		$vars['product'] = $product;
		$vars['SITE_DOMAIN'] = parse_url(GW::s('SITE_URL'), PHP_URL_HOST);
		$vars['CLIENT_TITLE'] = $item->order->user->title;
		$vars['order'] = $item->order;
		$vars['buyer'] = $item->order->user;
		$vars['executor'] = $executor;
		$vars['EXECUTION_FILE_URL'] = GW::s('SITE_URL').$this->app->buildURI('direct/orders/orders', 
			['act'=>'doRetrieveExecFile','id'=>$item->order->id,'ordered_item_id'=>$item->id,'key'=>$item->order->secret],['app'=>"site"]);
			
		
		$tmp = $this->app->buildURI('direct/orders/orders/statuschange', 
			['id'=>$item->order->id,'key'=>$item->order->secret, 'executor_id'=>$item->executor_id],
			['app'=>"site"]);
		$vars['STATUS_CHANGE_URL'] = GW::s('SITE_URL').trim($tmp,'/'); //workaround kad nuimt pradzioje /
			
		
		$to = $executor->email;
		
		
		
		
		$opts = [
		    'to'=>$to,
		    'tpl'=>GW_Mail_Template::singleton()->find($template_id),
		    'vars'=>$vars,
		    //'attachments'=>[$filename=>$pdf]
		];
		

		$file_link = GW::s('SITE_URL');
		
		
		
		$msg = GW::ln('/g/MESSAGE_SENT_TO',['v'=>['email'=>$to]]);
		//$this->setMessage();\
		
		
		//$opts['dryrun'] = 1;
		
		$status = GW_Mail_Helper::sendMail($opts);
		
		
		//d::Dumpas($opts);
		
		$item->set('keyval/email_executor',$status);
		
			
		
		if(isset($_GET['sys_call'])){
			echo json_encode(['resp'=>$msg]);
			exit;
		}else{
			$this->setMessage($msg);
			$this->jump();
		}
	}	
	
}

