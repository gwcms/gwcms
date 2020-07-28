<?php

class GW_Module_Extension
{
	//parent module
	public $mod;
	
	function __call($name, $arguments) 
	{
		if(method_exists($this->mod, $name)){
			return call_user_func_array([$this->mod,$name], $arguments);
		}else{
			$this->mod->setError("Method '$name' called from '".get_class($this)."' not exists in parent class ".get_class($this->mod));
		}
	}
	

	
	function &__get($name) 
	{
		if(isset($this->mod->$name)){
			
			return $this->mod->$name;
		}else {
			d::ldump('this part might have probs');
			$x = $this->mod->$name;;
			return $x;
		}
	}
	
	function __set($name, $val) 
	{
		$this->mod->$name = $val;
	}	
}
