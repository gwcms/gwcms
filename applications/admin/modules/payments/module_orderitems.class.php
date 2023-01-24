<?php


class Module_OrderItems  extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	
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
			
		

		$this->cartgroup_id = $this->app->path_arr['1']['data_object_id'] ?? false;
		$this->cartgroup = GW_Order_Group::singleton()->find(['id=?', $this->cartgroup_id]);
		
		if($this->cartgroup)
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
		

	}
	

	
	
	
	
	function getListConfig()
	{
		
		$cfg = array('fields' => [
			'id' => 'Lof', 
			'type'=>'Lo',
			'invoice_line'=>'L',
			'obj_type'=> 'lof',
			'obj_id'=> 'lof',
			'modpath' => 'lof',
			'unit_price' => 'Lof',
			'qty' => 'Lof',
			'total'=>'L',
			'vat_group'=>'Lof',
			'insert_time'=>'lof',
			'update_time'=>'lof',	
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
		
		
		$cfg['filters']['vat_group'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'payments/vatgroups'];
		
		
		if($this->feat('discountcode')){
			$cfg['fields']['coupon_codes'] = 'L';
		}
		
		return $cfg;
	}
	
	

	function __eventBeforeListParams(&$params)
	{		

		if(!$this->cartgroup_id)
		{
			$order_fields = "aa.user_id, aa.payment_status, aa.pay_time, aa.pay_test";
			$params['select']='a.*, '.$order_fields;
			$params['joins']=[
			    ['left','gw_order_group AS aa','a.group_id = aa.id'],
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
		$x=$this->__overrideFilterExObject("GW_User", "user_id", ["name","surname",'email'], $value, $compare_type);
		
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
	
	function viewForm()
	{
		$vars = parent::viewForm();
		
		$this->initType($vars['item']);
		
		//pridedamas naujas itemsas
		
		if(isset($_GET['shift_key'])){
			$this->options['obj_type'] = GW::db()->fetch_one_column("SELECT DISTINCT obj_type FROM `{$this->model->table}`");
			$this->options['context_obj_type'] = GW::db()->fetch_one_column("SELECT DISTINCT obj_type FROM `{$this->model->table}`");
		}
		

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
