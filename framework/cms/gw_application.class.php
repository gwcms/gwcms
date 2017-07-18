<?php

class GW_Application
{

	public $app_name;
	public $path;
	public $app_base; //application handler base path
	public $app_root; //application root path for real files
	public $sys_base; //system base path
	//detailed path info level by level
	public $path_arr;
	public $path_arr_parent;

	/**
	 * language code
	 */
	public $ln;
	public $page;
	public $module;
	public $data_object_id;
	public $db;
	public $user;
	public $auth;
	public $lang; //strings
	public $smarty;
	//argumentai kurie bus išlaikomi jumpinant, sudarinėjant linkus, perduodami per formas
	//pvz jei bus Array('pid'=>1), visad bus pernesama pid reiksme
	public $carry_params = Array();
	public $inner_request = false;
	public $user_class = "GW_User";
	public $sess; //application session - to avoid conflicts with site - admin apps

	/*
	 * loaded from session!
	 * */
	var $errors;

	function loadConfig()
	{
		include GW::s('DIR/APPLICATIONS') . strtolower($this->app_name) . '/config/main.php';
	}

	function initSession()
	{
		ob_start();
		session_start();

		$this->sess = & $_SESSION[$this->app_name]; //to avoid conflicts with site - admin apps
	}

	function initDB()
	{
		$this->db = GW::db();
	}

	function initAuth()
	{
		$this->auth = new GW_Auth(new $this->user_class());
		$this->user = $this->auth->isLogged();
		
		if($this->auth->error)
			$this->setError($this->auth->error);

		if (!isset($GLOBALS['do_not_register_request']) && $this->user)
			$this->user->onRequest();
	}

	function initSmarty()
	{
		require GW::s('DIR/VENDOR') . 'smarty/SmartyBC.class.php';
		$s = & $this->smarty;

		$s = new SmartyBC;


		$s->compile_check = true;
		//$s->allow_php_tag=true;
		$s->error_reporting = E_ALL & ~E_NOTICE;


		$s->compile_dir = GW::s("DIR/TEMPLATES_C");
		$s->template_dir = GW::s("DIR/$this->app_name");


		$s->_file_perms = 0666;
		$s->_dir_perms = 0777;

		$s->assignByRef('GLOBALS', $GLOBALS);
		$s->assign('app', $this);

		$s->assign('app_base', $this->app_base);
		$s->assign('sys_base', $this->sys_base);
		$s->assign('app_root', $this->app_root);

		$s->assignByRef('ln', $this->ln);
		$s->assignByRef('l', GW::$l);
		$s->assignByRef('lang', $this->lang);
		$s->assignByRef('page', $this->page);
		
		$x = new stdClass;
		$s->assignByRef('footer_hidden', $x);
		$s->merge_compiled_includes = true;
	}

	function initLang()
	{
		$this->setCurrentLang($this->ln);
		GW_Lang::$langf_dir = GW::s("DIR/{$this->app_name}/LANG");
		GW_Lang::$module_dir = GW::s("DIR/{$this->app_name}/MODULES");

		$this->lang = GW::l('/g/');
	}
	
	function setCurrentLang($ln)
	{
		$this->ln = $ln;
		GW_Lang::setCurrentLang($this->ln);
	}
	

	function __construct($context)
	{
		foreach ($context as $key => $value)
			$this->$key = $value;
	}

	function init()
	{
		$this->loadConfig();
		$this->initDB();
		$this->initSession();

		$this->initAuth();

		$this->requestInfo();


		$this->getPage();

		$this->initLang();
		$this->initSmarty();
	}

	function buildUri($path = false, $getparams = Array(), $params = [])
	{
		$ln = isset($params['ln']) ? $params['ln'] : $this->ln;

		unset($getparams['url']);

		if (isset($params['carry_params']))
			$getparams = (is_array($getparams) ? $getparams : []) + $this->carryParams();

		if ($path === false)
			$path = $this->path;

		return
			(isset($params['absolute']) ? Navigator::__getAbsBase() : '') .
			(isset($params['app']) ? $params['app'] . '/' : $this->app_base) .
			$ln .
			($path ? '/' : '') . $path .
			($getparams ? '?' . http_build_query($getparams) : '');
	}

