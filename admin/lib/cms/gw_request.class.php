<?

class GW_Request
{
	var $path;
	//detailed path info level by level
	var $path_arr;
	var $path_arr_parent;
	/**
	 * language code
	 */
	var $ln;
	
	/**
	 * @var GW_ADM_Page
	 */
	var $page;
	var $module;
	var $base;
	var $data_object_id;
	
	//argumentai kurie bus išlaikomi jumpinant, sudarinėjant linkus, perduodami per formas
	//pvz jei bus Array('pid'=>1), visad bus pernesama pid reiksme
	var $carry_params = Array();	
	

	function __construct()
	{	
	}
	
	function init()
	{
		$this->requestInfo();
		$this->getPage();		
	}


	function buildUri($path=false, $params=Array())
	{
		$ln =  $params['ln'] ? $params['ln'] : $this->ln;
		unset($params['url']);
		unset($params['ln']);

		if($path===false)
			$path=$this->path;
		
		return 
			$this->base.
			$ln.
			($path?'/':'').$path.
			($params? '?'.http_build_query($params):'');
	}
	
	
	/**
	 * returns $_GET parameters which is configured to carry through jumps
	 */
	function carryParams()
	{
		static $cache;
		
		if($cache)
			return $cache;
		
		return $cache = array_intersect_key($_GET, $this->carry_params);
	}
	
	function jump($path=false, $params=Array())
	{	
		if(!is_array($params))
			backtrace();
			
		$params = $params + $this->carryParams();
			
		Navigator::jump(self::buildUri($path, $params));
	}

	function jumpToFirstChild()
	{
		if(!$item = $this->page->getFirstChild())
			$this->fatalError('Restricted access');
		
			
		$this->jump($item->get('path'));
	}
	

	
	//gali buti ieskoma pvz
	//sitemap/templates/15/tplvars/form jei bus toks - sitemap/templates/tplvars tai supras
	//users/users/form 
	
	function getPage()
	{
		$this->page = new GW_ADM_Page();		
		
		for($i=count($this->path_arr)-1;$i>=0;$i--)
		{
			if($tmp = $this->page->getByPath($this->path_arr[$i]['path']))
			{
				$this->page =& $tmp;
				return true;
			}	
		}
		
		return false;
	}	
	
	
	function moduleExists($dirname, $name='')
	{
		return file_exists(GW::$dir['MODULES']."$dirname/module_".($name?$name:$dirname).".class.php");
	}
	
	
	function ifAjaxCallProcess()
	{
		if($_GET['act']!='do:json')
			return;
		
		$path_info=$this->getModulePathInfo($_GET['path']);
		
		$this->processModule($path_info);
	}	

	/**
	* modulio vardas gali buti pvz: a) users/register arba tik  b) users
	* klases failas gules:
	* a - users/register.class.php
	* b - users/users.class.php
	*/

	
	function requestInfo()
	{
		$this->uri = Navigator::getUri();
		$this->base = Navigator::getBase();
		$path_arr = explode('/', $_GET['url']);
		unset($_GET['url']);
		
		
		$ln = array_shift($path_arr);
		$this->ln = in_array($ln, GW::$static_conf['LANGS']) ? $ln : GW::$static_conf['LANGS'][0];
	
		$_SESSION['GW']['cms_ln']=$this->ln;		
		
		
		$this->path=implode('/', $path_arr);
		
		
		
		$this->path_arr = Array();
		$path_clean = '';
		$path='';
		$item=false;
		
		foreach($path_arr as $i => $name)
		{
			
			$path.=($path ? '/':'').$name;
			
			if(is_numeric($name) && $item)
			{
				$item['data_object_id']=(int)$name;
				$this->data_object_id = $item['data_object_id'];
				$item['path'].='/'.$name;
				continue;
			}
			
			
			$path_clean.=($path_clean ? '/':'').$name;
			
			$item =& $this->path_arr[];
			$item=Array('name'=>$name, 'path'=>$path, 'path_clean'=>$path_clean);
		}
		

		
		//jeigu $last_item['data_object_id'] tai nustatyt $_GET['id']
		if($item['data_object_id'])
			$_GET['id']=$item['data_object_id'];
			
		$this->path_clean = $path_clean;
		

		$this->path_arr_parent = 
			count($this->path_arr) >= 2 ? 
				$this->path_arr[count($this->path_arr)-2] : Array();
		

		//jeigu bus path articles/items/132
		//nuimti id - articles/items
		//kad galetu sudarinet teisingus linkus
				
		if(is_numeric($path_arr[count($path_arr)-1]))
			$this->path = dirname($this->path);
		
		
	}

	function getModulePathInfo($path)
	{
		$level=0;
		$info=Array();
		$path_arr=explode('/', $path);
		$path_arr_clean=array_map(Array('GW_Validation_Helper','classFileName'), $path_arr);
		
		
		if(is_dir($dirname=GW::$dir['MODULES'].$path_arr[0]))
			$info['dirname']=$path_arr[0];
		else
			return Array('path'=>Array('default'),'dirname'=>'default','module'=>'default');
		

			
		foreach($path_arr_clean as $i => $name)
			if(self::moduleExists($path_arr_clean[0], $name))
				$level=$i+1;
				
		if($level)
		{
			$info['path']=array_splice($path_arr_clean, 0, $level);
			$info['module']=$info['path'][count($info['path'])-1];
		
			$info['params']=array_splice($path_arr, $level, count($path_arr));
		}
		
		return $info;
	}
	
	
	function &constructModule($dir, $name)
	{
		include_once GW::$dir['MODULES']."{$dir}/module_{$name}.class.php";
		$name = "Module_{$name}";
		
		$obj = new $name();
		return $obj;
	}
	
	
	function processModule($path_info)
	{
		if(!$path_info['module'])// pvz yra users katalogas bet nera module_users.class.php, gal vidiniu moduliu tada yra
			$this->jumpToFirstChild();
		
		$module =& $this->module;
		$module = $this->constructModule($path_info['dirname'], $path_info['module']);
		
		$module->module_name = $path_info['module'];
		$module->module_path = $path_info['path'];
		$module->module_dir = GW::$dir['MODULES'].$path_info['dirname'].'/';

		$module->init();
		
		$module->process((array)$path_info['params']);		
	}
	
	function setMessage($msg,$status_id=0)
	{
		$_SESSION['messages'][]=Array($status_id, $msg);
	}
	
	function setMessages($msgs=Array())
	{
		foreach((array)$msgs as $field => $msg)
			$_SESSION['messages'][$field]=Array(0,$msg);	
	}
	
	function removeMessages()
	{
		$_SESSION['messages']=Null;
	}
	
	/**
	 * level 2=error, 1=warning, 3=info
	 */
	function setErrors($errors=Array(), $level=2)
	{
		foreach((array)$errors as $field => $error_str)
			$_SESSION['messages'][$field]=Array($level,$error_str);	
	}
	
	
	function fatalError($message)
	{
		$this->setErrors(Array($message));
		
		$path_info=Array();

		$path_info['module']='default';
		$path_info['path']=Array('default');
		$path_info['dirname']='default';

		$this->processModule($path_info);
		
		exit;
	}
	
	function process()
	{
		if(!$this->page->canAccess())
			if(GW::$user)
				$this->jumpToFirstChild();
			else
				$this->jump(GW::$static_conf['GW_SITE_PATH_LOGIN']);
				
		
				
		$path_info=$this->getModulePathInfo($this->path_clean);
		
		$this->processModule($path_info);
	}
	
}