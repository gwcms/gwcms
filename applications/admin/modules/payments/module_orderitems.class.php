<?php


class Module_OrderItems  extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	
	public $cartgroup_id=false;
		
	function init()
	{	
		
		$this->model = GW_Order_Item::singleton();
		
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		if(isset($_GET['pageby']))
			if($_GET['pageby']==-1){
				$this->list_params['paging_enabled']=false;	
			}else{
				$this->list_params['page_by'] = $_GET['pageby'];
			}
			
		
	
		if($this->app->path_arr['1']['path_clean'] == 'payments/ordergroups'){
			$this->cartgroup_id = $this->app->path_arr['1']['data_object_id'] ?? false;
			$this->cartgroup = GW_Order_Group::singleton()->find(['id=?', $this->cartgroup_id]);
		}
		
		if($this->cartgroup ?? false)
		{
			$this->filters['group_id']=$this->cartgroup_id;
		}

		if(isset($_GET['obj_type']))
			$this->filters['obj_type']=$_GET['obj_type'];
		
		if(isset($_GET['obj_id']))
			$this->filters['obj_id']=$_GET['obj_id'];	
		
		
		
		if(isset($_GET['context_obj_type']))
			$this->filters['context_obj_type']=$_GET['context_obj_type'];
		
		if(isset($_GET['context_obj_id']))
			$this->filters['context_obj_id']=$_GET['context_obj_id'];
		
		if(isset($_GET['processed']))
			$this->filters['processed']=$_GET['processed'];

		

		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['obj_type'] = 1;
		$this->app->carry_params['obj_id'] = 1;	
		$this->app->carry_params['orderflds'] = 1;
		$this->app->carry_params['flds'] = 1;
		$this->app->carry_params['ord'] = 1;
		$this->app->carry_params['noactions'] = 1;
		$this->app->carry_params['pay_interval'] = 1;
		$this->app->carry_params['pay_test'] = 1;
		$this->app->carry_params['processed'] = 1;
		$this->app->carry_params['context_obj_type'] = 1;
		$this->app->carry_params['context_obj_id'] = 1;
		$this->app->carry_params['groupby'] = 1;
		$this->app->carry_params['pageby'] = 1;
		
		$this->config =  new GW_Config($this->module_path[0].'/');
		$this->initFeatures();
		
		
		$this->options['vatgroups'] = GW_VATgroups::singleton()->getOptions();
		$this->initObjTypes();
		
		
		$this->mod_fields = GW_Adm_Page_Fields::singleton()->findAll(['parent=?', $this->model->table],['key_field'=>'fieldname']);
		$this->item_remove_log=1;
		
		$this->sellers_enabled = GW_Permissions::canAccess('payments/sellers',true, $this->app->user->group_ids, false);
	}
	

	
	
	public $extra_cols=[ 'name'=>1,'surname'=>1, 'company'=>1,  'company_code'=>1  ];	
	
	function getListConfig()
	{
		
		$cfg = array('fields' => [
			'id' => 'Lof', 
			'invoice_line2'=>'Lof',
			'obj_type'=> 'lof',
			'obj_id'=> 'lof',
			'modpath' => 'lof',
			'unit_price' => 'Lof',
			'qty' => 'Lof',
			'total'=>'L',
			'vat_group'=>'Lof',
			'insert_time'=>'lof',
			'update_time'=>'lof',	
			'group_id'=>'lof',
			'company'=>'lof',
			'surname'=>'lof',
			'name'=>'lof',
			'company_code'=>'lof',
			'pay_month'=>'lof',
			'amount_calc'=>'lof'
			]
		);
		
		
		if(!$this->cartgroup_id){
			$cfg['fields']['user_id'] = "Lof";
			$cfg['fields']['user_title'] = 'Lf';	
			$cfg['fields']['user_email'] = 'Lf';	
			$cfg['fields']['payment_status'] = 'Lof';	
			$cfg['fields']['pay_time'] = 'Lof';	
			$cfg['fields']['pay_test'] = 'Lof';	
		}
		$cfg['fields']['buyer_details'] = 'L';	
		
		$cols=$this->model->getColumns();
		
		if($cols['status'] ?? false){
			$cfg['fields']['status'] = 'Lof';	
		}
		
		
		if($this->app->user->isRoot()){
			$cfg['fields']['expires'] = 'Lof';
		}
		

		
		$cfg['filters']['vat_group'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'payments/vatgroups'];
		$cfg['filters']['obj_type'] = ['type'=>'select','options'=>$this->options['obj_type']];
		$cfg['filters']['pay_time'] = ['type'=>'daterange', 'datetimefiltp1d'=>1, 'ct'=>['DATERANGE'=>'RANGE']];
		
		
		$ocols = GW_Order_Group::singleton()->getColumns();
		
		
		if($this->sellers_enabled){
			$cfg['fields']['seller_id'] = 'Lof';
			
			$this->options['seller_id'] = GW_Pay_Sellers::singleton()->getOptionsShort();
		}
		
		if($this->feat('discountcode')){
			$cfg['fields']['coupon_codes'] = 'L';
		}
		
		
		
		
		
		return $cfg;
	}
	


	function __eventBeforeListParams(&$params)
	{		

		if(!$this->cartgroup_id)
		{
			$order_fields = "ord.user_id, ord.payment_status, ord.pay_time, ord.pay_test, ord.company, ord.company_code";
			
			if($this->sellers_enabled)
				$order_fields.=", ord.seller_id";
			
			
			
			
			if($this->list_config['pview'] && $this->list_config['pview']->select){
				//grby fails
				$params['select']='a.*';
			}else{
				$params['select']='a.*, usr.name, usr.surname, '.$order_fields;
			}

			$params['joins']=[
			    ['left','gw_order_group AS ord','a.group_id = ord.id'],
			    ['left','gw_users AS usr','ord.user_id = usr.id'],
			];	
		}
		$params['conditions'] = $params['conditions'] ?? '';
		
		//d::dumpas($params);
		if($_GET['pay_interval'] ?? false){
			$params['conditions'] = $params['conditions'] ? '('. $params['conditions'] .') AND ' :'1=1 AND ';
			
			list($date_from,$date_to) = explode(',', $_GET['pay_interval']);
			
			$params['conditions'].=GW_DB::prepare_query(['pay_time >= ? AND pay_time <= ?', $date_from, $date_to." 23:59"]);
		}
		
		if($_GET['pay_test'] ?? false){
			$params['conditions'] = $params['conditions'] ? '('. $params['conditions'] .') AND ' :'1=1 AND ';
			
			$params['conditions'].="pay_test=".(int)$params['pay_test'];
		}
				
		//d::dumpas($params);
		
		if($this->view_name=='email')
			$params['limit']=9999999;
		
	}
	
	function __eventAfterListParams(&$params){
		if(isset($_GET['ord']))
		{
			$params['order'] = $_GET['ord'];
		}		
	}
	
	function overrideFilterUser_title($value, $compare_type)
	{	
		$x=$this->__overrideFilterExObject("GW_User", "ord.`user_id`", ["name","surname",'email'], $value, $compare_type);
		
		return $x;
	}	
		
	
	
	function __eventAfterList(&$list)
	{
		$this->attachFieldOptions($list, 'user_id', 'GW_User');
		
		///$this->attachFieldOptions($list, 'composer_id', 'IPMC_Composer');
		
		
		//$pieces0 = IPMC_Competition_Pieces::singleton();
		if(isset($_GET['flds'])){
			$flds = explode(',',$_GET['flds']);
			
			$this->list_config['display_fields'] = array_fill_keys(array_keys($this->list_config['display_fields']), 0);
			
			foreach($flds as $fld)
				$this->list_config['display_fields'][$fld]=1;
			
			$this->list_config['dl_fields']=$flds;
			
			$this->tpl_vars['dl_fields'] = $flds;
			
			//d::dumpas($this->list_config);
		}
	
		foreach($list as $item)
			$this->initType($item);		
		
		if($this->list_config['display_fields']['contracts'] ?? false){
			$ids = array_keys($list);
			$cond = GW_DB::prepare_query(['obj_type=?', $this->model->table])." AND ".GW_DB::inCondition('obj_id', $ids);
			$counts = GW_Form_Answers::singleton()->countGrouped('obj_id',$cond);
			
			$this->tpl_vars['contract_counts'] = $counts;
		}
	}

	
	function __eventBeforeSave($item)
	{
		
		//d::dumpas($item);
		//$item->admin_id = $this->app->user->id;
	}
		
	
	function initType($item)
	{
		$class = $item->obj_type;
		
		static $cache;
		
		if(!isset($cache[$class])){
			$pages = GW_ADM_Page::singleton()->findALL(['info LIKE "%'.GW_DB::escape($class).'%"']);

			if(count($pages)==1){
				$cache[$class] = $pages[0]->path;
			}
		}
	
		
		$item->modpath = $cache[$class] ?? false;
				
	}
	
	function initObjTypes()
	{
		$tmp = GW::db()->fetch_one_column("SELECT DISTINCT obj_type FROM `{$this->model->table}`");	
		
		
		
		foreach($tmp as $itm)
			$this->options['obj_type'][$itm] = GW::ln('/g/CART_ITM_'.$itm);
		
		
		$this->options['context_obj_type'] = GW::db()->fetch_one_column("SELECT DISTINCT context_obj_type FROM `{$this->model->table}`");
		
	}
	
	function viewForm()
	{
		$vars = parent::viewForm();
		
		$this->initType($vars['item']);
		
		//pridedamas naujas itemsas
		$this->initObjTypes();
		
		
		$this->tpl_vars['extra_fields'] = ['id','insert_time','update_time','obj_type','obj_id','invoice_line2','group_id'];
		
		if($vars['item']->expirable)
			$this->tpl_vars['extra_fields'][] = 'expires';
		

		return $vars;
	}

	
	//seriesAct - supported
	function doMarkAsProcessed($item=false)
	{
		if(!$item)
			$item = $this->getDataObjectById();
		
		$item->set('processed', 1);
		$item->updateChanged();
		
		if(!$this->sys_call)
			$this->jump();			
	}

	function doCaptureObjInvoiceLines()
	{
		$t = new GW_Timer;
		$list = $this->model->findAll("invoice_line2=''",['limit'=>'3000']);
		
		$changed = 0;
		
		foreach($list as $item){
			$item->invoice_line2 = $item->invoice_line;			
			
			if($item->changed_fields){
				$changed++;
				$item->updateChanged();
			}
		}
		
		$cnt = count($list);
		$speed = $t->stop();
		$this->setMessage("Updated {$changed}/{$cnt} speed: $speed");
	}
	
	
	function doMarkAsPayd()
	{
		if(!$this->app->user->isRoot()){
			$this->setError("DEV only");
			$this->app->jump();
		}
		
		$item = $this->getDataObjectById();
		
		
		if(!$item->order->pay_test){
			$this->confirm("pay_test is not 1 so it is real payment, if you proceeed you might cause damage");
		}
		
		$obj = $item->obj;
		if($obj){
			
			$markaspaydresponse = $obj->orderItemPayd($item->unit_price, $item->qty, $item->order, $item);
			
			d::ldump($markaspaydresponse);
		}
		
	}
/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */	
}