	/**
	 * returns $_GET parameters which is configured to carry through jumps
	 */
	function carryParams()
	{
		static $cache;

		if ($cache)
			return $cache;

		return $cache = array_intersect_key($_GET, $this->carry_params);
	}

	function jump($path = false, $params = Array())
	{
		if (!is_array($params))
			backtrace();

		Navigator::jump(self::buildUri($path, $params, ['carry_params' => 1]));
	}

	//gali buti ieskoma pvz
	//sitemap/templates/15/tplvars/form jei bus toks - sitemap/templates/tplvars tai supras
	//users/users/form 

	function getPage()
	{
		$this->page = new GW_ADM_Page();

		for ($i = count($this->path_arr) - 1; $i >= 0; $i--) {
			if ($tmp = $this->page->getByPath($this->path_arr[$i]['path'])) {
				$this->page = & $tmp;
				return true;
			}
		}

		return false;
	}

	function moduleExists($dirname, $name = '')
	{
		return file_exists(GW::s("DIR/{$this->app_name}/MODULES") . "$dirname/module_" . ($name ? $name : $dirname) . ".class.php");
	}

	function preRun()
	{
		$files = glob(GW::s("DIR/{$this->app_name}/MODULES") . '*/zz_event_prerun*');

		foreach ($files as $file)
			include($file);
	}

	function postRun()
	{
		$files = glob(GW::s("DIR/{$this->app_name}/MODULES") . '*/zz_event_postrun*');

		foreach ($files as $file)
			include($file);
	}

	function ifAjaxCallProcess()
	{
		if ($_GET['act'] != 'do:json')
			return;

		$path_info = $this->getModulePathInfo($_GET['path']);

		$this->processModule($path_info);
	}

	function requestInfoInnerDataObject(&$name, &$item)
	{
		if (is_numeric($name) && $item) {
			$item['data_object_id'] = (int) $name;
			$data_object_id = $item['data_object_id'];
			$item['path'].='/' . $name;
			return true;
		}
	}

	/**
	 * modulio vardas gali buti pvz: a) users/register arba tik  b) users
	 * klases failas gules:
	 * a - users/register.class.php
	 * b - users/users.class.php
	 */
	//returns url
	function requestInfoInner()
	{
		$parr = $this->path_arr;
		$ln = array_shift($parr);

		$path = implode('/', $parr);



		$path_clean = '';
		$path = '';
		$item = false;
		$path_arr = Array();

		foreach ($parr as $i => $name) {
			$path.=($path ? '/' : '') . $name;

			if ($this->requestInfoInnerDataObject($name, $item))
				continue;

			$path_clean.=($path_clean ? '/' : '') . $name;

			$item = & $path_arr[]; //prideti item i $path_arr
			$item = Array('name' => $name, 'path' => $path, 'path_clean' => $path_clean);
		}

		$path_arr_parent = count($path_arr) >= 2 ?
			$path_arr[count($path_arr) - 2] : Array();


		//jeigu bus path articles/items/132
		//nuimti id - articles/items
		//kad galetu sudarinet teisingus linkus

		if (count($path_arr) && is_numeric($path_arr[count($path_arr) - 1]))
			$path = dirname($path);



		return compact('ln', 'path', 'path_arr', 'path_clean', 'data_object_id', 'path_arr_parent');
	}

	function requestInfo()
	{
		$pack = $this->requestInfoInner();

		if (isset($this->args['test_request_info']))
			d::dumpas($pack);

		extract($pack);

		$this->app_base = $this->sys_base . $this->app_base;
		$this->app_root = $this->sys_base . str_replace(GW::s('DIR/ROOT'), '', GW::s("DIR/$this->app_name/ROOT"));

		$this->path = $path;
		$this->path_arr = $path_arr;
		$this->path_arr_parent = $path_arr_parent;
		$this->path_clean = $path_clean;


		$this->ln = in_array($ln, GW::$settings['LANGS']) ? $ln : GW::$settings['LANGS'][0];




		$_SESSION['GW']['cms_ln'] = $this->ln;


		//jeigu $last_item['data_object_id'] tai nustatyt $_GET['id']
		if (isset($data_object_id) && $data_object_id)
			$_GET['id'] = $data_object_id;
	}

