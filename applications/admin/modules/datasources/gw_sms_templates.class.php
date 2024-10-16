<?php


class GW_SMS_Templates extends GW_i18n_Data_Object
{
	
	public $default_order='id DESC';	
	
	public $validators = [
	    'body'=>['gw_string', ['required'=>1]]
	];
	
	
	public $calculate_fields = [
		'title'=>1
	];


	public $i18n_fields = [
	    "body"=>1,
	];	
	
	//for GW_Mail_helper::__fSubjBody - to interpret smarty code
	public $format_texts =2;
	
		
	function calculateField($name) {
		
		switch($name){
			case 'title':
				return $this->id.'. '.GW_String_Helper::truncate($this->body, 80);
			break;	
		}
		
		
		parent::calculateField($name);
	}
}