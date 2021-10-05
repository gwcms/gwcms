<?php


class Module_OrderItems  extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	
	function init()
	{	
		
		
		$this->model = GW_Order_Item::singleton();
		
		parent::init();
		
		$this->list_params['paging_enabled']=false;	
		

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
		
	
		
		
		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['obj_type'] = 1;
		$this->app->carry_params['obj_id'] = 1;	
		$this->app->carry_params['orderflds'] = 1;
		$this->app->carry_params['flds'] = 1;
		$this->app->carry_params['ord'] = 1;
		$this->app->carry_params['noactions'] = 1;
		$this->app->carry_params['pay_interval'] = 1;
		$this->app->carry_params['pay_test'] = 1;
	}
	

	
	
	
	
	function getListConfig()
	{
		
		$cfg = array('fields' => [
			'id' => 'Lof', 
			'obj_type'=> 'Lof',
			'obj_id'=> 'Lof',
			'modpath' => 'L',
			'unit_price' => 'Lof',
			'qty' => 'Lof',
			'insert_time'=>'lof',
			'update_time'=>'lof',	
			'type'=>'lo'
			]
		);
		
		
		if(isset($_GET['orderflds'])){
			$cfg['fields']['user_id'] = "Lof";
			$cfg['fields']['user_title'] = 'Lf';	
			$cfg['fields']['payment_status'] = 'Lof';	
			$cfg['fields']['pay_time'] = 'Lof';	
			
		}
		
		return $cfg;
	}
	
	

	function __eventBeforeListParams(&$params)
	{		
		
		
		
		
		if(isset($_GET['orderflds']))
		{
			
			$order_fields = "aa.user_id, aa.payment_status, aa.pay_time";
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
		
		return $vars;
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
