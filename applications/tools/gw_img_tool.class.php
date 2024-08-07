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
		
		ob_clean();

		
		$params=$_GET;
		$item0 = new GW_Image();
		
		
		$params['id'] = array_shift($this->path_arr);
		
		if($this->path_arr){
			$params_decrypt = GW_Crypt_Helper::simpleDecryptUrl($this->path_arr[0]);
			$params = array_merge($params, $params_decrypt);
			//d::dumpas($params);
		}
		
		
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
			exit;
		}

		//pdf converter still have issues with webp
		if(isset($_GET['save_format'])){
			$params['save_format'] = $_GET['save_format'];
		}
		
		if(isset($params['width']) || isset($params['height']) || isset($params['zoom']) || isset($params['offset'])){
						
			if(isset($params['method']))
				$params['method'] = preg_replace('/[^a-z0-9]/','', $params['method']);
			
			if(isset($params['zoom'])){
				//max zoom level 10x /// execution time not normal if zoom over 1000x
				$params['zoom'] = min(10, (float)$params['zoom']);
			}
				
			
			
			$item->resize($params);
		}
		

		if(!isset($params['nocache']))
			GW_Cache_Control::setExpires('+24 hour');
		
		//GW_Cache_Control::checkFile($item->getFilename());
		
		if(isset($params['debug']))
		{
			d::dumpas(Array
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
		exit;
	}
}