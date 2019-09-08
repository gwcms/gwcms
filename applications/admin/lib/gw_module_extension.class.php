<?php

class GW_Module_Extension
{
	//parent module
	public $mod;
	
	function __call($name, $arguments) 
	{
		return call_user_func_array([$this->mod,$name], $arguments);
	}
	
	function __get($name) 
	{
		return $this->mod->$name;
	}
	
	function __set($name, $val) 
	{
		$this->mod->$name = $val;
	}	
}
