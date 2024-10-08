<?php

class GW_Module_Extension
{
	//parent module
	public $mod;
	
	function __call($name, $arguments) 
	{
		//if(method_exists($this->mod, $name)){
			return call_user_func_array([$this->mod,$name], $arguments);
		//}else{
		//	$this->mod->setError("Method '$name' called from '".get_class($this)."' not exists in parent class ".get_class($this->mod));
		//}
	}
	

	
	function &__get($name) 
	{
		if(isset($this->mod->$name)){
			
			return $this->mod->$name;
		}else {
			//public $options=[]; pirmas variantas bus
			//public $options; bus antras - not set
			
			//d::ldump("trying access non existing $name this part might have probs");
			//d::dumpas('test');
			$x = $this->mod->$name ?? null;;
			
			//d::ldump($this->mod->$name);
			return $x;
		}
	}
	
	function __set($name, $val) 
	{
		$this->mod->$name = $val;
	}	
	
	function __isset($name) {
		return isset($this->mod->$name) || isset($this->$name);
	}
}
