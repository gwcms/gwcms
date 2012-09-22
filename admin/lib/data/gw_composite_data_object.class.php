<?

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

	function saveCompositeItems()
	{
		//dump($this->composite_content_base);
		
		foreach($this->composite_content_base as $field => $item)
		{
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
	
	function get($field)
	{
		if(!$this->isCompositeField($field))
			return parent::get($field);
			
		$this->loadCompositeItem($field);
			
		return $this->composite_content_base[$field];
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
	
	
	function eventHandler($event)
	{
		switch($event)
		{
			case 'BEFORE_DELETE':
				$this->removeAllCompositeItems();
			break;
			
			case 'AFTER_SAVE':
				$this->saveCompositeItems();
			break;
		}
		
		parent::EventHandler($event);
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
	
}