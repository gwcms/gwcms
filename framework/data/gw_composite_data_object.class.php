<?php

/**
 * 
 * Attachment classes must have
 * setParams, setValues, save, getByOwnerObject, setOwnerObject, validate
 * in table: fields: 'owner' varchar(255) (with index)
 * 
 * @author vidmantas
 *
 */
class GW_Composite_Data_Object Extends GW_Data_Object
{

	var $composite_content_base = Array();
	var $composite_map;
	var $saved_composite_ids = [];

	function isCompositeField($field)
	{
		return isset($this->composite_map[$field]);
	}

	function saveCompositeItems($update_context=[])
	{
		$saved = 0;
		foreach ($this->composite_content_base as $field => $item) {
			
			$opts = $this->composite_map[$field][1] ?? [];
			
			if($opts['readonly'] ?? false)
				continue;

			
			//if it is requested to update only some fields not all
			if (isset($update_context['update_only']))
				if (!isset($update_context['update_only'][$field]))
					continue;

			$item->setOwnerObject($this, $field);
			
			$item->save();

			$saved = 1;
			
			if(isset($item->id))
				$this->saved_composite_ids[$field][] = $item->id;
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

		if(!$classname)
			return false;
		
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

		//$obj =& $this->composite_content_base[$field];
			
		//return $obj ? (method_exists($arrow, 'getValue')? $obj->getValue() : false) : false;
		
		return $this->composite_content_base[$field] ? $this->composite_content_base[$field]->getValue() : false;
	}
	
	function getCompositeObject($field)
	{
		$this->loadCompositeItem($field);

		return $this->composite_content_base[$field] ? $this->composite_content_base[$field] : false;
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
		
		
		if (isset($this->calculate_fields[$field])) {
			$func = $this->calculate_fields[$field];
			$func = $func == 1 ? 'calculateFieldCache' : $func;
			return $this->$func($field);
		}
		
		if(strpos($field, '/')!==false)
		{
			list($obj,$key) = explode('/', $field, 2);
			
			if(is_object($this->$obj))
				return $this->$obj->$key;
		}			
		
		return $this->{isset($this->composite_map[$field][1]['get_cached']) ? 'getCompositeCached' : 'getComposite'}($field);
	}

	protected  $composite_changed_fields=[];
		
	function set($field, $value)
	{	
				
		$realfieldname = strpos($field, '/')!==false ? explode('/', $field, 2)[0] : $field;
		
		
		if (!$this->isCompositeField($realfieldname))
			return parent::set($field, $value);

		if(strpos($field, '/')!==false)
		{
			list($obj,$key) = explode('/', $field, 2);
			
			if(isset($this->composite_map[$obj]))
				$this->composite_changed_fields[$obj]=1;
			
			$obj = $this->$obj;
			
			if($this->carry_before_changes_context){
				$obj->fireEvent("BEFORE_CHANGES", $this->carry_before_changes_context);
				$this->carry_before_changes_context = null;
			}
				
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
		
		if (isset($descript[1]))
			$item->setParams($descript[1]);

		$item->setOwnerObject($this, $field);
		
		$item->setValues($value);
		$this->composite_changed_fields[$field]=1;
		
		$this->composite_content_base[$field] = $item;
	}

	function updateChanged()
	{
		parent::updateChanged();
		
		if($this->composite_changed_fields)
			$this->fireEvent('AFTER_SAVE');
	}	
	
	
	private $carry_before_changes_context = null;
	
	function eventHandler($event, &$context_data = [])
	{
		switch ($event) 
		{
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
			case 'AFTER_CONSTRUCT':
				$this->ignore_fields['delete_composite']=1;
			break;
		
			case 'BEFORE_CHANGES':
				$this->carry_before_changes_context=$context_data;
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
	
	function setValidationParams($composite_name, $params)
	{
		$this->composite_map[$composite_name][1] = $params;
	}	
	
	/*
	  function __isset($name)
	  {
	  return isset($this->composite_map[$name]) || parent::__isset($name);
	  }
	 */
	
	static $linked_cache;
	static $linked_map=[];
	
	function linkedObjMap($field)
	{
		return self::$linked_map[$field];
	}
	
	//reverse to linkedObjMap
	function getRelationFieldByIdfield($idfield)
	{	
		foreach($this->composite_map as $objfield => $entry)
		{			
			if($entry[0]=='gw_composite_linked' && strtolower($entry[1]['relation_field'])==$idfield)
				return $objfield;
		}
	}	
		
	
	static function prepareLinkedObjects($list, $field=false, $opts=[]) 
	{
		if(!$list)
			return [];
		
		$fistel = reset($list);
		
		if(!isset($fistel->composite_map[$field]))
		{
			//trigger_error("Expected composite_map[$field]", E_USER_WARNING);
			return false;
		}
		
		$info = $fistel->composite_map[$field];
		
		$linkfield = $info[1]['relation_field']; 
		$obj_classname = $info[1]['object']; 
		
		self::$linked_map[$linkfield] = $field;
	
		$ids = [];

		foreach($list as $itm)
			if(is_numeric($itm->$linkfield))
				$ids[$itm->$linkfield] = $itm->$linkfield;
				
		if(!$ids)
			return false;
		
		$cond = GW_DB::inCondition('id', $ids);	
		$addlist = $obj_classname::singleton()->findAll($cond, ['key_field'=>'id']);
		
		if(!isset(self::$linked_cache[$obj_classname]))
			self::$linked_cache[$obj_classname] = [];
		
		self::$linked_cache[$obj_classname] += $addlist;
		
		if($opts['return_objects'] ?? false){
			
			$objlist = [];
			foreach($ids as $id)
				if(self::$linked_cache[$obj_classname][$id] ?? false)
					$objlist[$id] = self::$linked_cache[$obj_classname][$id];			
			
			return $objlist;
		}
		
		return $ids;
	}
	
	static function prepareImages($list, $field)
	{
		
		$im0 = GW_image::singleton();
		$owners = [];
		
		foreach($list as $itm)
			$owners[] = $im0->getOwnerFormat($itm, $field);
		
		
		$cond = GW_DB::inConditionStr('owner', $owners);	
		$addlist = GW_Image::singleton()->findAll($cond, ['key_field'=>'owner']);	
		
		if(!isset(self::$linked_cache['GW_Image']))
			self::$linked_cache['GW_Image'] = [];
		
		self::$linked_cache['GW_Image'] += $addlist;		
	}
	
	
	function getRelationField($class)
	{
		$class = strtolower($class);
		
		
		
		foreach($this->composite_map as $entry)
		{			
			if($entry[0]=='gw_composite_linked' && strtolower($entry[1]['object'])==$class)
				return $entry[1]['relation_field'];
		}
	}
	

	function getChildCounts($childClass, $ids, $extracond=false)
	{
		
		$relationfield = $childClass::singleton()->getRelationField(get_class($this));
		$table = $childClass::singleton()->table;
		
		
		$q= " SELECT `$relationfield`, count(*) AS cnt FROM `$table` WHERE ".GW_DB::inCondition($relationfield, $ids)." GROUP BY `$relationfield`";
		return GW::db()->fetch_assoc($q);
	}		
	
}
