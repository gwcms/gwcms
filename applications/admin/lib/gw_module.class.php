<?php

class GW_Module
{
	public $app;
	
	public $db;
	public $action_name;
	public $tpl_dir;
	public $module_dir;
	public $module_name;
	public $module_path;
	public $smarty;
	
	public $errors=[];
	public $errorMsgs=[];
	public $ob_collect=true;
	public $error_fields;
		
	/**
	 * @var GW_Request
	 */
	
	public $list_params=['page_by'=>20];
	public $log=[];
	
	/**
	 * specify template file path (without extension)
	 * @var string  
	 */
	public $tpl_file_name;
	public $tpl_vars;
	public $default_view='viewDefault';
	
	
	function getInfo()
	{
		return Array
		(
			'module_name'=>$this->module_name,
			'module_path'=>$this->module_path,
			'module_dir'=>$this->module_dir,
			'tpl_dir'=>$this->tpl_dir,
			'action'=>$this->action,
			'list_params'=>$this->list_params,
			'error_fields'=>$this->error_fields,
		);
	}
		
	function init()
	{
		$this->db =& $this->app->db;
		$this->tpl_dir="{$this->module_dir}tpl/".$this->module_name."/";

		$this->smarty = $this->app->smarty;
		
		$this->lang = GW_Lang_XML::load("{$this->module_dir}lang.xml", $this->app->ln);
		
		$this->loadErrorFields();
		$this->initListParams();
		
		$this->tpl_vars['messages'] =& $this->messages;
	}
	
	function initListParams()
	{
		$sess_store =& $_SESSION[implode('/',$this->module_path)];
		
		if(!$sess_store)
			$sess_store=[];
		
		$this->list_params = array_merge($this->list_params, $sess_store);
				
		if(isset($_GET['list_params']))
			die('LIST_PARAMS_DEPRECATED');		
		
		if(isset($_GET['list_params']) && ($tmp = $_GET['list_params']))
			$this->list_params = array_merge($this->list_params, $tmp);
			
		$sess_store = $this->list_params;
		$this->list_params =& $sess_store;
		
		if(isset($_GET['list_params']) && $_GET['list_params'])
		{
			unset($_GET['list_params']);
			$this->jump();
		}
	}
	
	function methodExists($name)
	{
		return method_exists($this,$name);
	}

	function isPublic($name)
	{
		return (stripos($name,'view')===0 || stripos($name,'do')===0) && $this->methodExists($name);
	}
 
	/*
	 * use error key, to declare error field id.
	 * example
	 * Array
	 * (
	 * 		'email'=>'Invalid email',
	 * 		'password'=>'Too short'
	 * )
	 * */
	function setErrors($errors, $level=2)
	{
		$this->app->setErrors($errors, $level);			
		$this->loadErrorFields();
	}
	
	function loadErrorFields()
	{		
                if(!isset($_SESSION['messages']))
                    return;
                
		foreach((array)$_SESSION['messages'] as $field => $error)
		{
			if($error[0]===2)
				$this->error_fields[$field]=$field;
		}
	}	
	
  
	  
	function process_act($act)
	{
		$name=$this->__funcVN($act);

		// && method_exists($this,$funcName)
		if($this->isPublic($name))
		{
			$this->ob_start();
			$this->$name();
			$this->ob_end();

			$this->action_name=$name;
		}
		else
		{
			$this->setErrors("Invalid action: \"$act\"");
			$this->processView();
		}
		
	}


	
	function processView($name='',$params=[])
	{
		$this->ob_start();

		$this->isPublic($name="view$name") || $name=$this->default_view;
		$this->action_name=$name;

		$vars = $this->$name($params);
						
		if(is_array($vars))
			foreach($vars as $varname => $var)
				$this->tpl_vars[$varname] =& $vars[$varname];
		
		$this->ob_end();

		$this->processTemplate();
	}
	
	
	function process($params=Array(), $request_params=[])
	{
		if(isset($request_params['act']) && ($act=$request_params['act']))
		{
			$this->process_act($act);
			
			if(isset($request_params['just_action'])) //prevent from displaying view
				return true;
		}
		
		$params=(array)$params;
		$this->processView(self::__funcVN(
			isset($params[0]) ? $params[0] : false), 
			array_splice($params,1)
			);
	}
	
	
	function processTemplate($soft=1)
	{
		
		$this->smarty->assign('m', $this);
		$this->smarty->assign($this->tpl_vars);
		
		
		//d::dumpas(array_keys($this->tpl_vars));
		
		if($this->tpl_file_name)
		{
			$file = $this->tpl_file_name;
		}else{
			$basename=preg_replace('/^view|do/','',strtolower($this->action_name));
			$file=$this->tpl_dir.$basename;
		}
		

		if(file_exists($tmp = $file.'.php')){
			include $tmp;
		}else{
			
			//iesko modulio tpl kataloge
			if(!file_exists($tmp = $file.'.tpl'))
				//ieskoti default kataloge
				if(!file_exists($tmp = GW::s("DIR/".$this->app->app_name."/MODULES")."default/tpl/".$basename.'.tpl'))
					$tmp='default_empty.tpl';
					
		}
				
		$this->smarty->display($tmp);
	}
	


	function __funcVN($str)
	{
		if(!$str)
			return; // jei tuscias stringas pasidarys 1 simbolio stringas
					
		//valid method name
		$str=preg_replace('/[^a-z0-9]/i', '_', $str);
		

		$str[0]=preg_replace('/[^a-z]/i', '_', $str[0]);
		
		return strtolower(str_replace('_','',$str));
	}

	function ob_start()
	{
		if(!$this->ob_collect)
			return;
			
		$this->tpl_vars['log'] =& $this->log;

		ob_start();
	}

	function ob_end()
	{
		if(!$this->ob_collect)
			return;

		$this->log[] = ob_get_contents();
		
		ob_end_clean();
	}
	
	function jump($path=false, $params=[])
	{
		//this thing allows to see last eddited element in list
		
		if(method_exists($this, 'getCurrentItemId') && $this->getCurrentItemId())
			$params['id']=$this->getCurrentItemId();
		
		$this->app->jump($path, $params);
	}
	
	function doSetFilters()
	{
		$this->list_params['filters'] = $_REQUEST['filters_unset'] ? [] : $_REQUEST['filters'];
		$this->list_params['page']=0;
				
		$this->jump();
	}	
	
	function fireEvent($event, &$context=false)
	{
		if(!is_array($event))
			$this->EventHandler($event, $context);
		else
			foreach($event as $e)
				$this->EventHandler($e, $context);
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
		if(method_exists($this, $tmp))
			$this->$tmp($context);
		
		//pass deeper
		//parent::eventHandler($event, $context);
	}

	function lang()
	{
		if(isset($this->args['lang']))
			return $this->args['lang'];
			
		return $this->app->ln;
	}	
	
}

