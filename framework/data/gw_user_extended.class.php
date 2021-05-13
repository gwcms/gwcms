<?php

/**
 * similar class to GW_Config just not preffix but user_id colmn
 * this class can store multiple values per key
 * 
 * 2016 GW CMS
 */
class GW_User_Extended
{

	use Singleton;

	public $user_id;
	public $table = 'gw_user_extended';
	public $db;
	private $_cache = [];

	function __construct($user_id = 0)
	{
		$this->user_id = $user_id;
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
		return $this->getDB()->fetch_result(["SELECT id FROM {$this->table} WHERE `user_id`=? AND `key`=? AND `value`=?", $this->user_id, $key, $value]);
	}

	/**
	 * updates update_time
	 */
	function touch($key, $value)
	{
		$this->getDB()->update($this->table, ['`user_id`=? AND `key`=? AND `value`=?', $this->user_id, $key, $value], ['update_time' => date('Y-m-d H:i:s')]);
	}

	function delete($cond)
	{
		$user_cond = GW_DB::prepare_query(['user_id=?', $this->user_id]);
		$cond = GW_DB::prepare_query($cond);

		return $this->getDB()->delete($this->table, "$user_cond AND $cond");
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
		return $this->getDB()->insert($this->table, [
			'user_id' => $this->user_id,
			'key' => $key,
			'value' => $value,
			'insert_time' => date('Y-m-d H:i:s')
		]);
	}

	function replace($key, $value)
	{
		$db = $this->getDB();

		$vals = ['user_id' => $this->user_id, 'key' => $key, 'value' => $value];
		$id = $db->fetch_result(["SELECT id FROM {$this->table} WHERE `user_id`=? AND `key`=?", $this->user_id, $key]);

		if ($id)
			$db->update($this->table, "id=" . (int) $id, $vals);
		else
			$db->insert($this->table, $vals + ['insert_time' => date('Y-m-d H:i:s')]);
	}

	function get($key, $all = true, $full=false)
	{
		$db = $this->getDB();

		if (isset($this->_cache[$key])) {
			return $this->_cache[$key];
		}

		$rez = $db->{$all ? 'fetch_rows' : 'fetch_row'}(["SELECT * FROM {$this->table} WHERE `user_id`=? AND `key`=?", $this->user_id, $key]);
		$list = [];

		if (!$all)
			return $full ? $rez :  ($rez['value'] ?? false);

		foreach ($rez as $row)
			$list[] = $full ? $row : $row['value'];

		return $list;
	}
	
	function getAll()
	{
		$db = $this->getDB();
		
		$rez = $db->fetch_assoc(["SELECT `key`,`value` FROM {$this->table} WHERE `user_id`=?", $this->user_id]);

		return $rez;
	}

	function __set($name, $value)
	{
		//d::ldump(['set'=>[$name, $value] ]);
		if(!$value){
			$this->delete(GW_DB::prepare_query(["`key`=?",$name]));
		}else{
			$this->replace($name, $value);
		}
	}
	
	function __get($name)
	{
		//d::ldump(['get'=>[$name] ]);
		return $this->get($name, false);
	}
	
	function __isset ($name){
		return true;
		//required by gw_data_object infrastructure
	}
	
	/*

	  function preload($key, &$time = 0) {
	  $db = & $this->getDB();

	  $key = addslashes(substr($this->prefix . $key, 0, 50));
	  $rows = $db->fetch_assoc($q = "SELECT id,value FROM {$this->table} WHERE id LIKE '$key%'");

	  $this->_cache = $rows + $this->_cache;

	  return $rows;
	  }

	  /*
	  function setValues($vals) {
	  foreach ($vals as $key => $val)
	  $this->set($key, $val);
	  }


	  function getAge($key) {
	  return time() - strtotime($this->getTime($key));
	  }

	  function getTime($key) {
	  $this->get($key, $time);
	  return $time;
	  }

	  function __set($key, $value) {
	  return $this->set($key, $value);
	  }

	  function __get($key) {
	  return $this->get($key);
	  }
	 *
	 */
}
