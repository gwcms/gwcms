<?php

class GW_Date_Validator extends GW_Validator
{

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


		//Checks if name is made out of +0-9 with possibility to have several names separated by one space.
		if (!preg_match('/^(\d{4}-\d{2}-\d{2})$/', $value))
			return $this->setErrorMessage('/G/VALIDATION/DATE/INVALID_DATE') && false;
	}
}
