<?php

class GW_Public_Module
{
	public $module_file;
	public $tpl_dir;
	public $tpl_vars;
	public $module_dir;
	public $lang;
	public $smarty;
	public $errors=Array();
	public $tpl_name;
	public $app;
	public $options;
	public $links;


	function __construct($variables=Array())
	{
		
		foreach($variables as $key => $val)
			$this->$key = $val;
			
		
		$this->module_dir = dirname($this->module_file).'/';
		$this->tpl_dir = $this->module_dir.'tpl/';

		GW_Lang::$module = $this->module_path[0];
		$this->lang = GW::l('/m/');
				
		$this->loadErrorFields();
		
		$this->tpl_vars['options'] =& $this->options;
		$this->tpl_vars['links'] =& $this->links;			
		
	}
	
	function init()
	{
		//nekviecia sitos funkcijos
	}
	
	function processTemplate($name)
	{
		
		$this->fireEvent("BEFORE_TEMPLATE");		
		
		$this->smarty->assignByRef('messages', $this->messages);
		$this->smarty->assign('m', $this);
		$this->smarty->assign($this->tpl_vars);
		
		

		if($this->tpl_name)
			$file=$this->tpl_dir.$this->tpl_name;
		else
			$file=$this->tpl_dir.$name;
		
		if(!file_exists($tmp = $file.'.tpl'))
			die("Template $tmp not found");
			
		$this->smarty->display($tmp);
	}

	function processView($name, $params=Array())
	{
		if($name=='')
			$name="default";
	
		$methodname="view".$name;
		$this->$methodname($params);

		$this->processTemplate($name);
	}


	function doJson()
	{
		$params=$_REQUEST['params'];
		$func='json'.$_REQUEST['function'];

		$result = call_user_func_array(Array($this, $func), $params);

		echo json_encode($result);
		exit;
	}

	function processAction($name)
	{
		if(substr($name,0,2)!='do')
			die('Invalid action name');


		$methodname=$name;
		$this->$methodname();
	}


	/**
	 * Validate view,action name
	 * @param $str
	 * @return unknown_type
	 */
	function __funcVN($str)
	{
		if(!$str)
			return; // jei tuscias stringas pasidarys 1 simbolio stringas
			
		//valid method name
		$str=preg_replace('/[^a-z0-9]/i', '_', $str);


		$str[0]=preg_replace('/[^a-z]/i', '_', $str[0]);

		return strtolower(str_replace('_','',$str));
	}



	function process($params)
	{
		$act_name = self::__funcVN(isset($_REQUEST['act']) ? $_REQUEST['act'] : false);

		
		if(isset($params[0])){
			$view_name = $params[0];
		

			if(!method_exists($this, 'view'.$view_name)){
				$view_name = 'default';
			}else{
				array_shift($params);
			}
		}else{
			$view_name = 'default';
		}
		
		if($act_name)
			$this->processAction($act_name);

		$this->processView($view_name, $params);
	}
	
	function setErrors($errors, $level=2)
	{
		$this->app->setErrors($errors, $level);	

		$this->errors = array_merge($this->errors, (array)$errors);		
		
		$this->loadErrorFields();
	}
	
	function loadErrorFields()
	{		
		if(!isset($_SESSION['messages']))
			return false;
		
		foreach((array)$_SESSION['messages'] as $field => $error)
		{
			if($error[0]===2)
				$this->error_fields[$field]=$_SESSION['messages'][$field][1];
		}
	}	
	
	
	function fireEvent($event, &$context=false)
	{
		if(!is_array($event))
			$this->EventHandler($event, $context);
		else
			foreach($event as $e)
				$this->EventHandler($e, $context);
	}
	
	
	public $__attached_events;
	
	function attachEvent($event, $callback)
	{
		$this->__attached_events[$event][]=$callback;
	}
	
	//overrride me || extend me
	function eventHandler($event, &$context)
	{
		switch($event)
		{
			case 'AFTER_SAVE':
				$item=$context;
			break;
		}
		
		$tmp = '__event'.  str_replace('_', '', $event);
		if(method_exists($this, $tmp)){
			$this->$tmp($context);
		}else{
			//d::dump('method '. $tmp.'notexists');
		}
		
		if(isset($this->__attached_events[$event]))
		{
			foreach($this->__attached_events[$event] as $callback)
				call_user_func ($callback,$context);
		}
		
		
		//pass deeper
		//parent::eventHandler($event, $context);
	}	
		
	
}