<?php


class GW_Form_Elements extends GW_i18n_Data_Object
{

	public $default_order = 'priority ASC';	
	public $validators = [
	    'type' => ['gw_string', ['required'=>1]],
	    'fieldname' => ['gw_string', ['required'=>1]],
	];		
	
	public $i18n_fields = [
	    "title"=>1,
	    "placeholder"=>1,
	    "note"=>1,
	    "hidden_note"=>1,
	];	

	
	function getTypes()
	{
		return  $this->getDB()->getColumnOptions($this->table, 'type');
	}
	
	function validate(){
		
		parent::validate();
		
		$cond=Array
		(
			'owner_id=? AND `fieldname`=? AND id!=?',
			$this->get('owner_id'),
			$this->get('fieldname'), 
			(int)$this->get('id')
		);
		
		if($duplicate = $this->find($cond))
			$this->errors['fieldname']='/G/VALIDATION/UNIQUE';
			
		
		
		return !(bool)count($this->errors);
	}
	
	

		
}