<?php

class GW_Pay_Methods extends GW_i18n_Data_Object
{
	//`gateway` ASC,`group` ASC, `country` ASC,

	
	public $ownerkey = 'payments/mergedpaymethods';
	public $extensions = [
	    'changetrack'=>1,
	    //'keyval'=>1
	];				
	public $keyval_use_generic_table = 1;	
	
	public $default_order = "`priority` ASC";
	
	
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