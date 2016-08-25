<?php

class GW_Module
{
	/**
	 *
	 * @var GW_Admin_Application
	 */
	public $app;
	
	public $db;
	public $action_name;
	public $view_name;
	public $tpl_dir;
	public $module_dir;
	public $module_name;
	public $module_path;
	public $smarty;
	
	public $errors=[];
	public $errorMsgs=[];
	public $ob_collect=true;
	public $error_fields=[];
		
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
	public $default_view='default';
	public $_args=[];//data passed from application params, request_params
	
	
	function viewModInfo()
	{
		$info = Array
		(
			'module_name'=>$this->module_name,
			'module_path'=>$this->module_path,
			'module_dir'=>$this->module_dir,
			'tpl_dir'=>$this->tpl_dir,
			'action'=>$this->action,
			'list_params'=>$this->list_params,
			'error_fields'=>$this->error_fields,
			'session'=>$_SESSION
		);
		
		if($this->app->user->isRoot())
		{
			$secretactions = ['doResetListVars'];
			
			foreach($secretactions as $key => $act)
			{
				$tmp = $this->buildUri(false,['act'=>$act]);
				$secretactions[$key] = "<a href='$tmp'>$act</a>";
			}
			
			d::ldump($info);
			d::ldump($secretactions);
		}
	}
	
	function doResetListVars()
	{
		if($this->app->user->isRoot())
		{		
			foreach($this->app->sess as $key => $data)
			{
				if(strpos($key, $this->module_path[0])===0)
				{
					unset($this->app->sess[$key]);
				}
			}
		}
		
		$this->jump();
	}
		
	function init()
	{
		$this->db =& $this->app->db;
		$this->tpl_dir="{$this->module_dir}tpl/".$this->module_name."/";

		$this->smarty = $this->app->smarty;
				
		GW_Lang::$module = $this->module_path[0];
		$this->lang = GW::l('/m/');
		
		
		$this->__processViewSolveViewName();
		
		$this->loadErrorFields();
		
		
		
		
		
		$this->initListParams();
		
		$this->tpl_vars['messages'] =& $this->messages;
	}
	
	function initListParams($modulepath=false,$viewname=false)
	{	
		if(!$modulepath)
			$modulepath=implode('/',$this->module_path);
		
		if(!$viewname)
			$viewname=$this->view_name;
		
		$sess_store =& $this->app->sess["$modulepath/$viewname"];
		
		if(!$sess_store)
			$sess_store=[];
		
		$this->list_params = array_merge($this->list_params, $sess_store);
		
		$sess_store = $this->list_params;
		$this->list_params =& $sess_store;		
	}
	
	function doSetListParams()
	{
		if(isset($_GET['list_params']) && ($tmp = $_GET['list_params']))
			$this->list_params = array_merge($this->list_params, $tmp);
		
		
		unset($_GET['list_params']);
		$this->jump();
			
	}
	
	function methodExists($name)
	{
		return method_exists($this,$name);
	}

	function isPublic($name)
	{
		return (stripos($name,'view')===0 || stripos($name,'do')===0) && $this->methodExists($name);
	}
 
	
	

	
	function loadErrorFields()
	{		

		
		
                if(!isset($this->app->sess['messages']))
                    return;
                
		foreach((array)$this->app->sess['messages'] as $msg)
		{
			if($msg["type"]==2 && isset($msg["field"]))
				$this->error_fields[$msg["field"]] = $msg["field"];
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
			$this->setError("Invalid action: \"$act\"");
			//$this->processView();
		}
		
	}


	
	function __processViewSolveViewName()
	{
		$params = $this->_args['params'];
		
		
		//jei paduodama 100/form/abc - 100 kontekstinio objekto id, form - viewsas, abc viewso paramsas
		while(isset($params[0]) && is_numeric($params[0]))
			$erase=array_shift($params);
		
		//nuimti pirmus paramsus kurie yra number
		//nuimta paramsa pastatyti kaip kontekstini objekto id
		
		$name = self::__funcVN(isset($params[0]) ? $params[0] : false);
		
		$this->view_name = $name;
		
		if(!$this->isPublic("view{$name}"))
			$this->view_name = $this->default_view;	
		
	}
	
	function processView($name='',$params=[])
	{
		$this->ob_start();
		
		if($name)
			$this->view_name = $name;
		
		$vars = $this->{"view{$this->view_name}"}($params);
						
		if(is_array($vars))
			foreach($vars as $varname => $var)
				$this->tpl_vars[$varname] =& $vars[$varname];
		
		$this->ob_end();

		$this->processTemplate();
	}
	
	
	function process()
	{
		extract($this->_args);
		
		if(isset($request_params['act']) && ($act=$request_params['act']))
		{
			$this->process_act($act);
			
			if(isset($request_params['just_action'])) //prevent from displaying view
				return true;
		}
		
		
		$this->processView($this->view_name, array_splice($params,1));
	}
	
	
	function getTemplateName()
	{
		$file = $this->tpl_file_name ? $this->tpl_file_name : $this->tpl_dir.$this->view_name;

		
		if(file_exists($tmp = $file.'.php')){
			include $tmp;
		}else{
			//iesko modulio tpl kataloge
			if(!file_exists($tmp = $file.'.tpl'))
				//ieskoti default kataloge
				if(!file_exists($tmp = GW::s("DIR/".$this->app->app_name."/MODULES")."default/tpl/".$this->view_name.'.tpl'))
					$tmp='default_empty.tpl';
					
		}
		
		return $tmp;
	}
	
