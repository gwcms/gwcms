<?php

class GW_Config
{
	use Singleton;
	
	public $prefix;
	var $table='gw_config';
	public $db;
	private $_cache=[];

	function __construct($prefix='')
	{
		$this->prefix=$prefix;
	}

	function &getDB()
	{
		return GW::$context->vars['db'];
	}

	function set($key,$value)
	{
		$db =& $this->getDB();
		$db->save($this->table,Array('id'=>$this->prefix.$key,'value'=>$value));
	}

	function get($key,&$time=0)
	{
		$db =& $this->getDB();

		$key=addslashes(substr($this->prefix.$key,0,50));
		
		if(isset($this->_cache[$key])){
			return $this->_cache[$key];
		}
		
		$rez = $db->fetch_row("SELECT * FROM {$this->table} WHERE id='$key'");
		$time = $rez['time'];
		return $rez['value'];
	}

	function preload($key,&$time=0)
	{
		$db =& $this->getDB();

		$key=addslashes(substr($this->prefix.$key,0,50));
		$rows = $db->fetch_assoc($q="SELECT id,value FROM {$this->table} WHERE id LIKE '$key%'");
	
		$this->_cache=$rows+$this->_cache;
		
		return $rows;
	}	
	
	function setValues($vals)
	{
		foreach($vals as $key => $val)
			$this->set($key, $val);
	}	
	
	function getAge($key)
	{
		return time()-strtotime($this->getTime($key));
	}
	
	function getTime($key)
	{
		$this->get($key,$time);
		return $time;
	}

	function __set($key,$value)
	{
		return $this->set($key,$value);
	}
	
	function __get($key)
	{
		return $this->get($key);
	}
	
    
}