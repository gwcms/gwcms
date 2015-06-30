<?php

/**
 * 
 * Attachment classes must have
 * setParams, setValues, save, getByOwnerObject, setOwnerObject, validate
 * in table: fields: 'owner' varchar(255) (with index)
 * 
 * @author vidmantas
 *
 *
 */



class GW_Composite_Data_Object Extends GW_Data_Object
{
	var $composite_content_base=Array();
	var $composite_map;
	
	function isCompositeField($field)
	{
		return isset($this->composite_map[$field]);
	}

	function saveCompositeItems($update_context)
	{		
		foreach($this->composite_content_base as $field => $item)
		{
			//if it is requested to update only some fields not all
			if(isset($update_context['update_only']))
				if(!isset($update_context['update_only'][$field]))
					continue;

			$item->setOwnerObject($this, $field);
			$item->save();
		}
	}
	
	function removeCompositeItem($field)
	{
		if($item = $this->composite_content_base[$field])
			$item->delete();
	}	

	function removeAllCompositeItems()
	{
		$this->loadCompositeItems();
		
		foreach($this->composite_content_base as $field => $item)
			if($item)
				$item->delete();
	}
	
	function loadCompositeItem($field)
	{	
		if(/*!$this->get($this->primary_fields[0]) ||*/ isset($this->composite_content_base[$field])) //do not load twice
			return false;
		
		$params = $this->composite_map[$field];
		
		list($classname, $ci_params) = $params;
		$obj = new $classname;
		
		$obj->setParams($ci_params);
		
		$this->composite_content_base[$field]=$obj->getByOwnerObject($this, $field);	
	}
	
	function loadCompositeItems()
	{
		foreach((Array)$this->composite_map as $field => $params)
			$this->loadCompositeItem($field);
	}
	
	function getComposite($field)
	{
		$this->loadCompositeItem($field);
					
		return $this->composite_content_base[$field] ? $this->composite_content_base[$field]->getValue() : false;
	}
	
	function getCompositeCached($field)
	{
		return $this->getCached($field, 'getComposite');
	}
	
	function get($field)
	{
		if(!$this->isCompositeField($field))
			return parent::get($field);
			
		return $this->{isset($this->composite_map[$field][1]['get_cached']) ? 'getCompositeCached' : 'getComposite'}($field);
	}
	
	function set($field, $value)
	{
		if(!$this->isCompositeField($field))
			return parent::set($field, $value);
			
		
		list($classname, $params) = $this->composite_map[$field];

		$item = new $classname();
		$item->setValues($value);
		$item->setParams($params);
		$item->setOwnerObject($this, $field);
			
		$this->composite_content_base[$field] = $item;
	}
	
	
	function eventHandler($event, $context_data=[])
	{
		switch($event)
		{
			case 'BEFORE_DELETE':
				$this->removeAllCompositeItems();
			break;
			
			case 'AFTER_SAVE':				
				$this->saveCompositeItems($context_data);
			break;
		}
		
		parent::EventHandler($event, $context_data);
	}
	
	function validate()
	{
		foreach($this->composite_content_base as $field => $item)
		{
			if(! $item->validate())
				$this->errors[$field] = $item->getFirstError();
		}
		
		parent::validate();
		
		return $this->errors ? false : true;		
	}
	
	/*
	function __isset($name) 
	{
		return isset($this->composite_map[$name]) || parent::__isset($name);
	}	
	*/
}