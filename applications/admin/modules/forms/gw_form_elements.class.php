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
	public $encode_fields = ['linkedfields'=>'json', 'selectcfg'=>'jsono'];
	


	public $composite_map = [
		'optionsgroup' => ['gw_composite_linked', ['object'=>'GW_Classificator_Types','relation_field'=>'options_src']],
		'form' => ['gw_composite_linked', ['object'=>'GW_Forms','relation_field'=>'owner_id']]
	];
	
	
	function getTypes()
	{
		
		d::dumpas('pakeista i varchar(20)');
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