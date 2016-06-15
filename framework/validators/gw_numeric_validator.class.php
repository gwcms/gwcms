<?php

class GW_Numeric_Validator extends GW_Validator
{

	function init()
	{
		$this->setParam('error_message', '/G/VALIDATION/NUMERIC_FAIL');
	}

	function isValid()
	{
		$value = $this->validation_object;

		$this->reset();

		$value = trim($value);

		if (!$value && !$this->getParam('required'))
			return true;

		if ($value && !is_numeric($value)) {
			$this->setErrorMessage($this->getParam('error_message'));
			return false;
		}

		return true;
	}

	function __countWords($value)
	{
		$words = 0;

		foreach (explode(' ', $value) as $word)
			if (preg_match('/[\w]/', $word))
				$words++;

		return $words;
	}
}
