<?php

class GW_Lang_File extends GW_Data_Object
{

	public $calculate_fields = [
	    'newtime'=>1,
	    'newsize'=>1,
	    'newexists'=>1,
	    'xmlmodif'=>1
	];
	
	function load($vals=Array())
	{
		$this->filename = $this->getFilename($this->id);
		
		
		//fixed position
		$this->loadSize();
		//fixed position
		//$this->loadData();
		$this->loadListColor();
		//fixed position
		//$this->new_size= $this->size - $this->last_offset;
		//fixed position
		//$this->loadListColor();
		
		$this->update_time = date('Y-m-d H:i:s', filemtime($this->filename));
			

		$this->loaded=true;
		
		$this->fireEvent('AFTER_LOAD');	
		
		
		
		//required for module
		return true;
	}
	
	static function getFilename($id)
	{
		return GW_Lang::getFilename($id);
	}
	
	

	//load after setting filename
	function loadSize()
	{
		$this->size = filesize($this->filename);
	}
	
	//load after new size
	function loadListColor()
	{
		if($this->tempExists())
			$this->list_color = 'orange';
	}	
	
	
	// size must be loaded first
	// if lastOffset bigger than size, logfile must be cleaned
	// in that case setting last_offset to 0
	function loadData()
	{
		static $data;
		
		if(!$data)
			$data=self::getData();

		if(isset($data[$this->id]))
			$this->setValues((array)$data[$this->id]);			
	}
		
	function saveData()
	{
		$data = (array)self::getData($cfg);		
		
		$data[$this->id] = 
			Array(
				'last_offset'=>$this->last_offset,
				'expanded'=>$this->expanded,
				'area'=>$this->area
			);
		
		
		$cfg->set('system/log_watch_config', json_encode($data));		
	}
	
	function readNewLines()
	{						
		$tmp = $this->last_offset;
		

		
		$buff=GW_Log_Read::offsetRead($this->filename, $tmp);
	
		$this->last_offset = $this->size;
		$this->saveData();
		
		return $buff;
	}
	
	function readFile()
	{
		$fn = $this->getFilename($this->id);
				
		$this->last_offset = $this->size;
		$this->saveData();
		
		return file_get_contents($fn);
	}
	
	function clean()
	{
		$fn = $this->getFilename($this->id);
				
		$this->last_offset = 0;
		$this->saveData();
		
		return file_put_contents($fn, "");
	}

	
	function getTempName()
	{
		@mkdir(GW::s('DIR/TEMP').'lang/');
		
		return GW::s('DIR/TEMP').'lang/'.str_replace('/','__',$this->id).'.xml';
	}

	function tempExists()
	{
		return file_exists($this->getTempName());
	}
	
	function writeTemp($data)
	{
		@mkdir(dirname($this->getTempName()));
		
		file_put_contents($this->getTempName(), $data);
	}
	
	function writeToOriginal($dest=false, $removetemp=true)
	{
		$dest = $dest ?: $this->filename;
		
		file_put_contents($dest, file_get_contents($this->getTempName()));
		
		if($removetemp)
			$this->removeTemp();
	}
	
	function removeTemp()
	{
		unlink($this->getTempName());
	}

	
	
	function findAll($conditions = NULL, $options = Array())
	{

		$search1 = GW::s('DIR/ADMIN/ROOT').'lang/*.lang.xml';
		$search2 = GW::s('DIR/ADMIN/ROOT').'modules/*/lang.xml';
		
		$list1_ = glob($search1);
		$list2_ = glob($search2);
			
		
		$list = array();
		$files = array();
		
		
		
		foreach($list1_ as $file){
			$key = 'G/'.str_replace('.lang.xml','',basename($file));
			$files[$key] = $file;
		}
		foreach($list2_ as $file){
			$key = 'M/'.basename(dirname($file));
			$files[$key] = $file;
		}	
		
		foreach($files as $index => $file)
		{
			$item = $this->createNewObject($index);
			$item->filename =$file;
			$item->load();
			
			$list[] = $item;
		}			
		
		
		


		
		return $list;
	}
	
	function calculateField($name) {
		
		switch($name){
			case 'newexists':
				return $this->tempExists();
			break;
			case 'newsize':
				if($this->newexists)
					return filesize($this->getTempName());
			break;
			case 'newtime':
				if($this->newexists){
					$ts = filemtime($this->getTempName());
					return date('Y-m-d H:i:s', $ts);;
				}
			break;
			case 'xmlmodif':
				return file_get_contents($this->getTempName());
			break;
		}
	}
	
	
	function getDataStruct($orig=false)
	{
		if($orig || !$this->newexists){
			$file = $this->filename;
		}else{
			$file = $this->getTempName();
		}
		
		$data = GW_Lang_XML::parseXML($file);
		
		return $data;		
	}
	
	
	static function recursiveSplit(&$data, $path, $mainln, $destln, &$collect)
	{
		
		
		foreach($data as &$el){			
			if($el['tag']!='I')
				break;
			
			$key = $el['attributes']['ID'];
			
		
			
			if(isset($el['childs'])){
				
				if($el['childs'][0]['tag']=='I'){
					self::recursiveSplit ($el['childs'], $path.'/'.$key,  $mainln, $destln, $collect);
				}else{
					$src =& GW_Lang_XML::structLangNodeSeek($el['childs'], $mainln);
					$dst =& GW_Lang_XML::structLangNodeSeek($el['childs'], $destln, true);
					
					if($src['value']??false && !($dst['value']??false)){
						$dst['value'] = $src['value']; 
						$collect["$path/$key"] =& $dst['value'];	
					}
				}
			}else{
				$val = trim($el['value']);
				$el['value'] = $val;
				
				if((strpos($path,'/MAP')===0 && $key!='title') || strpos($path, '/FIELDS_SHORT')===0)
					continue;
				
				if(substr_count($val, "\n") > 3 || substr_count($val, "\{$") > 2 || substr_count($val, "&lt;") > 2 || substr_count($val, '<br') > 1)
					continue;
				
				if(strip_tags($val)=='')
					continue;;
					
				if(preg_match('/\#[A-F0-9]/i', $val))
					continue;
				
				if(is_numeric($val))
					continue;
							
				$x =&  $el['value'];
				
				$collect["$path/$key"] =& $x;
				
				$el['childs'] = [
				    ['tag'=>$mainln, 'value'=>$val],
				    ['tag'=>$destln, 'value' => &$x],
				];
			}
		}
	}	
	
	
	function getLines($orig=false)
	{
		$data = $this->getDataStruct($orig);
		
		$lns = [];
				
		$collect = [];
		$recursw = function($data, $path) use (&$recursw, &$collect, &$lns) {
			foreach($data as $el){
				$key = $el['attributes']['ID'];
				$fullp = $path.($path?'/':'').$key;
		
			
				if(isset($el['childs'])){
				
					if($el['childs'][0]['tag']!='I'){
						$arr = [];
						foreach($el['childs'] as $lnnode){
							$arr[$lnnode['tag']] = $lnnode['value'];
							$lns[$lnnode['tag']]=1;
						}
						$collect[$fullp] = $arr;
					}else{
						$recursw($el['childs'], $fullp);
					}
				}else{
					$collect[$fullp] = ['ANY'=>$el['value']];
				}
			}
		};
		
		$recursw($data,'');
		
		return ['list'=>$collect, 'lns'=>$lns];
	}
	

	
}