	function processTemplate($soft=1)
	{
		
		$this->fireEvent("BEFORE_TEMPLATE");
		
		$this->smarty->assign('m', $this);
		$this->smarty->assign($this->tpl_vars);
		
		

		$tpl_name = $this->getTemplateName();
				
		$this->smarty->display($tpl_name);
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
		
		
		if(isset($_GET['return_to']))
			$path = $_GET['return_to'];
		
		if(isset($_REQUEST['dialog_iframe'])){
			echo "<script>window.parent.gwcms.dialogClose();</script>";
			exit;
		}
		
		$this->app->jump($path, $params);
	}
	
	function doSetFilters()
	{		
		$formatedfilters = [];
		
		foreach($_REQUEST['filters']['vals'] as $fieldname => $filters)
			foreach($filters as $idx => $value)
				$formatedfilters[] = [
					'field'=>$fieldname, 
					'value'=>$value, 
					'ct'=>$_REQUEST['filters']['ct'][$fieldname][$idx]
				];
		
				
		$this->list_params['filters'] = isset($_REQUEST['filters_unset']) && $_REQUEST['filters_unset']  ? [] : $formatedfilters;
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

	function lang()
	{
		if(isset($_GET['lang']))
			return $_GET['lang'];
		
			
		return $this->app->ln;
	}
	
	function buildPath($params=[])
	{
		if(isset($params['modulepath']))
		{
			$params['path']=implode('/',$this->module_path).'/'.$params['modulepath'];
			unset($params['modulepath']);
		}
		
		
		
		return $this->app->fh()->gw_path($params);
	}
	
	function buildUri($path=false,$getparams=[], $params=[])
	{
		
			
		
		if(!isset($params['level']))
			$params['level']=2;
		
		
		if($path==false && !isset($getparams['act']) && isset($getparams['return_to'])) {
			$path=$getparams['return_to'];
		} else {
			if($params['level']==2)
			{
				$path = implode('/',$this->module_path) . ($path?'/':''). $path;
			}elseif($params['level']==1){
				$path = $this->module_path[0] . ($path?'/':''). $path;
			}
		}
		
		$params['carry_params'] = 1;
		
		/*
		if(isset($params['relative_path']))
		{
			d::ldump([$this->module_path,$path, $params['relative_path']]);
			//pvz jeigu path = sitemap/pages/15 o relative_path = 10/form
			//padaryt sitemap/pages/10/form			
			$tmp = is_numeric(pathinfo($params['relative_path'], PATHINFO_FILENAME)) ? dirname($params['relative_path']) : $params['relative_path'];

			// extend path
			$path = $tmp . '/' . $path;			
		}	
		*/
		
		
		
		return $this->app->buildURI($path, $getparams, $params);
	}
	
	function getPagingData()
	{
		$query_info = $this->tpl_vars['query_info'];
		$params = $this->list_params;

		$current=(int)$params['page'] ? (int)$params['page'] : 1;
		$length=ceil($query_info['item_count'] / $params['page_by']);

		if($length<2)
			return;
		
		return [
			'current'=>$current,
			'length'=>$length,
			'first'=> $current < 2 ? 0 : 1,
			'prev'=>  $current <= 1 ? 0 : $current-1,
			'next'=>  $current >= $length ? 0 : $current+1,
			'last'=>  $current >= $length   ? 0 : $length,
		];			
	}
	
	/**
	 * to mark ordered fields in orders row each field must be in group with ASC or DESC
	 * exmpl: type ASC, group_id ASC, status DESC
	 */
	
	function calcOrder($name)
	{
		
		$order = $this->list_params['order'];
		$orders = explode(', ',$this->list_params['order']);		
		$multiorder_index = 0;


		$variants1=Array('desc','asc');

		foreach(explode(',', $name) as $iname)
		{
			$variants[0].=($variants[0]?',':'')."$iname ASC";
			$variants[1].=($variants[1]?',':'')."$iname DESC";
		}

		if($tmp=array_intersect($orders, $variants)) {
			foreach($tmp as $index => $ordercopy) {
				$multiorder_index = $index+1;
			}

			$order = $ordercopy;
		}

		$param = $variants[$tmp = intval(strpos($order, 'DESC')===false)];
		$curr_dir = $variants1[$tmp];


		return 
		[
			'uri'=> Navigator::buildURI(false, ['act'=>'do:setOrder','order'=>$param] ),
			'current'=>in_array($order, $variants) ? $curr_dir : false,
			'multiorder'=>count($orders) > 1 ? $multiorder_index : false
		];		
	}
	
	
	function setError($text)
	{
		$this->setMessage(["text"=>$text,"type"=>GW_MSG_ERR]);
	}
	
	
	function setMessage($message)
	{
		if ($this->sys_call) {
			$this->lgr->msg(json_encode($message));
		} else {
			$this->app->setMessage($message);
		}
		
		$this->loadErrorFields();
	}
	
	function setPlainMessage($text, $type=GW_MSG_SUCC)
	{
		$this->setMessage(["text"=>$text, "type"=>$type]);
	}

	function setItemErrors($item)
	{
		foreach($item->errors as $field => $error)
			$this->setMessage(["text"=>$error,"type"=>GW_MSG_ERR, "field"=>$field]);		
	}
		
}

