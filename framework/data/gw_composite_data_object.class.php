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

	var $composite_content_base = Array();
	var $composite_map;

	function isCompositeField($field)
	{
		return isset($this->composite_map[$field]);
	}

	function saveCompositeItems($update_context)
	{
		$saved = 0;
		foreach ($this->composite_content_base as $field => $item) {
			//if it is requested to update only some fields not all
			if (isset($update_context['update_only']))
				if (!isset($update_context['update_only'][$field]))
					continue;

			$item->setOwnerObject($this, $field);
			$item->save();

			$saved = 1;
		}

		if ($saved)
			$this->fireEvent('AFTER_COMPOSITE_ITEM_SAVE');
	}

	function removeCompositeItem($field, $id='*')
	{		
		if(!isset($this->composite_content_base[$field]))
			$this->getComposite($field);
		
		if ($item = $this->composite_content_base[$field]) {

			$item->deleteComposite($id);
			
			//unset($this->composite_content_base[$field]);
		}
	}

	function removeAllCompositeItems()
	{
		$this->loadCompositeItems();

		foreach ($this->composite_content_base as $field => $item)
			$this->removeCompositeItem($field);
	}

	function loadCompositeItem($field)
	{
		if (/* !$this->get($this->primary_fields[0]) || */ isset($this->composite_content_base[$field])) //do not load twice
			return false;
		
		$params = $this->composite_map[$field];
		$classname = $params[0];

		$obj = new $classname;

		if (isset($params[1]))
			$obj->setParams($params[1]);

		$this->composite_content_base[$field] = $obj->getByOwnerObject($this, $field);
	}

	function loadCompositeItems()
	{
		foreach ((Array) $this->composite_map as $field => $params)
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
		$realfieldname = strpos($field, '/')!==false ? explode('/', $field, 2)[0] : $field;
		
		if (!$this->isCompositeField($realfieldname))
			return parent::get($field);
		
		if(strpos($field, '/')!==false)
		{
			list($obj,$key) = explode('/', $field, 2);
			
			if(is_object($this->$obj))
				return $this->$obj->$key;
		}			

		return $this->{isset($this->composite_map[$field][1]['get_cached']) ? 'getCompositeCached' : 'getComposite'}($field);
	}

	function set($field, $value)
	{	
				
		$realfieldname = strpos($field, '/')!==false ? explode('/', $field, 2)[0] : $field;
		
		
		if (!$this->isCompositeField($realfieldname))
			return parent::set($field, $value);

		if(strpos($field, '/')!==false)
		{
	
			
			list($obj,$key) = explode('/', $field, 2);
			
			$obj = $this->$obj;
			//d::dumpas([$obj,$key]);
			
			return $obj->set($key, $value);
		}	

		/*
		if(strpos($field, '/')!==false)
		{
			$keys=explode('/', $field);
			$k1= array_shift($keys);
	
			$this->__objAccessWrite($this->content_base[$k1], $keys, $value);
			$this->changed_fields[$k1] = 1;
			return true;
		}*/	
		
		
		

		$descript = $this->composite_map[$field];
		$classname = $descript[0];


		$item = new $classname();
		$item->setValues($value);

		if (isset($descript[1]))
			$item->setParams($descript[1]);

		$item->setOwnerObject($this, $field);

		$this->composite_content_base[$field] = $item;
	}

	function updateChanged()
	{
		parent::updateChanged();
		
		$this->fireEvent('AFTER_SAVE');
	}	
	
	
	function eventHandler($event, &$context_data = [])
	{
		
		switch ($event) {
			case 'PREPARE_SAVE':
				
				
				if($this->composite_content_base || isset($this->content_base['delete_composite'])){
					$this->changed=1;
				}

				if (isset($this->content_base['delete_composite'])) {
					
					foreach ($this->content_base['delete_composite'] as $field => $ids) {
						foreach($ids as $id)
							$this->removeCompositeItem($field, $id);
					}

					unset($this->content_base['delete_composite']);
					unset($this->changed_fields['delete_composite']);
				}				
				
			break;
			
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
		foreach ($this->composite_content_base as $field => $item) {
			if (!$item->validate())
				$this->errors[$field] = $item->getFirstError();
		}

		parent::validate();

		return $this->errors ? false : true;
	}

	function getImageMinSize($name)
	{
		if (!isset($this->composite_map[$name][1]['dimensions_min']))
			return false;

		return explode('x', $this->composite_map[$name][1]['dimensions_min']);
	}

	function getImageMaxSize($name)
	{
		if (!isset($this->composite_map[$name][1]['dimensions_max']))
			return false;

		return explode('x', $this->composite_map[$name][1]['dimensions_max']);
	}

	function getImageReSize($name)
	{
		if (!isset($this->composite_map[$name][1]['dimensions_resize']))
			return false;

		return explode('x', $this->composite_map[$name][1]['dimensions_resize']);
	}
	/*
	  function __isset($name)
	  {
	  return isset($this->composite_map[$name]) || parent::__isset($name);
	  }
	 */
}
