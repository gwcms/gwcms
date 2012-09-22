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
	
	
	function validate()
	{
		if(!parent::validate())
			return false;		
			
		$this->set('title', preg_replace('/[^a-z-_0-9]/','_', strtolower($this->get('title')) ));
			
		$cond=Array
		(
			'template_id=? AND title=? AND id!=?',
			$this->get('template_id'),
			$this->get('title'), 
			(int)$this->get('id')
		);
		
		if($duplicate = $this->find($cond))
			$this->errors['title']='/VALIDATION/UNIQUE';
		
			
		return !(bool)count($this->errors);
	}
	

	
}
