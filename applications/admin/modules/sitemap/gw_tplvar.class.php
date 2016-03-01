<?php


/**
 * 
 * @author vidmantas
 *	
 */


class GW_TplVar extends GW_Data_Object
{
	var $table = 'gw_template_vars';
	var $default_order = 'id ASC';
	
	var $validators = Array('params'=>'gw_json');
	var $encode_fields=Array('params'=>'json');		
	
	
	function validate()
	{
		if(!parent::validate())
			return false;		
			
		$this->set('name', preg_replace('/[^a-z-_0-9]/','_', strtolower($this->get('name')) ));
			
		$cond=Array
		(
			'template_id=? AND name=? AND id!=?',
			$this->get('template_id'),
			$this->get('title'), 
			(int)$this->get('id')
		);
		
		if($duplicate = $this->find($cond))
			$this->errors['name']='/G/VALIDATION/UNIQUE';
		
			
		return !(bool)count($this->errors);
	}

	
	function eventHandler($event, &$context_data=[])
	{
		switch($event)
		{
			case 'BEFORE_SAVE':				
				if(!is_array($this->params))
					$this->params = json_decode($this->params, true);
			break;
		}	
			
		return parent::eventHandler($event, $context_data);
	}
}
