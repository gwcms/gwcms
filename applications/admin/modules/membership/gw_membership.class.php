<?php


class GW_Membership extends GW_Composite_Data_Object
{
	
	public $composite_map = [
		'user' => ['gw_composite_linked', ['object'=>'GW_Customer','relation_field'=>'user_id']],
	];		
	
	public $calculate_fields = [
		'title'=>1,
		'context_short'=>1,
		'invoice_line'=>1,
	];	
	
	
	function isValid($time=false)
	{
		$time = $time ?: date('Y-m-d H:i:s');
		return $this->validfrom < $time && $time < $this->expires;
	}

	
	function orderItemPayd($amount, $qty, $order)
	{
		$this->fireEvent('BEFORE_CHANGES');
		$this->pay_id = $order->id;
		$this->payd_amount += $amount;
		$this->active = 1;
		//$this->status = 80;
		
		$this->user->setLicId();
		
		$this->updateChanged();			
		
	}
	
	function calculateField($field)
	{
		switch ($field){
			case "use_lang":
				return $this->user->$field;
				

			case 'context_short':
				return $this->user->title;
			break;
			case 'invoice_line':
				return $this->context_short.' ('.$this->title.')';
			break;		
			case 'title':
			
				return date('Y-m-d', strtotime($this->validfrom)). ' — '. date('Y-m-d', strtotime($this->expires));
			break;		
					

		}
	}	
}