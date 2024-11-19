<?php

class GW_FSFile extends GW_Data_Object
{

	
	public $table = "reposfilelist";
	public $root_dir;
	public $base_dir;
	public $inherit_props=['root_dir','base_dir'];
	public $default_order = 'isdir DESC, filename ASC';		
	


	function filterFilename($filename) 
	{
		// sanitize filename
		$filename = preg_replace('~[<>:"/\\|?*]|[\x00-\x1F]|[\x7F\xA0\xAD]|[#\[\]@!$&\'()+,;=]|[{}^\~`]~x', '-', $filename);
		// avoids ".", ".." or ".hiddenFiles"
		$filename = ltrim($filename, '.-');
		// optional beautification

		// maximise filename length to 255 bytes http://serverfault.com/a/9548/44086
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
		return $filename;
	}

	function validate()
	{
		if(isset($this->changed_fields['filename']))
		{
			if($this->filterFilename($this->filename) != $this->filename)
				$this->errors['filename'] = "/G/VALIDATION/INVALID_FILENAME";
			
			
			$new = dirname($this->path).'/'.$this->filename;
			if(file_exists($new) || is_dir($new))
				$this->errors['filename'] = "/G/VALIDATION/FILE_EXISTS";
		}
		
		return count($this->errors)==0;
	}
	
	
	function loadList()
	{		
		$list0 = glob($this->root_dir.$this->dir.'*');
		$list = [];
				
		foreach($list0 as $path){
			$list[] = $this->getByPath($path);
		}
		
		return $list;
	}
	
	
	
	public function getByPath($path)
	{
		$arr = [
				'path'=> $path, 
				'filename'=>basename($path), 
				'isdir'=> is_dir($path)?1:0,
				'insert_time'=> date('Y-m-d H:i:s', filectime($path)),
				'update_time'=> date('Y-m-d H:i:s', filemtime($path)),
				'size'=> filesize($path),
				'extension'=> pathinfo($path, PATHINFO_EXTENSION),
				//'humansize' => GW_File_Helper::cFileSize(filesize($path)),
				'mime'=> Mime_Type_Helper::getByFilename($path)
		];
		
		$types = explode('/', $arr['mime']);
		if(count($types)>1)
		{
			list($arr['type'], $arr['subtype']) = $types;
		}else{
			$arr['type'] = $arr['mime'];
		}
		
		//d::dumpas($this->root_dir);
		
		$arr['relpath'] = str_replace($this->base_dir, '', $arr['path']);
		$arr['id'] = $this->getIDByPath($arr['path']);
		
		unset($arr['mime']);
		unset($arr['path']);
		//unset
		
		return $arr;
	}	
	
	function getIDByPath($path)
	{
		return str_replace('=','_',base64_encode($path));		
	}
	
	function getPathById($id)
	{
		return  base64_decode(str_replace('_','=',$id));;
	}
	
	
	function loadVals($fields="*") 
	{
		return $this->getByPath($this->getPathById($this->id));
	}
	
	
	function createTempTable()
	{
		
		$this->getDB()->query("DROP TABLE IF EXISTS `reposfilelist`;");
		
		$sql="
		CREATE TEMPORARY TABLE `reposfilelist` (
		`id` varchar(500) NOT NULL,
		`relpath` varchar(500) NOT NULL,
		`filename` varchar(200) NOT NULL,
		`size` bigint(20) NOT NULL,
		`extension` varchar(20) NOT NULL,		
		`isdir` tinyint(4) NOT NULL,
		`type` varchar(50) NOT NULL,
		`subtype` varchar(25) NOT NULL,
		`insert_time` datetime NOT NULL,
		`update_time` datetime NOT NULL
	      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		
		//egzistuos tik sesijos metu
		//$this->getDB()->query("DROP TABLE IF EXISTS `reposfilelist`;");
		$this->getDB()->query($sql);
	}
	
	function getColumns($type="all")
	{
		$list = ['relpath', 'filename','isdir','insert_time','update_time','size','type','extension'];
		return array_flip($list);
	}	
		
	
	
	function eventHandler($event, &$context_data = array()) {
		
		switch($event){
			case "BEFORE_LIST":
				$this->createTempTable();
				$rows = $this->loadList();
				
				
				$this->getDB()->multi_insert($this->table, $rows);
			break;
		
			case "BEFORE_SAVE":
				if(isset($this->changed_fields['filename']))
				{
					rename($this->path, dirname($this->path).'/'.$this->filename);
					//d::dumpas($this->changed_fields);
				}				
			break;
		}
	
		parent::eventHandler($event, $context_data);
	}
	
	
	public $calculate_fields = [
		'path'=>1,
		'humansize'=>1,
		'extension'=>1,
		'extension_lc'=>1,
		'subfilescount'=>1,
		'url'=>1,
		'files'=>1,
		'resize_url'=>1,
		'timestamp'=>1
	];



	function calculateField($key)
	{
		switch ($key) {
			case 'humansize':
				return GW_File_Helper::cFileSize($this->size);
			break;
			case 'path':
				return $this->getPathById($this->id);
			break;
		
			case 'extension':
				return pathinfo($this->filename, PATHINFO_EXTENSION);
			break;
			case 'extension_lc':
				return strtolower($this->extension);
			break;
		
			case 'subfilescount':
				if($this->isdir)
					return count(scandir($this->root_dir.$this->filename))-2;
			break;
			
			case 'url':
				$dir=GW::s("SITE_URL").str_replace(GW::s('DIR/REPOSITORY'),'repository/',$this->root_dir);
				return $dir.rawurlencode($this->filename);
			break;

			case 'files':
				return glob($this->path.'/*');
			break;
		
			case 'resize_url':
				//size=widthxheight&method=crop
				return "/tools/img_resize?file=".urlencode($this->relpath)."&dirid=repository";
			break;
		
			case 'timestamp':
				return filemtime($this->path);
			break;
			//case 'ext':
			//	return new IPMC_Competition_Extended($this->id);
		}	
	}
	
	function getContents()
	{
		return file_get_contents($this->path);
	}	
	
	
	
	function updateChanged()
	{
		if(isset($this->changed_fields['filename']))
		{
			$path = $this->getPathById($this->id);
			rename($path, $newpath = dirname($path).'/'.$this->filename);
			
			$this->id = $this->getIDByPath($newpath);
			//d::dumpas($this->changed_fields);
		}		
	}


	function delete()
	{
		if($this->isdir==0){
			
			unlink($this->path);
			
			return !file_exists($this->path);
		}else{
			if($this->subfilescount==0){
				rmdir($this->path);
				
				return !is_dir($this->path);
			}else{
				return false;
			}
		}
	}
	


	
}

