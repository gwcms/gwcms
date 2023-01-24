<?php

class GW_VATgroups extends GW_i18n_Data_Object
{	
	public $calculate_fields = [
	];

	public $validators = [
	    'title' => ['gw_string', [ 'required'=>1 ]],
	    'key' => ['gw_string', [ 'required'=>1 ]]
	];	
	
	public $default_order = "`priority` ASC";	

	
	function calculateField($name) {
		
		switch ($name)
		{
			case 'x':
				//return json_decode($this->products, true);
			break;
		}
		
		parent::calculateField($name);
	}	
	
	function validate()
	{
		if(!parent::validate())
			return false;		
			
		//$this->set('key', preg_replace('/[^a-zA-Z-_0-9]/','_', strtoupper($this->get('key')) ));
		

		$cond=Array
		(
			'`key`=? AND id!=?',
			$this->get('key'), 
			(int)$this->get('id')
		);

		if($duplicate = $this->find($cond))
			$this->errors['key']='/G/VALIDATION/UNIQUE';

			
		return !(bool)count($this->errors);
	}
	
	function getOptions($lang = 'lt')
	{
		return $this->getAssoc(['id', 'title']);
		//$opts= [];
		
		//foreach($this->findAll(false, ['order'=>"title_$lang ASC"]) as $country)
		//{
		//	$opts[$country->code] = $country->get('title', $lang);
		//}
		
		//return $opts;
	}
}