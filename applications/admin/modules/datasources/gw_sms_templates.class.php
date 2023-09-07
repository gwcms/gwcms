<?php


class GW_SMS_Templates extends GW_Data_Object
{
	
	public $default_order='id DESC';	
	
	public $validators = [
	    'message'=>['gw_string', ['required'=>1]]
	];
	
	
	public $calculate_fields = [
		'title'=>1,
		'doc_forms'=>1,
		'doc_ext_fields'=>1
	];
	
	
	
	function calculateField($name) {
		
		switch($name){
			case 'title':
				return $this->id.'. '.GW_String_Helper::truncate($this->message, 80);
			break;		
		}
		
		
		parent::calculateField($name);
	}
}