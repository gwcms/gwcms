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

		$file = $repositories[$_GET['dirid']].'/'.$file.'.jpg';
		
		return $file;
	}
	
	function process()
	{
		ob_clean();
		
		
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
			
			if(isset($_GET['debug'])){
				d::dumpas([
				    'src'=>$source, 
				    'info'=>shell_exec('ls -l '.$fp),
				    'fp'=>$fp, 
				    'exists'=>file_exists($fp)?'yes':'no',
				    'cachedir'=>$cachedir
				]);
			}
			
			if(!file_exists($fp)){
				$url = str_replace('{IMGID}', GW_Http_Agent::urlencode($file), $source);
				//d::dumpas($url);
				$data = file_get_contents($url);
				//@mkdir(dirname($storepath), 0777, true);
				file_put_contents($fp, $data);
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


		$resized = $cachedir.md5($file).'.'.md5(serialize($_GET)).'.'.(filesize($file));

		//check if is cached

		if(file_exists($resized))
		{
			self::output_image($resized, $file);
			exit;
		}
		
		$params['save_format']=GW::s('IMAGE_THUMB_FORMAT');
		//pdf converter still have issues with webp
		if(isset($_GET['save_format'])){
			$params['save_format'] = $_GET['save_format'];
		}

		//doresize & output file
		$im = new GW_Image_Manipulation($file);
		$im->resize($params);
		
		
		if(isset($_GET['filters'])){
			$filters = explode(';',$_GET['filters']);
			foreach($filters as $filter)
			{
				list($name, $arg) = explode(':', $filter);
				$im->filter($name, $arg);
			}
		}
				

		

		$im->save($resized, $params['save_format']);
		$im->clean();
		
		if($_GET['debug']??false)
			d::dumpas($resized);


		if(file_exists($resized))
		{
			self::output_image($resized, $file);
			exit;
		}
	}
}