	function getModulePathInfo($path)
	{
		$level = 0;
		$info = Array();
		$path_arr = explode('/', $path);
		$path_arr_clean = $path_arr;
		//array_map(Array('GW_Validation_Helper', 'classFileName'), $path_arr);

		if (is_dir($dirname = GW::s("DIR/{$this->app_name}/MODULES") . $path_arr[0]))
			$info['dirname'] = $path_arr[0];
		else
			return Array('path' => Array('default'), 'dirname' => 'default', 'module' => 'default');




		foreach ($path_arr_clean as $i => $name)
			if (self::moduleExists($path_arr_clean[0], $name))
				$level = $i + 1;




		if ($level) {
			$info['path'] = array_splice($path_arr_clean, 0, $level);
			$info['module'] = $info['path'][count($info['path']) - 1];

			$info['params'] = array_splice($path_arr, $level, count($path_arr));
		}

		return $info;
	}

	function &constructModule($dir, $name)
	{
		include_once GW::s("DIR/{$this->app_name}/MODULES") . "{$dir}/module_{$name}.class.php";
		$name = "Module_{$name}";

		$obj = new $name();
		$obj->app = $this;

		return $obj;
	}

	function constructModule1($path_info)
	{
		$module = $this->constructModule($path_info['dirname'], $path_info['module']);
		
		$module->module_name = $path_info['module'];
		$module->module_path = $path_info['path'];
		$module->module_dir = GW::s("DIR/{$this->app_name}/MODULES") . $path_info['dirname'] . '/';



		return $module;
	}

	function processModule($path_info, $request_params)
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
	}
	/*
	 * sms/mass?act=update
	 * */

	function innerProcess($path)
	{
		$path_e = explode('?', $path, 2);

		if (count($path_e) > 1) {
			list($path, $request_args) = $path_e;
			parse_str($request_args, $request_args);
		}

		$path_info = $this->getModulePathInfo($path);

		return $this->processModule($path_info, $request_args);
	}

	function setMessage($msg, $type = 0, $title=false, $field=false, $obj_id=false)
	{
		if(is_array($msg))
		{
			if(!isset($msg['type']))
				$msg['type'] = $type;			
			
			$this->sess['messages'][] = $msg;
		}else{
		
			$data = ['type' => $type, 'text'=>$msg];

			if($title)
				$data['title'] = $title;

			if($field)
				$data['field'] = $title;

			if($obj_id)
				$data['obj_id'] = $obj_id;		

			$this->sess['messages'][] = $data;
		}
	}
	
	function setError($message)
	{
		$this->setMessage(['type'=>GW_MSG_ERR, 'text'=>$message]);
	}


	function acceptMessages($prepare = false)
	{
		
		if (!isset($this->sess['messages']) || !($data = $this->sess['messages']))
			return false;

		if($prepare){
			foreach($data as $i => $msg){
				//translate error message if it is translation path
				if(substr($data[$i]['text'],0,1) == '/')
					$data[$i]['text'] = GW::l($data[$i]['text']);
				
				//get field captions
				if(isset($data[$i]['field']))
					$data[$i]['field_title'] = $this->fh()->fieldTitle($data[$i]['field']);
			}
		}
		
		$this->sess['messages'] = Null;

		//copy errors
		foreach ($data as $key => $item)
			if ($item['type'] == 2)
				$this->errors[$key] = $item;

		return $data;
	}


	

	function fatalError($message)
	{
		$this->setError($message);

		$path_info = Array();

		$path_info['module'] = 'default';
		$path_info['path'] = Array('default');
		$path_info['dirname'] = 'default';

		$this->processModule($path_info, []);

		exit;
	}

	function process()
	{

		if (!$this->canAccess($this->page))
			if ($this->user)
				$this->jumpToFirstChild();
			else
				$this->jump(GW::s("$this->app_name/PATH_LOGIN"));



		$path_info = $this->getModulePathInfo($this->path);
		
		

		$this->processModule($path_info, $_REQUEST);
	}

	/**
	 * 
	 * @return FH
	 */
	function FH()
	{
		$fh = GW::getInstance('FH');

		if (!$fh->app)
			$fh->app = $this;

		return $fh;
	}
}
