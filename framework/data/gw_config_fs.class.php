<?php

class GW_Config_FS
{
	use Singleton;

	public $dir;
	public $file;
	public $data;

	function __construct($storageid = '')
	{		
		$this->dir = GW::s('DIR/REPOSITORY').'settings/';
		@mkdir($this->dir, 0777);
		
		$this->file = $this->dir.preg_replace('/[^a-z 0-9-_.]/','_', $storageid).'.json';
		
	}

	function loadData()
	{
		$this->data = json_decode(file_get_contents($this->file), true);
	}
	
	function saveData()
	{
		file_put_contents($this->file, json_encode($this->data));
	}
	
	function set($key, $value)
	{
		$this->data[$key] = $value;
		$this->saveData();
	}

	function get($key)
	{
		if(!$this->data)
			$this->loadData();
		
		return $this->data[$key] ?? null;
	}


	function setValues($vals)
	{
		foreach ($vals as $key => $val)
			$this->set($key, $val);
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
