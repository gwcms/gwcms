<?php

/**
 * 
 * @author wdm
 *
 * GateWay CMS namespace
 * 
 */
/* GW Context */
class GW_Context
{

	public $vars;

	function __set($var, $value)
	{
		$this->vars[$var] = $value;
	}

	function &__get($var)
	{
		return $this->vars[$var];
	}
}

class GW_Tree_Data_Elm
{

	private $data_link;

	function __construct(&$data_link)
	{
		$this->data_link = & $data_link;
	}

	function __get($name)
	{
		if (isset($this->data_link[$name]) && is_array($this->data_link[$name]))
			return new GW_Tree_Data_Elm($this->data_link[$name]);
		else
			return $this->data_link[$name];
	}
}

class GW_l_Object_Call
{

	public $base = [];

	function __construct($base = false)
	{
		$this->base = $base;
	}

	function __get($name)
	{
		$this->base[] = $name;
		return new GW_l_Object_Call($this->base);
	}

	function __toString()
	{

		$x = GW::l('/' . implode('/', $this->base));

		GW::$l->base = [];

		return $x;
	}
}

class GW
{

	//nekintantys parametrai per visas aplikacijas
	static $settings;
	static $s; //short access to $settings via GW_Tree_Data_Elm
	static $l; //short access to GW::l() via GW_l_Object_Call
	static $error_log;
	static $context;
	//jeigu prisijunges vartotojas developeris
	static $devel_debug;

	/**
	 *
	 * @var GW_Logger
	 */
	static $lgr;

	/**
	 * 
	 * @return GW_DB
	 */
	function db()
	{
		if (!self::$context->db)
			self::$context->db = new GW_DB();

		return self::$context->db;
	}

	static function initClass($name)
	{
		$o = new $name();
		$o->db = self::$context->db;

		return $o;
	}

	static function getInstance($class, $file = false)
	{
		static $cache;

		if (isset($cache[$class]))
			return $cache[$class];

		if ($file)
			include_once $file;

		$cache[$class] = self::initClass($class);

		return $cache[$class];
	}

	function &_($varname)
	{
		return self::$$varname;
	}

	function init()
	{
		self::$context = new GW_Context;
		self::$s = new GW_Tree_Data_Elm(self::$settings);
		self::$l = new GW_l_Object_Call;
		self::$lgr = new GW_Logger(GW::s('DIR/LOGS') . 'system.log');
	}

	
	function fakerequest($path, $user_id)
	{
		$_POST = array();
		$args = parse_url($path, PHP_URL_QUERY);
		parse_str($args, $args);
		$_GET = $args;
		$_REQUEST = array_merge($_GET, $_POST);
		
		
		$path = parse_url($path, PHP_URL_PATH);
		
		ob_start();

		GW::request(['path'=>$path, 'fake_user_id'=>$user_id]);


		$out2 = ob_get_contents();		
		ob_end_clean();		
		return $out2;
	}
	
	function request($args = Array())
	{
		if (!isset($args['path']))
			$args['path'] = isset($_GET['url']) ? $_GET['url'] : '';

		if (!isset($args['args']))
			$args['args'] = $_GET + $_POST;

		$path_arr = explode('/', $args['path']);

		if (isset($path_arr[0]) && $path_arr[0] && is_dir(GW::s('DIR/APPLICATIONS') . '' . $path_arr[0])) {
			$app = strtoupper(array_shift($path_arr));
			$base_path = strtolower($app) . '/';
		} else {
			$base_path = '';
			$app = GW::s('DEFAULT_APPLICATION');
		}

		$context = Array(
		    'path_arr' => $path_arr,
		    'app_base' => $base_path,
		    'args' => $args['args'],
		    'sys_base' => Navigator::getBase()
		);
		
		$app_class = "GW_{$app}_application";
		include GW::s('DIR/APPLICATIONS') . strtolower($app) . DIRECTORY_SEPARATOR . strtolower($app_class) . '.class.php';


		$app_o = new $app_class($context);
		self::$context->app = $app_o;

		$app_o->app_name = $app;
		
		$app_o->init();
		
		if(isset($args['fake_user_id']))
		{
			self::$context->app->user = GW_User::singleton()->createNewObject($args['fake_user_id'], true);
		}

		
		if (self::$context->app->user)
			self::$devel_debug = self::$context->app->user->isRoot();

		$app_o->process();
	}

	static function &s($var_name, $value = Null)
	{
		$var = & self::$settings;
		$explode = explode('/', $var_name);

		foreach ($explode as $part)
			$var = & $var[$part];

		if ($value !== Null)
			$var = $value;

		return $var;
	}

	/** vertimai
	 * 
	 * @param type $key
	 */
	static function &l($key, $write = null)
	{
		return GW_Lang::readWrite($key, $write);
	}

	
	static function ln()
	{
		return forward_static_call_array(array('GW_Lang', 'ln'), func_get_args());
	}
}
