<?php

class GW_Site_Application extends GW_Application
{
	public $path_arg=Array();
	public $user_class="GW_Customer";
	public $updates_by_path=[]; //store updates to show in menu
	/**
	 *
	 * @var type GW_Page;
	 */
	public $page;
	
	
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
		if(GW::s('GW_LANG_SEL_BY_GEOIP'))
		{
			$this->geoIpLang();
		}
		
		
		
		$this->_jmpFrst(0);
	}
	
	function geoIpLang()
	{
		$country = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
		if($country!="LT"){
			$this->ln = "en";
		}
	}

	function jumpToFirstChild()
	{
		
		$this->_jmpFrst();
	}

	function jumpLink()
	{
		//Navigator::jump();
		$this->jump($this->page->link);
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

	
	function processPath($path, $args=[])
	{
		$path = explode('?', $path);
		
		if(isset($path[1])){
			parse_str($path[1], $args);
		}	
		
		$path = $path[0];		
		
		$path = explode('/',$path);
		
		$dir = array_shift($path);
		$name = array_shift($path);
		
		
		
		if(!$this->moduleExists($dir, $name))
			die("Failed locating module $dir/$name");
		
		$info=[];
		$info['module_path']=[$dir, $name];		
		$info['module_name']=$name;
		
		$fname = $this->moduleFileName($dir, $name);
				
		return $this->processSiteModule($fname, $path, $info, $args);
	}
	
	function subProcessPath($path, $args=[])
	{
		$path = explode('?', $path);
		
		if(isset($path[1])){
			parse_str($path[1], $args);
			$prevget = $_GET;
			$_GET = $args;
		}
		
		$path = $path[0];		
		
		$langmod = GW_Lang::$module;
		$restore_vars=$this->smarty->getTemplateVars(); 
		
		$res = $this->processPath($path, $args);
		
		$this->smarty->assign($restore_vars);
		GW_Lang::$module = $langmod;
		
		if(isset($prevget))
			$_GET = $prevget;
		
		return $res;
	}
	

	function processSiteModule($file, $params, $info, $args=[])
	{
		//prevent hacking via ajax request
		$file=str_replace('..','',$file);

		if(!file_exists($file))
			die('Fail locating '.$file);

		require_once $file;

		$classname=str_replace('.class','',pathinfo($file, PATHINFO_FILENAME));
		
		GW_Autoload::addAutoloadDir(dirname($file));
		
		$parg = $this->path_arg;
		if(isset($parg[0]) && $parg[0] == 'direct')
			array_shift($parg);
		
		$params = array_merge($params, $parg);	
		
		$m = new $classname([
			'module_file'=>$file,
			'module_path'=>$info['module_path'],
			'app'=>$this, 
			'smarty'=>$this->smarty,
			'args'=>$args,
			'_args'=>['params'=>$params]
		]+$info);
			
		
		$this->module =& $m;
		
		$m->initCommon();
		$m->init();
		
		
		
		$m->attachEvent('BEFORE_TEMPLATE', array($this,'postRun'));				
		
		return $m->process($params);
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
			$this->preloadBlocks();
			$this->processTemplate(GW::s("DIR/SITE/ROOT").$template->path);
		}else{
			$this->processPath($template->path, $_REQUEST);
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

	
	function procInternalLink($url, $proctype=1)
	{
		
		$path_args = explode('?', $url);
		$args =[];
		parse_str($path_args[1] ? $path_args[1]:"", $args);
		$path = $this->ln.'/'.$path_args[0];

		$oldget = $_GET;
		$oldrequest = $_REQUEST;
		$_GET=$args+$oldget;
		
		$_GET['url'] = $path;
		$_GET['opid'] = $this->page->id;
		
		
		$GLOBALS['OPAGE'] = $this->page;
		$GLOBALS['REDIRECT'] = 1;
		$GLOBALS['PAGE_BEFORE_REDIRECT'] = $this->page;
		$GLOBALS['PATHARR_BEFORE_REDIRECT'] = $this->path_arr;
		$GLOBALS['PATH_BEFORE_REDIRECT'] = $this->path;
		
		$_REQUEST = array_merge($_REQUEST, $_GET);

		
		if($proctype==2)
		{
			ob_start();
			GW::request();
			$out = ob_get_contents();
			ob_end_clean();
			ob_clean();
			
			$_GET = $oldget;
			$_REQUEST = $oldrequest;
			
			return (object)['content'=>$out, 'page'=>GW::$context->app->page, 'opage'=>$GLOBALS['PAGE_BEFORE_REDIRECT']];
			
		}else{
			GW::request();	
		}
		
		
	}
	
	function processType($type)
	{		
		switch($type)
		{
			case 0: $this->processPage($this->page);break;
			case 1: $this->jumpToFirstChild();break;
			
			case 4: //external link
				$this->jumpLink();
			break;
			case 2: //internal link
				$this->procInternalLink($this->page->link);				
			break;
			
			case 3: 
				//shift off direct
				$path = preg_replace('/^.*\//U','',$this->path);
				$this->page->path = $path;
				
				$this->processPath($path, $_REQUEST);
			break;
		
			default: die("Unknown page type");break;
		}		
	}

	
	
	
	function process()
	{
		$this->preRun();
		
		
		if(!$this->page->id)
			$this->jumpToFirstPage();
			
		$this->userzoneAccess();
		
		
		if(isset($_GET['toggle-lang-results-active']))
		{
			$this->sess['lang-results-active'] = isset($this->sess['lang-results-active']) && $this->sess['lang-results-active'] ? 0 : 1;
			unset($_GET['toggle-lang-results-active']);
			$this->jump(false, $_GET);
		}
		
		$this->processType($this->page->type);
		
		$this->postRun2();
	}
	
	function prepareMessage($text)
	{
		return GW::ln($text);
	}

	
	public $block_preload;
	
	function preloadBlocks()
	{
		if($this->site){
			$blocks = GW_Site_Block::singleton()->findAll(['site_id=? AND (ln=? OR ln="*") AND preload=1', $this->site->id, $this->ln]);

			foreach($blocks as $block)
				$this->block_preload[ $block->name ] = $block;
		}
		
	}
	
	function getBlock($name)
	{
		if(isset($this->block_preload[$name])){
			return $this->block_preload[$name];
		}else{
			return GW_Site_Block::singleton();
		}
	}

	function prepareContent($content)
	{
		
		$content = preg_replace_callback ('({module:([^}]+)})is'  , function ($m){
			//htmlspecialchars_decode
			$path = htmlspecialchars_decode( $m[1], ENT_NOQUOTES );
			return $this->subProcessPath($path);
		}, $content);
		
		return $content;
	}

}
