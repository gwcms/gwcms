<?php
class GW_Order_Item extends GW_Composite_Data_Object
{
	
	
	public $calculate_fields = [
	    'obj'=>1,
	    'total'=>1,
	    'order'=>1,
	    'expirable'=>1,
	    'expires_secs'=>1,
	    'is_expired'=>1,
	    'title'=>1,
		'type'=>1,
		'invoice_line'=>1
	];
	
	
	function calculateField($name) {
		
		switch ($name)
		{
			case "order":
				return GW_Order_Group::singleton()->createNewObject($this->group_id, true);
			break;			
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
				return  $this->expires_enabled  && $this->expires_secs < 0;
			break;
		
		}
		
		parent::calculateField($name);
	}	
		
	
}