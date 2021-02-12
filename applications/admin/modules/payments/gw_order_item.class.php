<?php
class GW_Order_Item extends GW_Composite_Data_Object
{
	
	
	public $calculate_fields = [
	    'obj'=>1,
	    'total'=>1,
	];
	
	
	function calculateField($name) {
		
		switch ($name)
		{
			case "obj":
				$class = $this->obj_type;
				return $class::singleton()->createNewObject($this->obj_id, true);
			break;
			case "total":
				return $this->unit_price * $this->qty;
			break;		
		}
		
		parent::calculateField($name);
	}	
		
	
}