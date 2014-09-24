<?php

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
	var $path_arg=Array();

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
				
			array_unshift($this->path_arg, $this->path_arr[$i]['name']);
		}

		

		return false;
	}
	
	//no data objects catching
	function requestInfoInnerDataObject(&$name, &$item)
	{
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
		if(!isset($_GET['act']) || $_GET['act']!='do:json')
			return;

		$this->processModule(GW::$dir["PUB_MODULES"].(isset($_GET['module'])?$_GET['module']:'default'), Array());
	}

	
	function processPath($path)
	{
		$path = explode('/',$path);
		
		$dir = array_shift($path);
		$name = array_shift($path);
		
		if(!$this->moduleExists($dir, $name))
			die("Failed locating module $dir/$name");
		
		$fname = $this->moduleFileName($dir, $name);
				
		$this->processModule($fname, $path);
	}
	
	function processModule($file, $params, $exit=true)
	{
		//prevent hacking via ajax request
		$file=str_replace('..','',$file);

		if(!file_exists($file))
			die('Fail locating '.$file);

		require_once $file;

		$classname=str_replace('.class','',pathinfo($file, PATHINFO_FILENAME));

		$m = new $classname(Array('module_file'=>$file));
		
		$this->module =& $m;
		
		$params = array_merge($params, $this->path_arg);
		
		$m->process($params);

	}

	
	function moduleFileName($dirname, $name='')
	{
		return GW::$dir['PUB_MODULES']."$dirname/module_".($name?$name:$dirname).".class.php";
	}

	function moduleExists($dirname, $name='')
	{
		return file_exists($this->moduleFileName($dirname, $name));
	}

	function processModuleView($file, $view)
	{
		$file = GW::$dir['PUB_MODULES'].$file;
		require_once $file;

		$restore_vars=GW::$smarty->getTemplateVars(); 

		
		$classname=str_replace('.class','',pathinfo($file, PATHINFO_FILENAME));

		$m = new $classname(Array('module_file'=>$file));
		$m->init();

		$m->processView($view);
		
		GW::$smarty->assign($restore_vars); 
	}

	function processPage()
	{
		if(!$template=$this->page->getTemplate())
			die('Template not set');
			

		if(strtolower(pathinfo($template->path, PATHINFO_EXTENSION) == 'tpl'))
		{
			$this->processTemplate(GW::$dir['PUB'].$template->path);
		}else{
			$this->processPath($template->path);
		}
		
	}
	
	function userzoneAccess()
	{		
		if(strpos($this->page->path, GW::$static_conf['GW_USERZONE_PATH'])===0 && !GW::$user)
		{

			$this->jump(GW::$static_conf['GW_SITE_PATH_LOGIN']);
			exit;
		}	
	}


	function process()
	{
		if(!$this->page->id)
			$this->jumpToFirstPage();
			
		$this->userzoneAccess();

		switch($this->page->type)
		{
			case 0: $this->processPage();break;
			case 1: $this->jumpToFirstChild();break;
			case 2: $this->jumpLink();break;
			default: die("Unknown page type");break;
		}
	}

}