<?php

class GW_Extension_KeyVal
{
	private $parent;
	public $obj;
	
	function __construct($parent, $name)
	{
		$this->parent = $parent;
		$parent->registerObserver(['extension', $name]);
		
		$this->obj = new GW_Generic_Extended($this->parent->id, $this->parent->table.'_extended');
		
	}
	
	function eventHandler($event, &$context_data = [])
	{			
		//d::ldump($event);
		
		switch ($event) {

			case 'AFTER_INSERT':
				
				
			break;
		
			case 'BEFORE_DELETE':
				$this->obj->delete("1=1");
			break;

			
		}
	}
	
	
	function __call($name, $arguments) 
	{
		return call_user_func_array([$this->obj, $name], $arguments);
	}
	
	function __get($name) 
	{
		return $this->obj->$name;
	}

}
