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
	
	

	function initLang()
	{
		parent::initLang();
		
		
		//kol kas atjungiam PIECES KOL TVARKOM ant prancuzu konkurso
	
		gw::s("LANGS",array_merge(gw::s("LANGS"),GW::s('i18nExt')));;
		
		

		if(in_array($this->ln, GW::s('i18nExt'))){
			$this->i18next = array_flip(GW::s('i18nExt'));

			$this->initI18nSchema();
			
			$this->initSite(); //again// previuosly not loaded i18ext
		}
		
		
		//d::dumpas(gw::s("LANGS"));
		
		$this->initLangNames(gw::s("LANGS"));
	}
	
	function init()
	{
		
		parent::init();
	}
	
	
	
	function getPageByPathArr()
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
	
	
	function getPage()
	{
		$this->getPageByPathArr();
		
		if($this->page)
		{
			$pages = $this->page->getParents();
			$pages[] = $this->page;
			
			foreach($pages as $page){
				$this->page_arr[$page->level]=$page;
			}
		}
	}
	
	//no data objects catching
	function requestInfoInnerDataObject(&$name, &$item)
	{
		
		if($this->path_arr[1] == 'direct'){
			return parent::requestInfoInnerDataObject($name, $item);
		}
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
		if(function_exists('geoip_country_code_by_name')){
			$country = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
			if($country!="LT"){
				$this->ln = "en";
			}
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
		
		
		$restore_vars=$this->smarty->getTemplateVars(); 
		$restore_mod = GW_Lang::$module;
		
		
		$info=[];
		$info['module_path']=[$dir, $name];		
		$info['module_name']=$name;
		
		$fname = $this->moduleFileName($dir, $name);
				
		$result = $this->processSiteModule($fname, $path, $info, $args);
		
		
		
		$this->smarty->assign($restore_vars); 
		GW_Lang::$module = $restore_mod;
		
		return $result;
	}
	
	
	function processModule($path_info, $request_params, $access_level=false)
	{
		if (!isset($path_info['module']))// pvz yra users katalogas bet nera module_users.class.php, gal vidiniu moduliu tada yra
			$this->jumpToFirstChild();
		
		$module = $this->constructModule1($path_info);

		$this->module = & $module;

		
		$path_info['params'] = isset($path_info['params']) ? $path_info['params'] : [];

		//d::dumpas($path_info);
		
		$module->_args = ['params' => $path_info['params'], 'request_params' => $request_params];
		$module->init();

		//if(GW::$app->inner_request)
		//	$module->ob_collect = false;

		$module->attachEvent('BEFORE_TEMPLATE', array($this, 'postRun'));

		$module->process();
		
		$this->postRun2();
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
		if(isset($parg[0]) && $parg[0] == 'direct'){
			$parg=[];//array_shift($parg);
		}
		
		$params = array_merge($params, $parg);	
		
		$m = new $classname([
			'module_file'=>$file,
			'module_path'=>$info['module_path'],
			'app'=>$this, 
			'smarty'=>$this->smarty,
			'args'=>$args,
			'_args'=>['params'=>$params,'request_params' => $args]
		]+$info);
			
		
		$this->module =& $m;
		
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
		
		if(isset($path_args[1]))
			parse_str($path_args[1] ? $path_args[1]:"", $args);
		
		$path = $this->ln.'/'.$path_args[0];

		$oldget = $_GET;
		$oldrequest = $_REQUEST;
		$_GET=$args+$oldget;
		
		$_GET['url'] = $path;
		$_GET['opid'] = $this->page->id;
		
		
		GW::$globals['OPAGE'] = $this->page;
		GW::$globals['REDIRECT'] = 1;
		GW::$globals['PAGE_BEFORE_REDIRECT'] = $this->page;
		GW::$globals['PATHARR_BEFORE_REDIRECT'] = $this->path_arr;
		GW::$globals['PATH_BEFORE_REDIRECT'] = $this->path;
		
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
			
			return (object)['content'=>$out, 'page'=>GW::$context->app->page, 'opage'=>GW::$globals['PAGE_BEFORE_REDIRECT']];
			
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

	function postRun2()
	{
		//d::dumpas('testas');
		if(GW_Lang::$developLnResList){
			d::ldump(GW_Lang::$developLnResList);
			
			
			//d::ldump(GW::db()->query_times);
			//ctrl+3 debug mode ir rodys
		}			
	}
	
	function postRun()
	{
	
	}

	function preRun() {
		if((GW::$context->app->sess['lang-results-active'] ?? false) == '1'){
			GW::db()->debug = true;
		}
		return parent::preRun();
	}
	
	function initAnonymousUser($create=true)
	{
		if($this->user)
			return false;
			
		// Anonymous User Identification cookie
		if(!isset($_COOKIE['AUID']) && $create){
			Navigator::setCookie("AUID", $uid = GW_String_Helper::getRandString(40));			
		}else{
			$uid = $_COOKIE['AUID'] ?? false;
		}
		
		$user = GW_Anonymous_User::singleton()->find(['idcookie=?', $uid]);
		
			
		if(!$user){
			if(!$create)
				return false;
			
			$vals = ['idcookie'=>$uid];
			
			$user = GW_Anonymous_User::singleton()->createNewObject($vals);
			$user->insert();
		}
		
		$user->setValues([
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
			    'lastip'=>$_SERVER['REMOTE_ADDR'],
			'use_lang' => $this->ln
		]);
		$user->updateChanged();
		
		return $user;		
	}

	function idInPath($id)
	{
		return is_numeric($id) ? $id : "id_".$id;
	}	
	
	
	function requestInfo() {

		parent::requestInfo();

		foreach($this->path_arr as $arr){
			if(isset($arr['data_object_id']))
				$this->path_data_objects[$arr['name']] = $arr['data_object_id'];
		}
	}
	
}
