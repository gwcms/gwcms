<?php


class GW_SMS_Templates extends GW_Data_Object
{
	
	public $default_order='id DESC';	
	
	public $validators = [
	    'message'=>['gw_string', ['required'=>1]]
	];
	
	
	public $calculate_fields = [
		'title'=>1,
		'body'=>1
	];
	
	
	//for GW_Mail_helper::__fSubjBody - to interpret smarty code
	public $format_texts =2;
	
		
	
	
	
	function calculateField($name) {
		
		switch($name){
			case 'title':
				return $this->id.'. '.GW_String_Helper::truncate($this->message, 80);
			break;	
			case 'body':
				//for GW_Mail_helper::__fSubjBody - to interpret smarty code
				return $this->message;
			break;
		}
		
		
		parent::calculateField($name);
	}
}