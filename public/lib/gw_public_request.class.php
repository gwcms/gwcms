<?

class GW_Public_Request extends GW_Request
{
	var $path;
	//detailed path info level by level
	var $path_arr;
	/**
	 * language code
	 */
	var $ln;
	
	/**
	 * @var GW_Page
	 */
	var $page;
	var $module;
	var $base;
	var $path_arg;

	function __construct()
	{	
		$this->page = new GW_Page();
	}
	
	function init()
	{
		$this->requestInfo();
		$this->getPage();
	}



	function getPage()
	{
		for($i=count($this->path_arr)-1;$i>=0;$i--)
		{
			if($tmp = $this->page->getByPath($this->path_arr[$i]['path']))
			{
				$this->page =& $tmp;
				return true;
			}	
			
			$this->path_arg= $this->path_arr[$i]['name'].($this->path_arg?'/':'').$this->path_arg;
		}
		
		return false;
	}


	function requestInfo()
	{
		$this->uri = Navigator::getUri();
		$this->base = Navigator::getBase();
		
		$path_arr = explode('/', $_GET['url']);
		unset($_GET['url']);
		
		
		$ln = array_shift($path_arr);
		$this->ln = in_array($ln, GW::$static_conf['PUB_LANGS']) ? $ln : GW::$static_conf['PUB_LANGS'][0];
	
		$_SESSION['GW']['ln']=$this->ln;		
		
		
		$this->path=implode('/', $path_arr);
		
		$this->path_arr = Array();
		$path_clean = '';
		$path='';
		$item=false;
		
		foreach($path_arr as $i => $name)
		{
			
			$path.=($path ? '/':'').$name;
			
			/*
			if(is_numeric($name) && $item)
			{
				$item['data_object_id']=(int)$name;
				$item['path'].='/'.$name;
				continue;
			}
			*/
			
			$path_clean.=($path_clean ? '/':'').$name;
			
			$item =& $this->path_arr[];
			$item=Array('name'=>$name, 'path'=>$path, 'path_clean'=>$path_clean);
		}
		

		
		//jeigu $last_item['data_object_id'] tai nustatyt $_GET['id']
		if($item['data_object_id'])
			$_GET['id']=$item['data_object_id'];
			
		$this->path_clean = $path_clean;
	}
	
	
	
	
	function _jmpFrst($cp=true)
	{
		$item0 = $cp ? $this->page : $this->page->createNewObject();

		$page = $item0->getChilds(Array('in_menu'=>1,'return_first_only'=>1));
		
		if(!$page)
			die('No active pages');
		
			
		$this->jump($page->path);	
	}
	
	function jumpToFirstPage()
	{
		$this->_jmpFrst(0);
	}
	
	function jumpToFirstChild()
	{
		$this->_jmpFrst();
	}

	function jumpLink()
	{
		Navigator::jump($this->page->link);
	}
	
	function processTemplate($file)
	{
		GW::$smarty->display($file);
	}
	
	function ifAjaxCallProcess()
	{
		if($_GET['act']!='do:json')
			return;

		$this->processModule(GW::$dir["PUB_MODULES"].$_GET['module']);
	}	
	
	
	function processModule($file, $exit=true)
	{
		//prevent hacking via ajax request
		$file=str_replace('..','',$file);
		
		if(!file_exists($file))
			die('Fail locating '.$file);
		
		require_once $file;
		
		$classname=str_replace('.class','',pathinfo($file, PATHINFO_FILENAME));
		
		$m = new $classname(Array('module_file'=>$file));
		$m->process();
		
		exit;
	}
	
	
	function moduleExists($dirname, $name='')
	{		
		return file_exists(GW::$dir['PUB_MODULES']."$dirname/module_".($name?$name:$dirname).".class.php");
	}	
	
	function processModuleView($file, $view)
	{
		$file = GW::$dir['PUB_MODULES'].$file;
		require_once $file;
		
		$classname=str_replace('.class','',pathinfo($file, PATHINFO_FILENAME));
		
		$m = new $classname(Array('module_file'=>$file));
		$m->init();
		
		$m->processView($view);
	}
	
	function processPage()
	{
		if(!$template=$this->page->getTemplate())
			die('Template not set');
			
		$file = GW::$dir['PUB'].$template->path;
		
		if(strpos($file,'/../')!==false || !file_exists($file))
			die('Illegal template filename');
			
		switch(strtolower(pathinfo($file, PATHINFO_EXTENSION)))
		{
			case 'tpl': $this->processTemplate($file);break;
			case 'php': $this->processModule($file);break;
		}
	}
	
	
	function process()
	{
		if(!$this->page->id)
			$this->jumpToFirstPage();
				
		switch($this->page->type)
		{
			case 0: $this->processPage();break;
			case 1: $this->jumpToFirstChild();break;
			case 2: $this->jumpLink();break;
			default: die("Unknown page type");break;
		}
	}	
	
}