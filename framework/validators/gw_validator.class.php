<?php

class GW_Validator
{
	use Singleton;

	var $error_messages = Array();
	var $validation_object = false;
	var $params = Array
	    (
	    'error_message' => '/G/VALIDATION/REQUIRED'
	);

	function __construct($validation_object=false, $params = Array())
	{
		$this->validation_object = $validation_object;
		$this->setParams($params);

		$this->init();
	}

	function init()
	{
		// can be overriden
	}

	function setParams($params)
	{
		$this->params = array_merge($this->params, $params);
	}

	function setParam($name, $value)
	{
		$this->params[$name] = $value;
	}

	function getParam($name)
	{
		return isset($this->params[$name]) ? $this->params[$name] : false;
	}

	/**
	 * if success return false
	 * if fail return errors
	 * 
	 * @return mixed
	 */
	static function getErrors($validator, $validation_object, $params = Array())
	{
		$class = $validator . '_Validator';
		$vld = new $class($validation_object, $params);

		return $vld->isValid() ? false : $vld->error_messages;
	}

	function getErrorMessages()
	{
		return $this->error_messages;
	}

	function setErrorMessage($message)
	{
		$this->error_messages[] = $message;
		//dump($this->error_messages);

		return false; //used for easy exit & minimizing code
	}

	function reset()
	{
		$this->error_messages = Array();
	}

	function isValid()
	{
		$value = $this->validation_object;

		if (!$value && $this->getParam('required'))
			$this->setErrorMessage($this->getParam('error_message'));


		return count($this->error_messages) == 0;
	}
}
