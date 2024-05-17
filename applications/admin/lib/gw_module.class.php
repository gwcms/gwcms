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
	public $sys_call = false;

	
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
				
		$moduleroot = GW_Lang::$module = $this->module_path[0];
		$this->module_path_clean = $moduleroot != $this->module_name ? "$moduleroot/{$this->module_name}" : $moduleroot;
		
		$this->lang = GW::l('/m/');
		
		
		$this->__processViewSolveViewName();
		
		$this->loadErrorFields();
		
		
		
		
		
		$this->initListParams();
		
		$this->tpl_vars['messages'] =& $this->messages;
	}
	
	/***
	 * Užkrauna peržiūros parametrus išsaugotus sesijoje
	 */
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
		
		
		if(isset($_GET['nopview']))
			return false;
		
		if(isset($this->list_params['pview']) && $this->list_params['pview'])
			$this->list_config['pview'] = GW_Adm_Page_View::singleton()->createNewObject($this->list_params['pview'], true);
	}
	
	function doSetListParams()
	{
		if(isset($_REQUEST['list_params']) && ($tmp = $_REQUEST['list_params'])){
			
	
			if(isset($tmp['search']) && isset($this->list_params['page']) && $this->list_params['page']!=1)
				$this->list_params['page']=1;
			
			if(isset($tmp['page_by']))
				$this->list_params['page']=1;			
			
			$this->list_params = array_merge($this->list_params, $tmp);
		}
				
		$this->jump();	
	}
	
	function methodExists($name)
	{
		return method_exists($this,$name);
	}

	function isPublic($name)
	{
		return (stripos($name,'view')===0 || stripos($name,'do')===0);
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
	
	function processActionBG($act)
	{
		
		if($_GET['background']==1){
			$taskparams = [
			    'act'=>$act, 
			    'title'=> $_GET['background_title']??GW::l('/m/VIEWS/'.$act),
			    'expire'=>'+5 minutes'
			];
			
			if(isset($_GET['background_duration']))
				$taskparams['expected_duration']=$_GET['background_duration'];			
			
			if(method_exists($this, $act.'_BackgroundParams'))
				$this->{$act.'_BackgroundParams'}($taskparams);
			

			
			$bgtask = new GW_Background_Task($taskparams);
			$this->app->sess['bgtasks'][$bgtask->id] = $bgtask;
			
			//neimplementinta tokio kol kas
			GW_WebSocket_Helper::notifyUser(
				$this->app->user->username, 
				[
				    'action'=>'bgtask_open',
				    'bgtaskid'=>$bgtask->id, 
				    'starttime'=>time(), 
				    'title'=>$taskparams['title'],
				    'expectedDuration'=>$bgtask->expected_duration
				]
			);

			$args = ['background'=>2, 'bgtaskid'=>$bgtask->id, 'GWSESSID'=>session_id()];
			
			//isimt app user id is sessijos jau eina
			
			Navigator::backgroundRequest($this->app->buildUri(false, $_GET), $args);

			$this->jump();
		}elseif($_GET['background']==2){
			//vykdymas fone uzdarom sesija kad nelockinti procesu
			$this->app->sessionWriteClose();
			
			$this->processActionNC($act);
			
			$this->app->reopenSessionIfClosed();
			unset($this->app->sess['bgtasks'][$_GET['bgtaskid']]);

			GW_WebSocket_Helper::notifyUser($this->app->user->username, ['action'=>'bgtask_close','bgtaskid'=>$_GET['bgtaskid']]);	
			
			if(isset($_GET['id']))
				$this->notifyRowUpdated($_GET['id']);
			exit;
		}		
	}
	
	function notifyRowUpdated($id, $nottype_ws=true, $user=false)
	{
		$packet = ['action'=>'update_row','id'=>$id, 'context'=>get_class($this->model)];
		
		if($nottype_ws){
			$user = $user ? $user : $this->app->user->username;
			$this->packetWS(false, $packet, $user);
		}else{
			$this->app->addPacket($packet);
		}
	}
	function packetWS($action, $packet, $user=false)
	{		
		$user = $user ? $user : $this->app->user->username;
		if($action)
			$packet['action'] = $action;
		
		GW_WebSocket_Helper::notifyUser($user, $packet);
	}	
	
	// process action (no check)
	function processActionNC($act)
	{
		$this->action_name=$act;
		
		$this->ob_start();
		$this->$act();
		$this->ob_end();

		

		if($this->isPacketRequest()){
			$this->app->outputPackets(true);
			exit;
		}
	}
	  
	function processAction($act)
	{
		$name=$this->__funcVN($act);

		// && method_exists($this,$funcName)
		if($this->isPublic($name))
		{
			if(isset($_GET['background'])){
				$this->processActionBG($name);
			}else{
				$this->processActionNC($name);
			}
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
		while(isset($params[0]) && $this->app->isItemIdentificator($params[0]))
			$erase=array_shift($params);
		
		//nuimti pirmus paramsus kurie yra number
		//nuimta paramsa pastatyti kaip kontekstini objekto id
		
		$name = self::__funcVN(isset($params[0]) ? $params[0] : false);
		
		if(!$name)
			$name = $this->default_view;		
		
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

		$t = new GW_Timer;
		$res = $this->processTemplate(false, $params['return_as_string'] ?? false);
		GW::$globals['debug']['module_process'][$this->module_name.'_template'] = $t->stop(3);
			
		return $res;
	}
	
	
	function process()
	{
		$t = new GW_Timer;
			
		extract($this->_args);
				
		
		if(isset($request_params['act']) && ($act=$request_params['act']))
		{
			$this->processAction($act);
			
			if(isset($request_params['just_action'])) //prevent from displaying view
				return true;
		}
		
		
		//NEKRAUT VIEWSU JEI SYSCALL
		if(isset($request_params['act']) && isset($_GET['sys_call']))
			return false;
		
		if($this->view_name)
			$this->processView($this->view_name, array_splice($params,1));
		
		GW::$globals['debug']['module_process'][$this->module_name] = $t->stop(3);
	}
	
	
	function getTemplateName()
	{
		$file = $this->tpl_file_name ? $this->tpl_file_name : $this->tpl_dir.$this->view_name;

		
		if(file_exists($tmp = $file.'.php')){
			include $tmp;
		}else{
			//iesko modulio tpl kataloge
			if(!file_exists($tmp = $file.'.tpl')){

				if(isset($this->default_tpl_file_name) && file_exists(($dflt=$this->default_tpl_file_name.".tpl")))
					return $dflt;
				
				//ieskoti default kataloge
				if(!file_exists($tmp = GW::s("DIR/".$this->app->app_name."/MODULES")."default/tpl/".$this->view_name.'.tpl'))
					$tmp='default_empty.tpl';
			}
					
		}
		
		return $tmp;
	}
	
	function processTemplate($fromstring=false, $tostring=false)
	{
		
		$this->fireEvent("BEFORE_TEMPLATE");
		
		$this->smarty->assign('m', $this);
		$this->smarty->assign($this->tpl_vars);
		
		if($fromstring){
			return $this->smarty->fetch('string:' . $fromstring);
		}else{
			$tpl_name = $this->getTemplateName();

			if($tostring)
				return $this->smarty->fetch($tpl_name);
			else
				$this->smarty->display($tpl_name);
			
		}
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
		if (isset($_REQUEST['RETURN_TO']) && ($tmp = $_REQUEST['RETURN_TO']))
			return die(header('Location: ' . $tmp));
		
		
		if(Navigator::isAjaxRequest()){
			if(isset($_GET['id']))
				$this->notifyRowUpdated($_GET['id'], false, false);
				
			
			$this->app->outputPackets();
		}
		
		if(isset($_GET['json'])){
			
			$this->app->outputPackets(true);
			exit;
		}
		
		
		if(isset($_GET['background']) && $_GET['background']==2)
			return false;
		
		
		//this thing allows to see last eddited element in list
		
		if(method_exists($this, 'getCurrentItemId') && $this->getCurrentItemId())
			$params['id']=$this->getCurrentItemId();
		
		
		if(isset($_GET['return_to']))
			$path = $_GET['return_to'];
		
		if(isset($_REQUEST['dialog_iframe']) && !($this->dialog_iframe_errors ?? false)){
			echo "<script>window.parent.gwcms.dialogClose();window.parent.gwcms.close_dialog2()</script>";
			exit;
		}
		
		
		
		if($debug = ($this->app->sess['debug'] ?? false)){
			$url = $this->app->jump($path, $params, ['return_url'=>1]);
			
			d::ldump("<span style='color:red'>Youre in debug mode so jump disabled click manualy jump link here: <a href='$url'>$url</a></span>");
			
			return false;
		}
		
		$this->app->jump($path, $params);
	}
	
	function doSetFilters()
	{		
		$this->list_params['filters'] = [];
		$filts = $_REQUEST['filters'] ?? [];
		
	
				
		if(! (isset($_REQUEST['filters_unset']) && $_REQUEST['filters_unset']) ) //if unset is passed skip setting
			foreach($filts['vals'] as $field => $filters)
				foreach($filters as $idx => $value)
					$this->setFilter($field, $value, isset($filts['ct'][$field][$idx]) ? $filts['ct'][$field][$idx] : 'EQ');
		
		$this->list_params['page']=0;
		$this->jump();
	}
	
	function doSetSingleFilter()
	{
		$this->list_params['filters'] = [];
		$this->setFilter($_GET['field'], $_GET['value'], ($_GET['ct'] ?? 'EQ'));
		$this->list_params['page']=0;
		$this->jump();		
	}
	
	function getFiltersByField($field)
	{
		$foundfilters = [];
		
		if(isset($this->list_params['filters'])){
			foreach($this->list_params['filters'] as $filter)
				if($filter['field']==$field){
					$foundfilters[]=$filter['value'];
				}
		}
		
		return $foundfilters;
	}
	
	/**
	 * if $comparetype = IN value must be json_encoded
	 */
	function setFilter($field, $value, $comparetype='EQ', $opts=[])
	{	
		if(!$value || ($comparetype=="IN" && $value=='null'))
			return false;
			
		$this->list_params['filters'][] = $opts+[
					'field'=>$field, 
					'value'=>$value, 
					'ct'=>$comparetype
				];
	}
	
	function getFilterByFieldname($fieldname)
	{
		if(isset($this->list_params['filters']))
			foreach($this->list_params['filters'] as $idx => $filterdata)
				if($filterdata['field'] == $fieldname)
					return ['data'=>$filterdata, 'index'=>$idx];
	}
	
	function replaceFilter($field, $value, $comparetype='EQ')
	{
		if($filt=$this->getFilterByFieldname($field))
				unset($this->list_params['filters'][ $filt['index'] ]);
			
		$this->setFilter($field, $value, $comparetype);
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
				call_user_func($callback,$context);
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
		
		$params['page_by'] = max(1, (int)$params['page_by']);

		$current=(int)$params['page'] ? (int)$params['page'] : 1;
		$length=ceil((int)$query_info['item_count'] / $params['page_by']);

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
			'order'=> $param,
			'current'=>in_array($order, $variants) ? $curr_dir : false,
			'multiorder'=>count($orders) > 1 ? $multiorder_index : false
		];		
	}
	
	
	function setError($message, $addopts=[])
	{
		$this->setMessageEx(["text"=>$message, "type"=>GW_MSG_ERR]+$addopts);
	}
	
	
	function isPacketRequest()
	{
		return isset($_REQUEST['packets']) && $_REQUEST['packets']==1;
	}
		
	function setMessageEx($opts=[])
	{
		
		if(isset($opts['params']))
		{
			$str = "";
			foreach($opts['params'] as $key => $val)
			{
				$str.=GW::l($key).': <i>'.htmlspecialchars($val).'</i><br/>';
			}
			$opts['footer']=$str;
		}		
		
		if(isset($_GET['bgtaskid']))
		{
			$this->app->reopenSessionIfClosed();
			
			$task = $this->app->sess['bgtasks'][$_GET['bgtaskid']];
			$opts['title']=$task->title;
			$opts['bgtaskid']=$_GET['bgtaskid'];
	
			$this->packetWS('notify', $opts);
			
			return true;
		}
		
				
		if ($this->sys_call) {
			if($this->lgr)
				$this->lgr->msg(json_encode($opts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
			
			$this->app->setMessage($opts+['sysmsg'=>1]);
		} else {
			
			if($this->isPacketRequest()){
				if(isset($_GET['id']))
					$packet['id'] = $_GET['id'];
				if(is_string($opts['text']) && $opts['text'][0]=='/')
					$opts['text'] = GW::l($opts['text']);
				
				$this->app->addPacket(['action'=>'notification'] + $opts);
			}else{
				$this->app->setMessage($opts);
			}
		}
				
		$this->loadErrorFields();		
	}
	
	function setMessage($message, $addopts=[])
	{
		$opts = is_array($message) ? $message : ["text"=>$message, "type"=>GW_MSG_SUCC];
		$this->setMessageEx($opts+$addopts);
	}
	
	function setPlainMessage($text, $type=GW_MSG_SUCC)
	{
		$opt = ["text"=>$text, "type"=>$type];
		if($type==GW_MSG_SUCC)
			$opt['float'] = 1;
		
		$this->setMessageEx($opt);
	}

	function setItemErrors($item)
	{
		foreach($item->errors as $field => $error){
			$error = is_array($error) ? $error : ["text"=>$error];
			$error['type']=GW_MSG_ERR;
			$error['field']=$field;
			
			if($item->title)
				$error['title']=$item->title;
						
			$this->setMessage($error);
		}	
	}
	
	public $dynamicFieldTitles=[];
		
	function fieldTitle($field)
	{

		if(isset($this->dynamicFieldTitles[$field]))
			return $this->dynamicFieldTitles[$field];
		
		if(strpos($field,'/')!==false)
		{
			// user/name -> name
			$field = explode('/', $field);
			$field = $field[count($field)-1];//return last section
		}
		
		$title= GW::l($fkey = '/A/FIELDS/' . $field);
		return $title != $fkey ? $title : $field;
	}
	
	function shortFieldTitle($field){
		if(isset($this->dynamicFieldTitles[$field]))
			return $this->dynamicFieldTitles[$field];		
		
		return $this->app->FH()->shortFieldTitle($field);
	}
	
	function runActInBackground()
	{
		if($this->sys_call){
			return true;
		}else{
			Navigator::backgroundRequest($this->app->buildUri(false, $_GET));
			$logfile = basename($this->lgr->file);
			$this->setMessage("<iframe src='/admin/".$this->app->ln."/system/logwatch/iframe?id={$logfile}&padding=1' style='width:100%;height:200px;'></iframe>");
			$this->jump();
			return false;			
		}		
	}
}

