<?php

class Module_Shop extends GW_Public_Module
{
	var $view_path_index=1;
	

	
				

	/**
	 * to be expanded in cart view
	 */
	public $cart_items = [];
	
	/**
	 *
	 * @var Nat_Order
	 */
	public $order ;
	
	function init()
	{
		
		parent::init();
		$this->config = new GW_Config($this->module_path[0] . '/');
		$this->config->preload('');		
		//$this->features = array_fill_keys((array)json_decode($this->config->features), 1);
		$this->initFeatures();
		
		
		
		$this->doInitUser();
		
		$this->model = new Shop_Products;
				
		$this->paging_enabled=1;
		

		if(isset($_GET['pageby'])){
			$this->list_params['page_by']=max((int)$_GET['pageby'], 1);
			
		}else{
			if(($_GET['displ']??false)=='table'){
				$this->list_params['page_by']=64;
			}else{
				$this->list_params['page_by']=12;
			}
		}
		
		
		$this->list_params['paging_enabled']=1;
		$this->list_params['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
		
		
		if($this->feat('wishlist')){
			$this->initWishlist();
		}

		
	}
	
	function feat($id)
	{
		return isset($this->features[$id]);
	}
	
	function __eventbeforeView($params)
	{
		///taip yra blogai!
		$this->tpl_vars['page_title'] = GW::ln("/m/VIEWS/{$this->module_name}");
	}
	
	
	
	
	function initClassif()
	{
		
		$this->model = Shop_Products::singleton();
		$this->mod_fields = GW_Adm_Page_Fields::singleton()->findAll(['parent=? AND active=1 AND public=1', $this->model->table]);
		
		$types = Shop_ProdTypes::singleton()->findAll('count>0 AND active=1',['key_field'=>'id']);
		$class_types = Shop_Classificator_Types::singleton()->findAll(false, ['key_field'=>'id']);
		
		$classif = Shop_Classificators::singleton()->findAll('active=1 AND count > 0',['order'=>'type ASC, title ASC','key_field'=>'id']);
		$grouped_classif = [];
		
		foreach($classif as $class)
			$grouped_classif[$class->type][] = $class;

		
		
		
		$this->tpl_vars['classTypes'] = $class_types;
		$this->tpl_vars['classificators'] = $classif;
		$this->tpl_vars['classificatorGroup'] = $grouped_classif;
		$this->tpl_vars['prodtypes'] = $types;		
	}
	
	function getFirstClass($item)
	{
		$prodtype = $this->tpl_vars['prodtypes'][$item->type];
		$classfields = array_flip((array)$prodtype->fields);
		
		foreach($this->mod_fields as $field){
			if($field->type=="generic" || $field->type=='optional' && isset($classfields[$field->fieldname]) && $field->inp_type=="select_ajax"){
				return $field->fieldname;
			}
		}
	}
	
	function getClassifVal($val)
	{
		return $this->tpl_vars['classificators'][$val]->title;
	}
	
	function prepareList($opts=[])
	{
		$this->initClassif();
		
		$t = new GW_Timer;
		//$params = $this->prepareListParams($opts);
		
		$extra= ($opts['cond'] ?? false) ? $opts['cond'] : "1=1";
		
		if(isset($_GET['prodgroup'])){
			$extra.=" AND  `type`=".(int)$_GET['prodgroup'];
		}
		
		if(isset($_GET['classid'])){
			
			$class = $this->tpl_vars['classificators'][$_GET['classid']] ?? false;
			
			
			if($class)
				$type = $this->tpl_vars['classTypes'][$class->type] ?? false;
			
			
			if(!$class)
			{
				
				$this->setError("Class ".$_GET['classid']." not exists");
			}elseif(!$type){
				$this->setError("Class type ".$class->type." not exists");
			}else{
				$field = GW_DB::escapeField($type->key);
						
				$extra.=" AND  $field=".(int)($_GET['classid']);				
			}
				
			

		}		
		
		$params = ['conditions'=>"active=1 AND parent_id=0 AND qty>0 AND $extra"];
		
		if(isset($_GET['pageby']))
			$_GET['pageby'] = max((int)$_GET['pageby'], 1);
		
		
		$pageby = $this->tpl_vars['current_page_by'] = $_GET['pageby'] ?? $this->list_params['page_by'];
			
		
		$page = $this->list_params['page']?$this->list_params['page']-1: 0;
		$params['offset'] = $pageby*$page;
		$params['limit'] = $pageby;	
		
		
		
		
		$validord=['priority','title','price'];
		$this->tpl_vars['validord'] = $validord;
		$args['ord'] = $_GET['ord'] ?? false;
		
		if(!in_array($args['ord'], $validord))
			$args['ord'] = $validord[0];
		
		$params['order']=$args['ord']. ($args['ord'] == $validord[0] ? ' DESC' : ' ASC');		
		
		
		$list=$this->model->findAll($params['conditions'], $params);
				
		$debug_q = GW::db()->last_query;
		
		//d::dumpas($debug_q);
		
		$this->setUpPaging($this->model->lastRequestInfo());		
		
		$this->afterList($list);	

		$this->smarty->assign('list', $list);		
		
		$s = $t->stop();
		
		
		
		
		if($this->app->user && $this->app->user->isRoot() && ($this->app->sess['debug'] ?? false))
			$this->setMessage('<pre>'.SQL_Format_Helper::format($debug_q).'</pre><br/> '.$s.' secs');
		
	}
	
	
	function getVal($field, $val, $cfg){
	
				
		if($cfg->inp_type=='select_ajax' && $cfg->modpath=="shop/classificators"){

			return $this->tpl_vars['classificators'][$val]->title;
		}
		
		
		if($cfg->inp_type=="bool")
		{
			return GW::ln('/m/FIELD_'.$cfg->fieldname.'_'.$val);
		}
		
		return $val;
	}
	
	function viewDefault()
	{
		$this->prepareList();
	}

	function viewItem()
	{
		$item = $this->model->createNewObject($_REQUEST['id'], 1);
		
		if(isset($_GET['test']))
			backtrace();

		

		$this->tpl_vars['item'] = $item;
	}




	/*
	function doRemoveFromCart()
	{
		unset($this->cart_products[$_REQUEST['id']]);
		$this->saveCart();

		$this->app->jump();
	}
*/



	

	
	function viewP()
	{
		$this->initClassif();
		
		
/*		
<meta property="og:url" content="https://www.musicshopeurope.com/product/0001-89-010 m/festival-fanfare.aspx" />
<meta property="og:title" content="Festival Fanfare" />
<meta property="og:description" content="This famous piece by the Swiss composer Franco Cesarini is a festive way to start your concert!" />
<meta property="og:image" content="https://www.musicshopeurope.com/content/files/images/ProductImages/large/0001-89-010 m_1.jpg" />
<meta property="og:image:type" content="image/jpeg" />
<meta property="og:audio" content="https://www.musicshopeurope.com/content/files/Audios/0001-89-010 M_1.mp3" />
<meta property="og:audio:type" content="audio/mpeg" />
<meta property="og:video" content="https://youtu.be/TmkBxlw1REI" />		
*/		
		
		$item=$this->getDataObjectById();
		
		
		
		if(!$item)
		{
			$this->setError("Item not found");
			$this->app->jump('direct/shop/shop');
		}
		
		$this->afterList([$item]);
		
		
		
		$this->tpl_vars['addinfo'] = $item->extensions['keyval']->getAll();
		
		if(isset($_GET['debug']))
			d::Dumpas($this->tpl_vars['addinfo']);
		
		
		if($this->feat('prod_visit_history')){
			$this->addHistory($item);
		}
		
		
		

		
		if($this->feat('modifications')){
			
			
			
			$modifications = $item->findAll(['active=1 AND parent_id=?',$item->id],['key_field'=>'id','order'=>'priority DESC']);
			
			
			$get_price_range = function($modifications){
				$minprice = 99999999;
				$maxprice = 0;
				foreach($modifications as $mod){
					$minprice = min($mod->price, $minprice);
					$maxprice = max($mod->price, $maxprice);
				}

				if($minprice==99999999){
					$minprice = 0;
				}				
				return [$minprice, $maxprice];
			};


			
			
			
			
			$this->tpl_vars['modifications'] = $modifications;
			$this->tpl_vars['modifications_pricerange'] = $get_price_range($modifications);
			
	
			if($_GET['modid'] ?? false){
				$amod = $modifications[$_GET['modid']] ?? false;
				if($amod){
					$oitem = $item;
					$item = $amod;


					$modifications_subs =  $item->findAll(['active=1 AND parent_id=?',$item->id],['key_field'=>'id','order'=>'priority DESC']);
					$this->tpl_vars['modifications_subs'] = $modifications_subs;

					$this->tpl_vars['modifications_pricerange'] = $get_price_range($modifications_subs);
				}
			
			}
			
			//d::dumpas([$minprice, $maxprice]);

			
			if($_GET['smodid'] ?? false){
				
				
				$amod = $modifications_subs[$_GET['smodid']] ?? false;
				
				
				
				
				
				//d::dumpas($amod);
				$oitem = $item;
				$item = $amod;
			}

			
			
			$this->tpl_vars['active_mod'] = $amod ?? false;
		}
		
		$this->tpl_vars['item'] = $item;
		$this->tpl_vars['oitem'] = $oitem ?? $item;		
		
		$this->tpl_vars['breadcrumbs_attach'] = [['title'=>$item->title]];
		
		
		
		$this->tpl_name = 'p_'.($this->config->product_tpl?:'default');
		
		
	}
	
	
	function setUid(&$data)
	{
		if($this->app->user && $this->app->user->id){
			$data['user_id'] = $this->app->user->id;
		}else{
			$data['auser_id'] = substr($_COOKIE['user_secret']??'', 0, 10);
		}		
	}

	function addHistory($item)
	{
		$data = [];
		$this->setUId($data);
		
		//$data['instrumn_id'] = $item->instrumn_id;
		
		$data['product_id'] = $item->id;
		
		
		$conds = GW::db()->prepare_query(GW::db()->buidConditions($data));
		
		//1 vieta kur timestamp naudoju po 12 metu patirties
		
		if($cnt = GW::db()->fetch_result("SELECT cnt FROM shop_product_history WHERE ".$conds)){
			GW::db()->update('shop_product_history', $conds, ['cnt'=>$cnt+1,'update_time'=>date('Y-m-d H:i:s')]);
		}else{
			GW::db()->insert('shop_product_history',$data+['cnt'=>1,'insert_time'=>date('Y-m-d H:i:s')]);
		}
	}
	
	function prepareHistoryList($limit)
	{
		$data = [];
		$this->setUId($data);
		
		$conds = GW::db()->prepare_query(GW::db()->buidConditions($data));
		
		$extra = "1=1";
		
		if(isset($_GET['id'])){
			$extra="product_id!="	.(int)$_GET['id'];
		}
		
		$ids = GW::db()->fetch_one_column("SELECT product_id FROM shop_product_history WHERE ".$conds." AND $extra ORDER BY update_time DESC LIMIT $limit");
		
 		if($ids)			
			$this->prepareList(['cond'=>GW_DB::inCondition('id', $ids)]);		
	}
	
	function viewInproductHistory()
	{
		$this->prepareHistoryList(6);
		$this->tpl_name = 'inproduct_history';
	}
	
	
	function viewHistory()
	{
		$this->prepareHistoryList(1000);
		$this->tpl_vars['nofilters']=1;			
	}

	function viewSuccess()
	{

	}
	
	
	public $wishlist_type=1;
		
	function initWishlist()
	{
		if($this->app->user){
			$list0 = GW::db()->fetch_one_column(["SELECT product_id FROM shop_user_wishlist WHERE user_id=? AND type=?", $this->app->user->id, $this->wishlist_type]);
			$list = [];

			foreach($list0 as $id)
				$list[$id] = $id;
		}else{
			$list = [];
		}
		GW::$globals['GW_SHOP_wishlist'] = $list;
				
		return $list;		
	}	
	
	function doAdd2Wishlist()
	{
		$this->userRequired();
		
		$item=$this->getDataObjectById();
		
		if(!isset(GW::$globals['GW_SHOP_wishlist']))
			$this->initWishlist();
		
		
		if(isset(GW::$globals['GW_SHOP_wishlist'][$item->id]))
		{
			unset(GW::$globals['GW_SHOP_wishlist'][$item->id]);
			GW::db()->delete("shop_user_wishlist", ["user_id=? AND product_id=? AND type=?", $this->app->user->id, $item->id, $this->wishlist_type]);
			
			$action = "REMOVED_FROM";
			$linksnis="kil";
		}else{
			GW::$globals['GW_SHOP_wishlist'][$item->id] = $item->id;
			GW::db()->insert("shop_user_wishlist", ["user_id"=>$this->app->user->id, 'product_id'=>$item->id, 'type'=>$this->wishlist_type, 'insert_time'=>date('Y-m-d H:i:s')]);
			
			$action = "ADDED_TO";
			$linksnis="gal";
		}
		
		$this->setMessage([
		    'text'=>'<b>'.$item->title.'</b> '.GW::ln('/M/SHOP/'.$action).' '.GW::ln('/M/SHOP/WISHLIST', ['l'=>$linksnis,'c'=>1]),
		    'type'=>0,
		    'buttons'=>[['title'=>'<i class="fa fa-heart"></i> '.GW::ln('/m/VIEW_WISHLIST'), 'url'=>$this->app->buildUri('direct/shop/shop/wishlist')]]
			]);		
		
		
		if(isset($_GET['jump'])){
			unset($_GET['act']);
			$this->app->jump($this->app->path, $_GET);
		}
		
		$this->jsonResponse(['status'=>"ok", 'item_title'=>$item->title]);
		exit;
	}
	
	function viewWishlist()
	{		
		if(!$this->app->user){
			$this->setMessage(GW::ln('/m/NEED_AUTHORISE_TO_ACCESS_WISHLIST'));
			$this->app->jump('direct/users/users/login',['after_auth_nav' => $_SERVER['REQUEST_URI']]);
		}
		
		if(!isset(GW::$globals['GW_SHOP_wishlist']))
			$this->initWishlist();
		
		$ids = array_keys(GW::$globals['GW_SHOP_wishlist']);
		
		if($ids)			
			$this->prepareList(['cond'=>GW_DB::inCondition('id', $ids)]);
		
		$this->tpl_vars['nofilters']=1;		
	}
	
	
	
	function isItemInCart($item)
	{
		$cart = GW::$globals['site_cart'];
		return $cart && (bool)$cart->getItem($item);
	}
	
	function isItemInWishlist($id)
	{
		return isset(GW::$globals['GW_SHOP_wishlist'][$id]);
	}
	
	

	

	
	function doInitUser()
	{
		//if(!isset($_COOKIE['user_secret']))
		//		$this->setCookie("user_secret", GW_String_Helper::getRandString(20));			
	}	
		
	function doInitProducts()
	{		
		$this->doInitUser();
		
		
		if($this->feat('wishlist')){
			$vars['wishlist'] = $this->initWishlist();
		}
		
		
		return $vars;
	}
	
	function doAdd2Cart()
	{
		
		$cartvals = $_REQUEST['item'];
		
		
		
		$item = Shop_Products::singleton()->find(['id=? AND active=1', $cartvals['id']]);
		
		if(!$item){
			$this->setError("Item not available");
			$this->app->jump();
		}
				
		
		if($this->feat('anonymous_cart') && !$this->app->user){
			$auser = $this->app->initAnonymousUser();
			$cart = $auser->getCart(true);
			

		}else{
			$auser = false;
			$this->userRequired();
			$cart = $this->app->user->getCart(true);
		}
		
		
			//if($_SERVER['REMOTE_ADDR']=='84.15.236.87'){
			//	d::dumpas($cart);
			//}	
		//$data = $this->rootConfirmJson($cart->toArray());
		//if(!$data)
		//	return false;
		//	
		////// KITAS SELLERIS-------------------------------------------
		
		if($cart->items && ($cart->seller_id || $item->seller_id) && $cart->seller_id!=$item->seller_id->seller_id){
			$caption = GW::ln('/M/ORDERS/VIEWS/doCloseOrder');
			$url = $this->app->buildUri('direct/orders/orders', ['act'=>"doCloseOrder",'id'=>$cart->id]);
		
			$closebtn = "<a href=\"$url\" class=\"btn u-btn-brown btn-xs rounded-0\">
						      <i class=\"fa fa-times\"></i> $caption
						      </a>";
			//"Jūsų krepšelyje yra prekių/paslaugų iš kito pardavėjo. Jūs galite 1. $closebtn - galėsite per <b>Mano užsakymai</b> pasirinkti 'pildyti toliau' arba 2. Užbaigti su ankstesnio krepšelio pildymu ir sugrįšti čia vėliau"
			$this->setPlainMessage(GW::ln("/g/SELLER_DIFFER1").$closebtn.GW::ln("/g/SELLER_DIFFER2"), GW_MSG_WARN);
			
			$args = $_GET;
			unset($args['act']);
			$this->app->jump(false, $args);
			return false;
		}
		
		if($item->seller_id){
			$cart->seller_id = $item->seller_id;
			$cart->updateChanged();
		}
		////// KITAS SELLERIS-------------------------------------------
		
		if($item->free)
		{
			$cart = GW_Order_Group::singleton()->createNewObject(['user_id'=>$this->app->user->id]);
			$cart->open = 0;
			$cart->payment_status = 0;
			$cart->active = 1;
			$cart->insert();			
		}
		
	
				
		$payprice = $item->calcPrice($cartvals['qty']);
		
		if($item->free){
			$payprice = 0;
		}

		//nebepridet pakartotinai
		$cartitem = $cart->getItem(Shop_Products::singleton()->createNewObject($cartvals['id'])) ?: new GW_Order_Item;
		
		
		$url = $this->app->buildUri('direct/shop/shop/p',['id'=>$item->id]);
		
		
		$orderqty = ($cartvals['qty'] ?? 1);
		
		
		
		if(!$payprice && $item->singlebuyredirect)
		{
			header('Location: '.$item->singlebuyredirect);
			exit;
		}
			
			
		
		
		$vals = [
			'obj_type'=>'shop_products',
			'obj_id'=>$item->id,
			'qty' => min($cartitem->qty + $orderqty, $item->qty),
			'unit_price'=>$payprice,
			//'context_obj_id'=>$user->id,
			//'context_obj_type'=>'gw_customer'
			
			'deliverable'=>$this->feat('delivery') ? 10 : 0, //real item
			'vat_group'=>$item->vat_group ? $item->vat_group : $this->config->vatgroup,
			'link' =>$url
		];
		
		
		if($this->feat('no_qty_change_in_cart')){
			$vals['qty_range']= "$orderqty;$orderqty";
		}else{
			$vals['qty_range']="1;$item->qty";
		}
		
		//modifikacijoms
		if($item->parent_id){
			$vals['context_obj_type'] = 'shop_products';
			$vals['context_obj_id'] = $item->parent_id;
			$vals['invoice_line2'] = $item->title; //nes is admin kitaip title prideda
		}
		
		$cartitem->setValues($vals);
		
		$cart->addItem($cartitem);
		
		
		if($this->feat('jump2cartafteradd')){
			$args=[];
			if($auser){
				$args['key']=$cart->secret;
				$args['orderid']=$cart->id;
				$args['id']=$cart->id;
			}
			
			//if($auser)
			//	$args['key']=$cart->secret;			
			
			$this->app->jump('direct/orders/orders/cart', $args);
		}else{
		
			if($item->free){
				
			$this->setMessage([
			    'text'=>'<b>'.$item->title.'</b> '.GW::ln('/M/SHOP/FREE_RESERVATION_APPROVED'),
			    'type'=>0,
			    'buttons'=>[['title'=>'<i class="fa fa-shopping-cart"></i> '.GW::ln('/m/VIEW_ORDER'), 'url'=>$this->app->buildUri('direct/orders/orders', ['id'=>$cart->id])]]
				]);	
				
				$args = ['id'=>$cart->id];		
				$args['paytest']=1;
				$args['rcv_amount'] = 0;


				$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?act=doMarkAsPaydSystem&sys_call=1&'. http_build_query($args));				
				
			}else{
				$this->setMessage([
			    'text'=>'<b>'.$item->title.'</b> '.GW::ln('/M/SHOP/ADDED_TO').' '.GW::ln('/M/SHOP/CART', ['l'=>'gal','c'=>1]),
			    'type'=>0,
			    'buttons'=>[['title'=>'<i class="fa fa-shopping-cart"></i> '.GW::ln('/m/VIEW_CART'), 'url'=>$this->app->buildUri('direct/orders/orders/cart')]]
				]);
			}
		

		

			//d::dumpas('aaa');
			$args = $_GET;
			unset($args['act']);
			$this->app->jump(false, $args);
		}
		
	}	
	
	

	public function afterList($list) 
	{
		
		
	}
		
	
	function canSeeOrders()
	{
		if($this->app->user)
			return in_array($this->config->shop_orders_viewers_group, $this->app->user->group_ids);		
	}
	
	function getOrders($item, $subitems=false)
	{
		/*
			'obj_type'=>'shop_products',
			'obj_id'=>$item->id,
		 * 		 */
		
			$order_fields = "aa.user_id, aa.payment_status, aa.pay_time, aa.pay_test";
			$params['select']='a.*, '.$order_fields;
			$params['joins']=[
			    ['left','gw_order_group AS aa','a.group_id = aa.id'],
			];			
			$params['order']="payment_status DESC, pay_time DESC";
		
		$ids = [$item->id] ;
		
		if($subitems){
			$ids = array_merge($ids, $subitems);
		}
		
		$list = GW_Order_Item::singleton()->findAll(
			'obj_type="shop_products" AND processed=0 AND aa.payment_status=7 AND pay_test=0 AND '.GW_DB::inCondition('obj_id', $ids), $params
		);
		

		
		return $list;
	}
	
	function getAmountReceived($item, $subitems){
		$ois = $this->getOrders($item, $subitems);
		
		
		//d::dumpas($ois);
		
		$sum = 0;
		foreach($ois as $oi)
			$sum+=$oi->total;
		
		return $sum;
	}
	
}