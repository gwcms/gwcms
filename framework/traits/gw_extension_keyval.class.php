<?php

class GW_Extension_KeyVal
{
	private $parent;
	public $obj = false;
	public $cacheNotSaved;
	
	function __construct($parent, $name)
	{
		$this->parent = $parent;
		$parent->registerObserver(['extension', $name]);
		
		
		//$this->constructExt();
	}
	
	function constructExt()
	{
		if(!$this->obj){
			$generic = isset($this->parent->keyval_use_generic_table);
			$table = $generic ? $this->parent->table : $this->parent->table.'_extended';

			$this->obj = new GW_Generic_Extended($this->parent->id, $table, $generic);
		}		
	}
	
	function eventHandler($event, &$context_data = [])
	{			
		//d::ldump($event);
		
		switch ($event) {

			case "AFTER_CONSTRUCT":
				$this->constructExt();
			break;
			
			case 'AFTER_INSERT':
				$this->constructExt();
				
				
				if($this->cacheNotSaved)
				{
					$this->obj->setOwnerId($this->parent->id);
					$this->obj->storeAll($this->cacheNotSaved);
					$this->cacheNotSaved = [];	
				}

				
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
		return $this->obj->get($name);
	}
	
	function get($name)
	{
		return $this->__get($name);
	}
	
	function __set($name, $value) 
	{
		if($this->parent->id){			
			$this->obj->setOwnerId($this->parent->id);
			
			return $this->obj->replace($name, $value);
		}else{
			$this->cacheNotSaved[$name] = $value;
		}
	}
	
	function __isset($name)
	{
		return true;
	}

}
