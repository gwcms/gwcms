<?php

class GW_Pay_Methods extends GW_Data_Object
{
	//`gateway` ASC,`group` ASC,

	public $default_order = "`country` ASC,`priority` ASC";
	
	
	public $calculate_fields = [
	    'title_tr'=>1,
	];

	function calculateField($name) {
		
		switch ($name)
		{
			case "title_tr":
				return strpos($this->title,'/')===0 ?  GW::ln($this->title) : $this->title;
			break;
	
		
		}
		
		parent::calculateField($name);
	}	
		
}