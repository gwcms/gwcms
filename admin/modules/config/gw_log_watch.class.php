<?php



class GW_Log_Watch extends GW_Data_Object
{

	function load($vals=Array())
	{
		$this->filename = $this->getFilename($this->id);
		
		//fixed position
		$this->loadSize();
		//fixed position
		$this->loadData();
		//fixed position
		$this->new_size= $this->size - $this->last_offset;
		//fixed position
		$this->loadListColor();
		
		$this->update_time = date('Y-m-d H:i:s', filemtime($this->filename));
			

		$this->loaded=true;
		
		$this->fireEvent('AFTER_LOAD');	
		
		//required for module
		return true;
	}
	
	static function getFilename($file)
	{
		$file = str_replace(Array('\\','..'),'', $file); //replace bad chars
		$file = GW::$dir['LOGS'].$file;

		if(!file_exists($file))
			return false;
			
		return $file;
	}
	
	static function getData(&$cfg=false)
	{
		$cfg = GW::getInstance('GW_Config');
		$data = json_decode($cfg->get('system/log_watch_config'), true);
		
		return $data;	
	}	

	//load after setting filename
	function loadSize()
	{
		$this->size = filesize($this->filename);
	}
	
	//load after new size
	function loadListColor()
	{
		if($this->new_size)
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
		$buff=GW_Log_Read::offsetRead($this->filename, $this->last_offset);
		
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
	
	
	function findAll($conditions = NULL, $options = Array())
	{
		$files = glob(GW::$dir['LOGS'].'*.log');
		$list = Array();
		

		
		foreach($files as $index => $file)
		{
			$short = str_replace(GW::$dir['LOGS'],'',$file);
			$filemtime = date('Y-m-d H:i:s', filemtime($file));
			
			$item = $this->createNewObject($short);
			$item->load();
			
			$list[$filemtime. ' '.$index] = $item;
		}
		
		ksort($list);
		$list = array_reverse($list);	

		
		return $list;
	}
	
}