<?php

class GW_Name_Validator extends GW_Validator
{

	function init()
	{
		$this->setParam('error_min_length', '/G/VALIDATION/NAME/MIN_LENGTH');
		$this->setParam('error_max_length', '/G/VALIDATION/NAME/MAX_LENGTH');
		$this->setParam('error_illegal_name_format', '/G/VALIDATION/NAME/ILLEGAL_NAME_FORMAT');
	}

	function isValid()
	{
		$value = $this->validation_object;

		$this->reset();

		$value = trim($value);

		if (!$value && !$this->getParam('required'))
			return true;

		if ($min_length = $this->getParam('min_length'))
			if (mb_strlen($value) < $min_length)
				return $this->setErrorMessage($this->getParam('error_min_length')) && false;


		if ($max_length = $this->getParam('max_length'))
			if (mb_strlen($value) > $max_length)
				return $this->setErrorMessage($this->getParam('error_max_length')) && false;


		if ($min_words = $this->getParam('min_words'))
			if ($this->__countWords($value) < $min_words)
				return $this->setErrorMessage($this->getParam('error_min_length')) && false;

		//Checks if name is made out of A-Za-zåøaÅØÆ with possibility to have several names separated by one space.
		if (!preg_match('/^([A-Za-zåøæÅØÆ]+)(( ?)([A-Za-zåøæÅØÆ]+))*$/', $value))
			return $this->setErrorMessage($this->getParam('error_illegal_name_format')) && false;

		if (!empty($value))
			return true;

		$this->setErrorMessage($this->getParam('error_message'));

		return false;
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
