<?php

class GW_Img_Tool
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
		$params=$_GET;
		$item0 = new GW_Image();
		
		$params['id'] = array_shift($this->path_arr);

		$condition=$this->admin?'id=?':'`key`=?';

		if(isset($_REQUEST['f']) && $_REQUEST['f'] == '1'){
		    $condition='`key`=?';
		}

		$item=$item0->find(Array($condition,$params['id']));


		if(!$item)
			die('File doesn\'t exist');


		if(isset($params['size']))
		{
			$params+=GW_Image::parseDimensions($params['size']);
			unset($params['size']);
		}

		if(GW::s('PROJECT_ENVIRONMENT')==GW_ENV_DEV && !$item->fileExists()){	
			
			initEnviroment(GW_ENV_PROD);
			header('Location: '.GW::s("SITE_URL").$_SERVER['REQUEST_URI']);
		}

		if(isset($params['width']) || isset($params['height']) || isset($params['zoom']) || isset($params['offset']))
			$item->resize($params);


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


		if(isset($_REQUEST['download'])){
			header("Content-Type: application/x-download");	
			header('Content-Disposition: attachment; filename="'.$item->get('original_filename').'";');
			header("Accept-Ranges: bytes");
			header("Content-Length: ".$item->get('size'));
		}else{
			header("Content-Type: ". Mime_Type_Helper::getByFilename($item->getFilename()) );	
		}

		readfile($item->getFilename());
	}
}