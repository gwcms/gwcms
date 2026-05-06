<?php

class GW_Order_Payment_Confirmation extends GW_Composite_Data_Object
{
	public $table = 'gw_order_payment_confirmation';
	public $ownerkey = 'payments/orderpaymentconfirmations';
	
	public $composite_map = [
		'order' => ['gw_composite_linked', ['object'=>'GW_Order_Group','relation_field'=>'order_id']],
		'user' => ['gw_composite_linked', ['object'=>'GW_User','relation_field'=>'created_by']],
	];
	
	public $calculate_fields = [
		'title'=>1,
		'signed_amount'=>1,
	];
	
	static function tableExists()
	{
		static $exists;
		
		if($exists !== null)
			return $exists;
		
		$row = GW::db()->fetch_row("SHOW TABLES LIKE 'gw_order_payment_confirmation'", 1, true);
		return $exists = (bool)$row;
	}
	
	function calculateField($name)
	{
		switch($name){
			case 'title':
				return '#'.$this->id.' '.$this->direction.' '.$this->amount.' '.$this->currency.' order #'.$this->order_id;
				
			case 'signed_amount':
				return $this->direction == 'refund' ? -1 * (float)$this->amount : (float)$this->amount;
		}
	}
	
	function eventHandler($event, &$context_data = [])
	{
		switch($event){
			case 'BEFORE_INSERT':
				if(!$this->received_at)
					$this->received_at = date('Y-m-d H:i:s');
				
				if(!$this->unique_key){
					$this->unique_key = implode(':', [
						$this->source ?: 'manual',
						$this->direction ?: 'payment',
						(int)$this->order_id,
						(int)$this->source_log_id,
						md5($this->amount.'|'.$this->received_at.'|'.$this->reference),
					]);
				}
			break;
		}
		
		return parent::eventHandler($event, $context_data);
	}
}
