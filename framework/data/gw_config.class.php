<?php

class GW_Config
{

	use Singleton;

	public $prefix;
	var $table = 'gw_config';
	public $db;
	private $_cache = [];

	function __construct($prefix = '')
	{
		$this->prefix = $prefix;
	}

	function &getDB()
	{
		return GW::$context->vars['db'];
	}

	function set($key, $value)
	{
		$db = & $this->getDB();
		$db->save($this->table, Array('key' => $this->prefix . $key, 'value' => $value));
	}

	function get($key, &$time = 0)
	{
		$db = & $this->getDB();

		$key = addslashes(substr($this->prefix . $key, 0, 100));

		if (isset($this->_cache[$key])) {
			return $this->_cache[$key];
		}

		$rez = $db->fetch_row("SELECT * FROM {$this->table} WHERE `key`='$key'");
		$time = $rez['time'] ?? null;
		return $rez['value'] ?? null;
	}

	function preload($key, &$time = 0)
	{
		$db = & $this->getDB();

		$key = addslashes(substr($this->prefix . $key, 0, 50));
		$rows = $db->fetch_assoc($q = "SELECT `key`,value FROM {$this->table} WHERE `key` LIKE '$key%'");

		$this->_cache = $rows + $this->_cache;

		return $rows;
	}
	
	function exportLoadedValsNoPrefix()
	{
		$dat=[];
		$prefix = preg_quote($this->prefix,'/');
		
		
		foreach($this->_cache as $key => $val)
			$dat[preg_replace("/^$prefix/",'', $key)] = $val;
		
		return $dat;
	}

	function setValues($vals)
	{
		foreach ($vals as $key => $val)
			$this->set($key, $val);
	}

	function getAge($key)
	{
		return time() - strtotime($this->getTime($key));
	}

	function getTime($key)
	{
		$this->get($key, $time);
		return $time;
	}

	function __set($key, $value)
	{
		return $this->set($key, $value);
	}

	function __get($key)
	{
		return $this->get($key);
	}
}
