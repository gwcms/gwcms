<?php


class GW_Links
{
	var $owner_obj;
	var $values;
	var $params;
	var $table;
	var $owner_obj_id;
	var $id1="id"; //owner_object_id
	var $id2="id1"; //dest_object_id
	
	
	/**
	 * @return DB
	 */
	function &getDB()
	{
		return GW::$db;
	}

	function getByOwnerObject($obj)
	{
		$this->setOwnerObject($obj);
		return $this;
	}
	
	function setOwnerObject($obj)
	{
		$this->owner_obj = $obj;
		$this->owner_obj_id = $obj->get($obj->primary_fields[0]);
	}
	
	function save()
	{
		$this->updateBinds($this->values);
	}
	
	function setValues($values)
	{
		$this->values = $values;
	}
	
	function setParams($params)
	{
		$this->params = $params;
		
		if(!isset($this->params['table']))
			trigger_error('GW_Links: not specified table param', E_USER_ERROR);
			
		$this->table=$this->params['table'];
		
		if(isset($this->params['fieldnames']))
			list($this->id1,$this->id2)=$this->params['fieldnames'];
	
		
	}
	
	
	function validate()
	{
		return true;
	}
	
	function getBinds()
	{
		$db = $this->getDB();
		$list = $db->fetch_rows(Array("SELECT {$this->id2} FROM $this->table WHERE $this->id1=?",$this->owner_obj_id), false);
		
		foreach($list as $i => $rec)
			$list1[]=$rec[0];
		
		unset($list);
			
		return $list1;
	}
	
	
	function removeBinds($binds)
	{
		if(!count($binds))return;
				
		$db=$this->getDB();
		
		$cond="{$this->id1}=? AND (";
		
		foreach($binds as $i => $id1)
			$cond.="{$this->id2}=? OR ";	
			
		$cond=substr($cond,0,-4).')';
		
		$filter = array_merge((array)$cond, (array)$this->owner_obj_id, $binds);
		
		$db->delete($this->table, $filter);
	}
	
	function addBinds($binds)
	{
		if(!count($binds))return;
		
		$db=$this->getDB();
		
		$list = Array();
		
		foreach($binds as $id1)
			$list[]=Array($this->id1=> $this->owner_obj_id, $this->id2=>$id1);	
		
		$db->multi_insert($this->table, $list);
	}	

	function updateBinds($newbinds)
	{		
		$newbinds=(array)$newbinds;
		$oldbinds=(array)$this->getBinds();
		
		$add=array_diff($newbinds,$oldbinds);
		$remove=array_diff($oldbinds,$newbinds);
		
		$this->removeBinds($remove);
		$this->addBinds($add);		
	}

	function delete()
	{
		$db = $this->getDB();
		$db->delete($this->table, Array($this->id1.'=?', $this->owner_obj_id));
	}
}