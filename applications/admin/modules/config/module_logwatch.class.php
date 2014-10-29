<?php


class Module_LogWatch extends GW_Common_Module
{	
	
	//to remove integer validation
	var $data_object_id_type=0;

	function init()
	{
		parent::init();
		
		$this->model = new GW_Log_Watch;
	}

	
	function viewDefault()
	{
		$this->viewList();
	}

	
	function getFile()
	{
		if(! $file=$this->model->getFilename($fn=$_REQUEST['id']))
			die("File '$fn' Not Exists!");
			
		return $file;
	}

	
	function doGetUpdates()
	{
		$default_lines_count=1000;
		
		$file=$this->getFile();
			
		$timer = new GW_Timer;
			
		if(!$lines = (int)$_REQUEST['lines']) 
			$lines = $default_lines_count;
		
		$data=($offset=(int)$_REQUEST['offset']) ? GW_Log_Read::offsetRead($file, $offset) : GW_Log_Read::LinesRead($file, $lines, $offset);
			
		echo json_encode(Array('data'=>htmlspecialchars($data),'time'=>$timer->stop(5),'offset'=>$offset));
		exit;
	}
	

	function doSet()
	{
		
		$file = $this->getDataObjectById();
		
		if(isset($_REQUEST['expanded']))
			$file->expanded = (int)$_REQUEST['expanded'];
		
		if(isset($_REQUEST['area']))
			$file->area = $_REQUEST['area'];
			
		dump($file->toArray());

		
		$file->saveData();
		
		exit;
		
	}
	
	function viewGetArea()
	{
		$file = $this->getDataObjectById();
		echo $file->area;
		exit;
	}
	
	
	function viewRealTime()
	{
		$id = $_REQUEST['id'];
		
		return Array('id'=>$id);
	}
	function viewIframe()
	{
		$this->viewRealTime();
	}
		
	function viewEntire()
	{

		header("content-type: text/plain");
		
		$file = $this->getDataObjectById();
		echo $file->readFile();
		
		
		exit;
	}
	
	function viewNewLines()
	{
		header("content-type: text/plain");
		
		$file = $this->getDataObjectById();
		
		echo $file->readNewLines();
		
		exit;
	}	
		
}
