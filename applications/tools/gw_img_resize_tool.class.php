<?php


/*
//add in config
GW::s('IMG_RESIZE_TOOL_REPOSITORIES',[
    'adbphotos' => GW::s('DIR/SYS_REPOSITORY').'adbphoneimg/',
]);
 */

class GW_Img_Resize_Tool extends GW_Img_Tool
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
	
	function output_image($file, $original)
	{
		if(isset($_REQUEST['debug']))
		{
			dump(
			Array(
					'file'=>$GLOBALS['file'],
					'file_exists'=>(int)file_exists($GLOBALS['file']),
					'resized'=>$GLOBALS['resized'],
					'resize_params'=>$GLOBALS['params']
			)
			);
			exit;
		}

		GW_Cache_Control::setExpires('+24 hour');
		header("Content-Type: ". Mime_Type_Helper::getByFilename($original) );
		readfile($file);
	}	
	
	function __getFile($file)
	{
		$repositories = GW::s('IMG_RESIZE_TOOL_REPOSITORIES');
		//strict repositories
		$file = preg_replace('/[^.a-z0-9_ -]/i', '', $file);

		if(!isset($_GET['dirid']) && !isset($repositories[$_GET['dirid']]) )
			die('dirid not specified or invalid');

		$file = $repositories[$_GET['dirid']].'/'.$file;
		
		return $file;
	}
	
	function process()
	{
		$repositories = GW::s('IMG_RESIZE_TOOL_REPOSITORIES');
		
		GW::s('DIR/SYS_IMAGES_CACHE_1', $cachedir=GW::s('DIR/SYS_REPOSITORY').'cache/images_1/');
		

		@mkdir($cachedir);
		@chmod($cachedir,0777);


		$file = $_GET['file'];
		$file = str_replace('..','error',$file); //prevent exit directory
		
		if(isset($_GET['fetch']))
		{
			/* Example config:
				GW::s('IMG_FETCH_SOURCE_NATOS1', 'http://library.hlmgbdealers.com/images/large/{IMGID}_1.jpg');
				GW::s('IMG_RESIZE_TOOL_REPOSITORIES/NATOS1', GW::s('DIR/SYS_REPOSITORY').'/productimg/');
			 * Example request:
			 *	http://natosnew/tools/img_resize?file=ZM507&fetch=NATOS1&size=480x700
			 */
			
			$source = GW::s('IMG_FETCH_SOURCE_'.$_GET['fetch']);
			$_GET['dirid'] = $_GET['fetch'];
			
			$fp = $this->__getFile($file);
			$storepath = $repositories[$_GET['dirid']];
			
			if(!file_exists($fp)){
				$data = file_get_contents(str_replace('{IMGID}', $file, $source));
				$file = $file.'.jpg';
				$storepath = $storepath.$file;
				//@mkdir(dirname($storepath), 0777, true);
				file_put_contents($storepath, $data);
				//d::dumpas($storepath);
			}
		}
		
		
		//public repositories - security weak
		if($_GET['dirid']=='repository'){
			$file = GW::s('DIR/REPOSITORY').$file;
		}else{
			$file = $this->__getFile($file);		
		}
			
		

		
		if(!isset($_REQUEST['size'])){
			self::output_image($file, $file);
		}

		list($params['width'],$params['height'])=explode('x',$_REQUEST['size']);
		
		if(isset($_GET['method']))
			$params['method'] = $_GET['method'];


		if(!file_exists($file))
			die('Failed to locate file: '.$file);


		$resized = $cachedir.str_replace('/','__',$file).'.'.md5(serialize($_GET));

		//check if is cached

		if(file_exists($resized))
		{
			self::output_image($resized, $file);
			exit;
		}
		

		//doresize & output file
		$im = new GW_Image_Manipulation($file);
		$im->resize($params);

		$params['save_format']='auto';

		$im->save($resized, $params['save_format']);
		$im->clean();


		if(file_exists($resized))
		{
			self::output_image($resized, $file);
			exit;
		}
	}
}