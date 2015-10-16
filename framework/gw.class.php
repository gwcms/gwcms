<?php

/**
 * 
 * @author wdm
 *
 * GateWay CMS namespace
 * 
 */

/* GW Context*/
class GW_Context
{
	public $vars;
	
	function __set($var, $value)
	{
		$this->vars[$var]=$value;
	}
	
	function &__get($var)
	{
		return $this->vars[$var];
	}
}

class GW
{
	//nekintantys parametrai per visas aplikacijas
	static $settings;
	static $error_log;
	static $context;

	
	function db()
	{
		if(!self::$context->db)
			self::$context->db = new GW_DB();
		
		return self::$context->db;
	}
	
	static function initClass($name)
	{
		$o = new $name();
		$o->db = self::$context->db;
		
		return $o;
	}
	
	static function getInstance($class, $file=false) 
	{
		static $cache;
		
		if( isset($cache[$class]) )
			return $cache[$class];
			
		if($file)
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
	}

	function request($args=Array())
	{		
		if(!isset($args['path']))
			$args['path'] = isset($_GET['url'])?$_GET['url']:'';
		
		if(!isset($args['args']))
			$args['args'] = $_GET+$_POST;
		
		$path_arr = explode('/',$args['path']);
			
		if(isset($path_arr[0]) && $path_arr[0] && is_dir(GW::s('DIR/APPLICATIONS').''.$path_arr[0]))
		{
			$app = strtoupper(array_shift($path_arr));
			$base_path = strtolower($app).'/';
		}else{
			$base_path = '';
			$app = GW::s('DEFAULT_APPLICATION');
		}
		
		$context = Array(
		    'path_arr'=>$path_arr,
		    'app_base'=>$base_path, 
		    'args'=>$args['args'],
		    'sys_base'=>Navigator::getBase()
		);	
		
		$app_class = "GW_{$app}_application";
		include GW::s('DIR/APPLICATIONS').strtolower($app).DIRECTORY_SEPARATOR.strtolower($app_class).'.class.php';
		
		
		$app_o = new $app_class($context);
		self::$context->app = $app_o;
		
		$app_o->app_name = $app;
		
		$app_o->init();
		$app_o->process();
	}
	
	
	
	
	static function &s($var_name, $value=Null)
	{
		$var  =& self::$settings;
		$explode = explode('/',$var_name);

		foreach($explode as $part)
			$var =& $var[$part];
		
		if($value!==Null)
			$var = $value;
		
		return $var;
	}
	
	/** vertimai
	 * 
	 * @param type $key
	 */
	
	static function l($key)
	{
		return GW_Lang::read($key);
	}
	
	static function ln($key, $valueifnotfound=false)
	{
		static $cache;
		
		list($module, $key) = explode('/', $key, 2);
		$module=  strtolower($module);

		//uzloadinti vertima jei nera uzloadintas
		
		if(!isset($cache[$module]))
		{
			$tr = GW_Translation::singleton()->getAssoc(['key','value_'.GW_Lang::$ln],['module=?', $module]);
			
			foreach($tr as $k => $val){
				
				$var  =& $cache[$module];			
				
				foreach(explode('/', $k) as $kk)
					$var =& $var[$kk];
				
				$var = $val;
			}
				
		}
		
		//paimti vertima is cache
		$explode = explode('/',$key);

		foreach($explode as $part){
			if(isset($var[$part])){
				$var =& $var[$part];
			}else{
				$var=null;
				break;
			}
		}
		
		if($var==Null){
			//jei tokia pat kalba ir verte nerasta ikelti vertima i db
			if($valueifnotfound && strpos($valueifnotfound, GW_Lang::$ln.':')!==false ){
				list($ln, $val) = explode(':', $valueifnotfound, 2);
				$t = GW_Translation::singleton()->createNewObject(['module'=>$module,'key'=>$key, 'value_'.GW_Lang::$ln=>$val]);
				$t->insert();
				
				$var = $val;
			}
		}
			
		
		return $var;		
		
		
			
		
	}
		
}