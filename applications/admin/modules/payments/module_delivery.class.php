<?php


class Module_Delivery extends GW_Common_Module
{	
	public $default_view = 'default';
	
	function init()
	{
		$this->model = $this->config =   new GW_Config($this->module_path[0].'/');
		$this->initLogger();
		
		$this->algo = $this->config->delivery_algo;

		
		parent::init();
	}
	
	function viewDefault()
	{
		$this->tpl_file_name = $this->tpl_dir."config";

		
		
		return ['item'=>$this->model];
	}
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		foreach($vals as $key => $val)
			if(is_array($val))
				$vals[$key] = json_encode($val);
			
			
		$this->fireEvent("BEFORE_SAVE", $vals);
		
		$this->model->setValues($vals);
		
		$this->fireEvent("AFTER_SAVE", $this->model);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->setPlainMessage('/g/SAVE_SUCCESS');
		//$this->__afterSave($vals);
		$this->jump();
	}

	
	function doCalculateDelivery()
	{
		$order = GW_Order_Group::singleton()->find(['id=?', $_GET['order_id']]);
		$f="doCalculateDelivery{$this->algo}";
		$this->$f($order);
		
	}
	
	function doCalculateDeliveryNatos($order)
	{
		$opts_low = self::__getDeliveryOptionsNatos("lo_");
		$opts_std = self::__getDeliveryOptionsNatos("");	
		
		
		$lowdelivery_prods = array_flip(json_decode($opts_low['exceptions']));
		
		
		$islowdelivery = true;
				
		//sumazintos kainos isimtis
		foreach($order->items as $oi){
			if(!$oi->deliverable)
				continue;
			
			if($oi->deliverable && !isset($lowdelivery_prods[$oi->obj_id]))
				$islowdelivery = false;
		}
		
		$opts = $islowdelivery ? $opts_low : $opts_std;	
		
			
		if($order->country == 'LT'){
			$amount_shipping = $opts['lt'];
		}elseif(GW_Country::singleton()->isEuCountry($order->country)){
			$amount_shipping = $opts['eu'];
		}else{
			$amount_shipping = $opts['in'];
		}			
		
		echo json_encode(['amount_shipping'=>$amount_shipping]);
		exit;
	}

	
	function __getDeliveryOptionsNatos($plan)
	{
		$cfg = GW_Config::singleton()->preload("payments/{$plan}delivery_");		
		$opts = [];
		foreach($cfg as $key => $val)
			$opts[str_replace("payments/{$plan}delivery_",'', $key)] = $val;
		
		asort($opts);
		
		return $opts;				
	}




	
}