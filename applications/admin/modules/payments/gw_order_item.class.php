<?php
class GW_Order_Item extends GW_Composite_Data_Object
{
	
	
	public $calculate_fields = [
	    'obj'=>1,
	    'total'=>1,
	    'expirable'=>1,
	    'expires_secs'=>1,
	    'is_expired'=>1,
	    'title'=>1,
		'type'=>1,
		'invoice_line'=>1,
	    	'door_code'=>1,
	    'coupon_codes'=>1,
	    'vat_title'=>1,
	    'vat_part'=>1
	];
	
	public $composite_map = [
		'order' => ['gw_composite_linked', ['object'=>'GW_Order_Group','relation_field'=>'group_id']],
	];	
	
	
	public $ownerkey = 'payments/orderitems';
	public $extensions = [
	    'keyval'=>1
	];				
	public $keyval_use_generic_table = 1;
	
	//used in ordered items
	public $ignore_fields = [
		'modpath' => 1
	];		
	
		
	static function getVatGroupsPerc()
	{
		static $cache;
		
		if(!$cache)
			$cache = GW_VATgroups::singleton()->getOptionsPercent();
		
		return $cache;
	}
	
	static function getVatGroups()
	{
		static $cache;
		
		if(!$cache)
			$cache = GW_VATgroups::singleton()->getOptions();
		
		return $cache;
	}	

	
	function eventHandler($event, &$context_data = array()) {
		
		switch($event){
			case 'BEFORE_INSERT':
				if(!$this->invoice_line2)
					$this->invoice_line2 = $this->invoice_line;
			break;
			case 'AFTER_SAVE':
			
				if($this->order instanceof GW_Order_Group) {
					$this->order->fireEvent('BEFORE_CHANGES');
					$this->order->updateTotal();
				}
			break;
			

		}
		
		parent::eventHandler($event, $context_data);
	}	
	
	function calculateField($name) {
		
		switch ($name)
		{
			case "obj":
				$class = $this->obj_type;
				
				if(!$class)
					return false;
				
				if($class)				
					return $class::singleton()->createNewObject($this->obj_id, true);
			break;
			case "total":
				return $this->unit_price * $this->qty;
			break;	
			case 'expires_secs':
				return strtotime($this->expires) - time();
			break;

			case 'title':
				if($this->id)
					return $this->type. ' - '.$this->obj->title;
			break;
			case 'type':
				return GW::ln("/g/CART_ITM_{$this->obj_type}");
			break;
			case  'invoice_line':
				return $this->obj->invoice_line ?: $this->obj->title;
			break;
		
			case 'expirable':
				return $this->expires && strpos($this->expires, "0000-00-00")===false;
			break;
			case 'is_expired':
				return  $this->expirable  && $this->expires_secs < 0;
			break;
			case 'door_code';
				return gw_ttlock_codes::singleton()->createNewObject($this->get('keyval/door_code_id'), true)->code;
			break;	
			case 'coupon_codes':
				return explode(',',$this->keyval->coupon_codes);
			break;
			
			case 'vat_title':
				$opt=$this->getVatGroups();
				return $opt[$this->vat_group] ?? ''; 
			break;
			case 'vat_part':
				if(!$this->vat_group)
					return '-';
				
				$percents=$this->getVatGroupsPerc();
				
				//d::dumpas($percents);
				
				if(isset($percents[$this->vat_group]) && $percents[$this->vat_group]);
					return round($this->total - $this->total/((100+$percents[$this->vat_group])/100), 2);
					
				return '-';
				
			break;
		
		}
		
		parent::calculateField($name);
	}	
		
	
}