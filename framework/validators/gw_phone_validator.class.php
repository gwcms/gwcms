<?php

class GW_Phone_Validator extends GW_String_Validator
{

	function init()
	{
		$this->setParam('error_min_length', '/G/VALIDATION/PHONE/MIN_LENGTH');
		$this->setParam('error_max_length', '/G/VALIDATION/PHONE/MAX_LENGTH');
		$this->setParam('error_illegal_phone_format', '/G/VALIDATION/PHONE/ILLEGAL_PHONE_FORMAT');
	}

	function isValid()
	{

		$value = $this->validation_object;

		$this->reset();

		$value = trim($value);
		//dump("VALUE: " . $value . " isset:" . isset($value) . " required:" . $this->getParam('required') . " !value" . !$value);
		if (!$value && !$this->getParam('required')) {
			//dump("VALID");
			return true;
		}

		if ($min_length = $this->getParam('min_length'))
			if (mb_strlen($value) < $min_length)
				return $this->setErrorMessage($this->getParam('error_min_length')) && false;


		if ($max_length = $this->getParam('max_length'))
			if (mb_strlen($value) > $max_length)
				return $this->setErrorMessage($this->getParam('error_max_length')) && false;

		//Checks if name is made out of +0-9 with possibility to have several names separated by one space.
		if (!preg_match('/^([+]?)( ?)([0-9]+)(( ?)([0-9]+))*$/', $value))
			return $this->setErrorMessage($this->getParam('error_illegal_phone_format')) && false;
	}
}
