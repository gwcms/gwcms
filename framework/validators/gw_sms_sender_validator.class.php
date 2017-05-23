<?php

class GW_SMS_Sender_Validator extends GW_Validator
{

	function init()
	{
		$this->setParam('error_invalid', '/G/VALIDATION/INVALID_SMS_SENDER');
	}

	function isValid()
	{
		$value = $this->validation_object;

		$this->reset();

		$value = trim($value);

		if (!$value && !$this->getParam('required'))
			return true;


		if (
		    (is_numeric($value) && strlen($value) > 16) ||
		    (!is_numeric($value) && strlen($value) > 11)
		) {
			$this->setErrorMessage($this->getParam('error_invalid'));
			return false;
		}

		if (!empty($value))
			return true;

		$this->setErrorMessage($this->getParam('error_message'));

		return false;
	}
}
