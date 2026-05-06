<?php

class Module_OrderLedger extends GW_Common_Module
{
	use Module_Import_Export_Trait;
	
	public $order_id = false;
	public $order = false;
	
	function init()
	{
		$this->model = GW_Order_Payment_Confirmation::singleton();
		
		parent::init();
		
		$this->list_params['paging_enabled'] = 1;
		
		if($this->app->path_arr['1']['path_clean'] == 'payments/ordergroups'){
			$this->order_id = $this->app->path_arr['1']['data_object_id'] ?? false;
		}
		
		if(isset($_GET['order_id']))
			$this->order_id = (int)$_GET['order_id'];
		
		if(isset($_GET['filters']['order_id']))
			$this->order_id = (int)$_GET['filters']['order_id'];
		
		if($this->order_id){
			$this->filters['order_id'] = $this->order_id;
			$_GET['filters']['order_id'] = $this->order_id;
			$this->order = GW_Order_Group::singleton()->find(['id=?', $this->order_id]);
		}
		
		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['order_id'] = 1;
		$this->app->carry_params['filters'] = 1;
		
		$this->item_remove_log = 1;
	}
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields'] = [
			'id' => 'Lof',
			'order_id' => 'Lof',
			'direction' => 'Lof',
			'status' => 'Lof',
			'source' => 'Lof',
			'amount' => 'Lof',
			'currency' => 'Lof',
			'received_at' => 'Lof',
			'bank_account' => 'Lof',
			'reference' => 'Lof',
			'source_log_table' => 'Lof',
			'source_log_id' => 'Lof',
			'created_by' => 'Lof',
			'change_transaction_id' => 'Lof',
			'test' => 'Lof',
			'insert_time' => 'Lof',
			'comment' => 'Lof',
		];
		
		$cfg['filters']['order_id'] = [
			'type'=>'select_ajax',
			'options'=>[],
			'preload'=>1,
			'modpath'=>'payments/ordergroups',
		];
		
		if($this->order_id)
			unset($cfg['fields']['order_id']);
		
		return $cfg;
	}
}
