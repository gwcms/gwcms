<?php


class Module_Delivery extends GW_Module_Config_Common
{	
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
		return parent::viewDefault();
	}

	protected function beforeConfigSave(&$vals)
	{
		$this->fireEvent("BEFORE_SAVE", $vals);
	}

	protected function afterConfigSave(&$vals)
	{
		$this->fireEvent("AFTER_SAVE", $this->model);
	}

	
	function doCalculateDelivery()
	{
		$order = GW_Order_Group::singleton()->find(['id=?', $_GET['order_id']]);
		$f="doCalculateDelivery{$this->algo}";
		$this->$f($order);
		
	}
	
	function doCalculateDeliveryUniversal($order)
	{
			
		$amount_shipping = 0;
		echo json_encode(['amount_shipping'=>$amount_shipping]);
		exit;
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

	
	function doCalculateDeliveryOrderPrint($order)
	{
		echo json_encode(['amount_shipping'=>$order->amount_shipping]);
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
