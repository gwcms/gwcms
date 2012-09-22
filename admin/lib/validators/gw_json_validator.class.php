<?php

/**
 * 
 * Validates json text
 * @author wdm
 *
 */

class GW_Json_Validator extends GW_Validator 
{	
	
	function init()
	{
		$this->setParam('error_invalid', '/VALIDATION/INVALID_JSON');
	}
	
	function isValid()
	{
		$value = $this->validation_object;	
			
		if(is_string($value) && !json_decode($value))
			return $this->setErrorMessage($this->getParam('error_invalid'));
			
		return true;
	}
}

?>