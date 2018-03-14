<?php

class GW_Site_Application extends GW_Application
{
	public $path_arg=Array();
	public $user_class="GW_Customer";
	public $updates_by_path=[]; //store updates to show in menu
	
	function getPage()
	{
		$this->page = new GW_Page();
		
		if(isset($this->path_arr[0]['name']) && $this->path_arr[0]['name']=='direct')
		{
			$this->page->id=99999999;
			$this->page->type=3;
		}
			
		
		for($i=count($this->path_arr)-1;$i>=0;$i--)
		{
			if($tmp = $this->page->getByPath($this->path_arr[$i]['path']))
			{
				$this->page =& $tmp;
				return true;
			}
				
			array_unshift($this->path_arg, $this->path_arr[$i]['name']);
			
			$this->path_arr[$i]['isarg']=1;
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
		//$this->preRun();
		$this->postRun();
		$this->smarty->display($file);
	}

	function ifAjaxCallProcess()
	{
		if(!isset($_GET['act']) || $_GET['act']!='do:json')
			return;

		$this->processSiteModule(GW::s("DIR/SITE/MODULES").(isset($_GET['module'])?$_GET['module']:'default'), Array());
	}

	
	function processPath($path)
	{
		$path = explode('/',$path);
		
		$dir = array_shift($path);
		$name = array_shift($path);
		
		if(!$this->moduleExists($dir, $name))
			die("Failed locating module $dir/$name");
		
		$info=[];
		$info['module_path']=[$dir, $name];
		$info['module_name']=$name;
		
		$fname = $this->moduleFileName($dir, $name);
		
	
				
		$this->processSiteModule($fname, $path, $info);
	}
	
	function processSiteModule($file, $params, $info)
	{
		//prevent hacking via ajax request
		$file=str_replace('..','',$file);

		if(!file_exists($file))
			die('Fail locating '.$file);

		require_once $file;

		$classname=str_replace('.class','',pathinfo($file, PATHINFO_FILENAME));
		

		$m = new $classname(Array(
		    'module_file'=>$file,
		    'app'=>$this, 
		    'smarty'=>$this->smarty
		)+$info);
		
		
		$this->module =& $m;
		
		$m->init();
		
		$m->attachEvent('BEFORE_TEMPLATE', array($this,'postRun'));		
		
		
		if($this->page->type==3 && isset($m->lang['VIEWS'][$this->page->path]['TITLE']))
			$this->page->title = $m->lang['VIEWS'][$this->page->path]['TITLE'];
		
		
		$params = array_merge($params, $this->path_arg);
		
		$m->process($params);

	}

	
	function moduleFileName($dirname, $name='')
	{
		return GW::s('DIR/SITE/MODULES')."$dirname/module_".($name?$name:$dirname).".class.php";
	}

	function moduleExists($dirname, $name='')
	{
		return file_exists($this->moduleFileName($dirname, $name));
	}

	function processModuleView($file, $view)
	{
		$file = GW::s("DIR/SITE/MODULES").$file;
		require_once $file;

		$restore_vars=$this->smarty->getTemplateVars(); 

		
		$classname=str_replace('.class','',pathinfo($file, PATHINFO_FILENAME));

		$m = new $classname(Array('module_file'=>$file));
		$m->app = $this;
		$m->init();

		$m->processView($view);
		
		$this->smarty->assign($restore_vars); 
	}

	function processPage(GW_Page $page)
	{
		if(!$template=$page->getTemplate())
			die('Template not set');
			
		//$this->preRun();
		
		if(strtolower(pathinfo($template->path, PATHINFO_EXTENSION) == 'tpl'))
		{
			$this->processTemplate(GW::s("DIR/SITE/ROOT").$template->path);
		}else{
			$this->processPath($template->path);
		}
		
	}
	
	function userzoneAccess()
	{
		if(strpos($this->page->path, GW::s('SITE/USERZONE_PATH'))===0 && !$this->user)
		{		
			$getargs=$_GET;
			unset($getargs['url']);
			
			$getargs = $getargs ? '?'.http_build_query($getargs) : '';
			
			$this->jump(GW::s('SITE/PATH_LOGIN'),['returnto_url'=>  $this->path.$getargs]);
			exit;
		}	
	}


	function process()
	{
		//d::dumpas($this->page);
		
		if(!$this->page->id)
			$this->jumpToFirstPage();
		
		$this->preRun();
			
		$this->userzoneAccess();

		switch($this->page->type)
		{
			case 0: $this->processPage($this->page);break;
			case 1: $this->jumpToFirstChild();break;
			case 2: $this->jumpLink();break;
			case 3: 
				//shift off direct
				$path = preg_replace('/^.*\//U','',$this->path);
				$this->page->path = $path;
				
				
				
				$this->processPath($path);
			break;
			default: die("Unknown page type");break;
		}
	}

}