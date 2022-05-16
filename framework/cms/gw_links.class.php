<?php

class GW_Links implements GW_Composite_Slave
{

	public $owner_obj;
	public $owner_type=false;
	public $values;
	public $params;
	public $table;
	public $owner_obj_id;
	public $id1 = "id"; //owner_object_id
	public $id2 = "id1"; //dest_object_id
	public $idxfield=false;

	/**
	 * @return DB
	 */

	function &getDB()
	{
		return GW::$context->vars['db'];
	}

	public function getByOwnerObject($master, $fieldname)
	{
		$this->setOwnerObject($master, $fieldname);
		return $this;
	}

	public function setOwnerObject($master, $fieldname)
	{
		
		$this->owner_obj = $master;
		$this->owner_obj_id = $master->get($master->primary_fields[0]);
		
		
		if($this->params['table'] == 'gw_generic_binds')
			$this->owner_type = $master->table;
		
		//d::ldump($master);
	}

	public function save()
	{
		
		if (!is_null($this->values))
			$this->updateBinds($this->values);
	}

	public function setValues($values)
	{
		$this->values = $values;
	}

	public function setParams($params)
	{
		$this->params = $params;

		if (!isset($this->params['table'])){
			$this->params['table'] = 'gw_generic_binds';

			
			
			
			//trigger_error('GW_Links: not specified table param', E_USER_ERROR);
		}

		$this->table = $this->params['table'];

		if (isset($this->params['fieldnames']))
			list($this->id1, $this->id2) = $this->params['fieldnames'];
		
		if(isset($this->params['idxfield']))
			$this->idxfield = true;
	}

	public function deleteComposite($id='*')
	{
		$db = $this->getDB();
		$db->delete($this->table, Array($this->id1 . '=?', $this->owner_obj_id));
	}

	public function getValue()
	{
		return $this->getBinds();
	}

	public function validate()
	{
		return true;
	}

	private function getBinds()
	{
		$db = $this->getDB();
		
		$ord = $this->idxfield ? " ORDER BY idx ASC":'';
		
		$cond = GW_DB::prepare_query(["$this->id1=?", $this->owner_obj_id]);
		
		if($this->owner_type){
			$cond.=" AND ".GW_DB::prepare_query(['owner=?',$this->owner_type]);
		}
		
		$list = $db->fetch_rows("SELECT {$this->id2} FROM $this->table WHERE ".$cond.$ord, false);

		$list1 = [];

		foreach ($list as $i => $rec)
			$list1[] = $rec[0];

		unset($list);

		return $list1;
	}

	private function removeBinds($binds)
	{
		if (!count($binds))
			return;

		$db = $this->getDB();

		$cond = "{$this->id1}=?";
		
		if($this->owner_type)
			$cond.=" AND ".GW_DB::prepare_query (['owner=?', $this->owner_type]);
		
		$cond.="  AND (";

		foreach ($binds as $i => $id1)
			$cond.="{$this->id2}=? OR ";

		$cond = substr($cond, 0, -4) . ')';
		
		$filter = array_merge((array) $cond, (array) $this->owner_obj_id, $binds);

		$db->delete($this->table, $filter);
	}

	private function addBinds($binds)
	{
		if (!count($binds))
			return;

		$db = $this->getDB();

		$list = Array();
		
		if($this->owner_type)
			$db->testExistEnumOption($this->table, 'owner', $this->owner_type);

		foreach ($binds as $idx => $id1){
			$vals = Array($this->id1 => $this->owner_obj_id, $this->id2 => $id1);
			
			if($this->owner_type)
				$vals['owner'] = $this->owner_type;
			
			if($this->idxfield)
				$vals['idx'] = $idx;
				
			$list[] = $vals;
		}
		
		$db->multi_insert($this->table, $list);
	}

	private function updateBinds($newbinds)
	{
		if($this->idxfield){
			$this->removeBinds($this->getBinds());
			$this->addBinds($newbinds);
		}else{
			$newbinds = (array) $newbinds;
			$oldbinds = (array) $this->getBinds();

			$add = array_diff($newbinds, $oldbinds);
			$remove = array_diff($oldbinds, $newbinds);
			
			//d::dumpas([$add,$remove]);

			$this->removeBinds($remove);
			$this->addBinds($add);
			
			
			
		}
	}
}
