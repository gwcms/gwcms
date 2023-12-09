<?php

class gw_todo_periods extends GW_i18n_Data_Object
{	

	public $ownerkey = 'todo/periods';
	public $extensions = [
	    'changetrack'=>1,
	    //'keyval'=>1
	];				
	public $keyval_use_generic_table = 1;	
	public $ignored_change_track = ['update_time'=>1];
	public $composite_map = [
		'user' => ['gw_composite_linked', ['object'=>'GW_Customer','relation_field'=>'user_id','readonly'=>1]],
	];		

	
	function calculateField($name) {
		
		switch ($name)
		{
			case "x";
				return $this->code;
			break;

		}
		
		parent::calculateField($name);
	}	
	
	function eventHandler($event, &$context_data = []) {
		
		switch($event){
			case 'BEFORE_SAVE':
				$this->remind_date = date('Y-m-d', strtotime($this->to. ' - '.$this->remind_before));
			break;
			
		}
		
		parent::eventHandler($event, $context_data);
	}
	

}