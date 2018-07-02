<?php



class GW_Diary_Invest extends GW_Composite_Data_Object
{
	var $table = 'gw_diary_invest';
	
	//var $calculate_fields = Array('child_count'=>1, 'path'=>'getPath', 'title'=>1);
	var $default_order = 'insert_time DESC';		
	

	public $validators = [
	    'object' => ['gw_string', ['required'=>1]]
	];
	
}