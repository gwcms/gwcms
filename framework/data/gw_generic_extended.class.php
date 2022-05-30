<?php

/**
 * similar class to GW_Config just not preffix but owner_id colmn
 * this class can store multiple values per key
 * todo create generic class
 */

class GW_Generic_Extended 
{

	use Singleton;

	public $owner_id;
	public $table = false;
	public $own_table = false;
	public $db;
	private $_cache = [];
	public $prefix;

	function __construct($owner_id = 0, $table=false, $generic=false)
	{
		$this->table = $table;
		
		if(!$this->table)
			$this->table = strtolower(get_class($this));		
		
		if($generic){
			$this->own_table = $this->table;
			$this->table = strtolower(get_class($this));
		}	
		
		$this->owner_id = $owner_id;
	}
	
	function setOwnerId($id)
	{
		$this->owner_id = $id;
	}

	/**
	 * 
	 * @return GW_DB
	 */
	function &getDB()
	{
		return GW::$context->vars['db'];
	}

	/**
	 * returns 1 if inserted
	 * otherwise null
	 */
	function insertIfNotExists($key, $value)
	{
		if (!$this->exists($key, $value))
			return $this->insert($key, $value);
	}

	function exists($key, $value)
	{
		$own_tbl_cond = $this->own_table ? "own_table = '".GW_DB::escape($this->own_table)."' AND " : '';
		return $this->getDB()->fetch_result(["SELECT id FROM {$this->table} WHERE $own_tbl_cond `owner_id`=? AND `key`=? AND `value`=?", $this->owner_id, $key, $value]);
	}

	/**
	 * updates update_time
	 */
	function touch($key, $value)
	{
		$this->getDB()->update($this->table, ['`owner_id`=? AND `key`=? AND `value`=?', $this->owner_id, $key, $value], ['update_time' => date('Y-m-d H:i:s')]);
	}

	function delete($cond)
	{
		
		$own_id_cond = GW_DB::prepare_query(['owner_id=?', $this->owner_id]);
		$own_tbl_cond = $this->own_table ? "own_table = '".GW_DB::escape($this->own_table)."' AND " : '';
			
		$cond = GW_DB::prepare_query($cond);

		return $this->getDB()->delete($this->table, "$own_tbl_cond $own_id_cond AND $cond");
	}

	function deleteOld($key, $how_old = '-1 year')
	{
		$delete_older_than = date('Y-m-d H:i:s', strtotime($how_old));

		$this->delete(["`key`=? AND update_time < ?", $key, $delete_older_than]);
	}

	function deleteKeyVal($key, $val)
	{
		$this->delete(["`key`=? AND `value` = ?", $key, $val]);
	}

	function insert($key, $value)
	{
		$vals = [
			'owner_id' => $this->owner_id,
			'key' => $key,
			'value' => $value,
			'insert_time' => date('Y-m-d H:i:s')
		];
		
		if($this->own_table)
			$vals['own_table'] = $this->own_table;
		
		return $this->getDB()->insert($this->table, $vals);
	}

	function replace($key, $value)
	{
		$db = $this->getDB();
		
		$own_tbl_cond = $this->own_table ? "own_table = '".GW_DB::escape($this->own_table)."' AND " : '';
		

		$key = $this->prefix . $key;
		
		$vals = ['owner_id' => $this->owner_id, 'key' => $key, 'value' => $value];
		if($this->own_table)
			$vals['own_table'] = $this->own_table;		
		
		$id = $db->fetch_result(["SELECT id FROM {$this->table} WHERE $own_tbl_cond `owner_id`=? AND `key`=?", $this->owner_id, $key]);

		if ($id)
			$db->update($this->table, "id=" . (int) $id, $vals);
		else
			$db->insert($this->table, $vals + ['insert_time' => date('Y-m-d H:i:s')]);
	}

	function get($key, $all = false)
	{
		$db = $this->getDB();
		
		$key = $this->prefix . $key;

		if (isset($this->_cache[$key])) {
			return $this->_cache[$key];
		}

		$rez = $db->{$all ? 'fetch_rows' : 'fetch_row'}(["SELECT * FROM {$this->table} WHERE `owner_id`=? AND `key` LIKE ?", $this->owner_id, $key]);
		$list = [];
		
		if (!$all)
			return $rez['value'] ?? false;

		foreach ($rez as $row)
			$list[$row['key']] = $row['value'];


		return $list;
	}
	
	function getAll($extra_cond=false)
	{
		$db = $this->getDB();
		
		$own_tbl_cond = $this->own_table ? "own_table = '".GW_DB::escape($this->own_table)."' AND " : '';
		
		if($extra_cond)
			$extra_cond = " AND $extra_cond";

		$rez = $db->fetch_assoc(["SELECT `key`,`value` FROM {$this->table} WHERE $own_tbl_cond `owner_id`=? $extra_cond", $this->owner_id]);

		return $rez;
	}
	
	function findOwner($cond)
	{
		$db = $this->getDB();
		
		$own_tbl_cond = $this->own_table ? "own_table = '".GW_DB::escape($this->own_table)."' AND " : '';
		
		$cond = GW_DB::prepare_query($cond);
		
		$rez = $db->fetch_one_column("SELECT owner_id FROM {$this->table} WHERE $own_tbl_cond $cond");

		return $rez;
	}	

	function storeAll($list)
	{
		foreach($list as $key => $value)
			$this->replace($key, $value);
	}
	
	function preload($key, &$time = 0)
	{
		$db = & $this->getDB();
		
		$key = $this->prefix . $key;
		
		$rows = $this->getAll("`key` LIKE '$key%'");

		$this->_cache = $rows + $this->_cache;

		return $rows;
	}
		
	
	
	function __get($name) {
		return $this->get($name, false);
	}
	
	function __set($name, $value) {
		
		return $this->replace($name, $value);
	}	
	function set($name, $value) {
		
		return $this->replace($name, $value);
	}

	function __isset($name) {
		return true; //kad veiktu GW_Data_object->__objAccessRead
	}
	

}
