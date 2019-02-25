<?php

class gw_admin_form_help{

	static function calcInputNamePattern($input_name_pattern, $type='')
	{
		if(!$input_name_pattern)
			$input_name_pattern="item[%s]";
		
		if(strpos($type, 'multiselect')!==false)
			$input_name_pattern="{$input_name_pattern}[]";

		return $input_name_pattern;
	}
	/*
	static function calcInputId($input_name){
		$input_id=$input_name;
		$input_id=str_replace(["[","]"],'__',$input_id);
		$input_id=str_replace("/",'___',$input_id);
		return $input_id;
	}	
	*/
}
