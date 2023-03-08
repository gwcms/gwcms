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
			
				$this->parent->ignore_fields['keyval'] = 1;
				$this->parent->calculate_fields['keyval'] =  'extensionget';	
			break;
			
			case 'AFTER_INSERT':
				$this->constructExt();
				
				$this->obj->setOwnerId($this->parent->id);
				
				if($this->cacheNotSaved)
				{
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
		return $this->get($name);
	}
	
	function get($name, $all=false)
	{
		if($this->parent->id)
			return $this->obj->get($name, $all);
	}
	
	function search($phrase)
	{
		return $this->obj->findOwner(GW_DB::prepare_query(['value LIKE ?', '%'.$phrase.'%']));
	}
	function searchKey($phrase)
	{
		return $this->obj->findOwner(GW_DB::prepare_query(['key LIKE ?', '%'.$phrase.'%']));
	}	
	
	function set($name, $value){
		
		if(isset($this->parent->extensions['changetrack'])){

			$old = $this->get($name);
			if(is_array($value))
				$value = json_encode ($value);

			if($old != $value){
				$this->parent->extensions['changetrack']->additional_changes['keyval/'.$name]=['new'=>$value, 'old'=>$old];
			}
		}

		$this->obj->set($name, $value);
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
