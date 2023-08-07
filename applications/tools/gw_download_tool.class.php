<?php

class GW_Download_Tool
{
	public $path_arr;
	
	public $admin=false;
	
	public $app;
	
	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
		
	}
	
	
	function init()
	{
		$this->app->initDB();
	}
	
	function process()
	{
		ob_clean();
		
		$params=$_GET;
		$item0 = new GW_File();
		
		$params['id'] = array_shift($this->path_arr);
		
		
		if(!$params['id'])
			die('Bad request.');


		$item=$item0->find(Array('`key`=?',$params['id']));


		if(!$item)
			die('File doesn\'t exist');


		GW_Cache_Control::setExpires('+24 hour');
		//GW_Cache_Control::checkFile($item->getFilename());

		if(isset($params['debug']))
		{
			dump(Array
			(
				'params' => $params,
				'item' => $item,
				'cache_files'=> $item->getCacheFiles()
			));
			exit;
		}	

		GW_File_Helper::output($item->getFilename(), $_GET['view'] ?? false);

	}
}