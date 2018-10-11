<?php

class GW_FSFile
{
	public $dir="/";
	public $root_dir;
	public $content_base=[];
	public $is_db_based = false;
	public $loaded = false;
	
	function __construct($vals=[], $load=false)
	{
		$this->root_dir = GW::s('DIR/REPOSITORY');;
		
		if(!is_array($vals)){
			$path = $vals;
			
			if(strpos($path, 'id:')===0)
				$path= substr ($path, 3);
			
			$this->content_base = $this->getByPath(base64_decode($path));
			$this->loaded = true;
		
		}else{
		
			foreach($vals as $key => $val)
			{
				$this->set($key, $val);
			}
		}
	}
	
	function set($key, $val)
	{
		$this->content_base[$key] = $val;
	}
	
	function get($key)
	{
		
		if($key=='id')
		{
			return 'id:'.base64_encode($this->path);
		}
		
		return $this->content_base[$key];
	}
	
	function findAll()
	{
		
		
		$list0 = glob($this->root_dir.$this->dir.'*');
		$list = [];
		
		foreach($list0 as $path){
			$list[] = new GW_FSFile($this->getByPath($path));
		}
		

		
		return $list;
		
	}
	
	function getByPath($path)
	{
		$arr = [
				'path'=> $path, 
				'filename'=>basename($path), 
				'isdir'=> is_dir($path)?1:0,
				'insert_time'=> date('Y-m-d H:i:s', filectime($path)),
				'update_time'=> date('Y-m-d H:i:s', filemtime($path)),
				'size'=> filesize($path),
				'humansize' => GW_File_Helper::cFileSize(filesize($path)),
				'mime'=> Mime_Type_Helper::getByFilename($path)
		];
		
		$types = explode('/', $arr['mime']);
		if(count($types)>1)
		{
			list($arr['type'], $arr['subtype']) = $types;
		}else{
			$arr['type'] = $arr['mime'];
		}
		
		$arr['relpath'] = str_replace($this->root_dir, '', $arr['path']);
		
		return $arr;
	}
	
	

	
	
	public $primary_fields = ['path']; 
	
	function getColumns()
	{
		$list = ['path', 'filename','isdir','insert_time','update_time','size','humansize','type'];
		return array_flip($list);
	}
	
	
	function lastRequestInfo()
	{
		
	}
	
	function __set($key, $val)
	{
		return $this->set($key, $val);
	}
	
	function __get($key)
	{
		return $this->get($key);
	}
	
	function createNewObject($values = array(), $load = false)
	{
		$class = get_class($this);
		$o = new $class($values, $load);
		return $o;
	}	
	
}

