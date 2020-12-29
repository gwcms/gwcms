<?php

class GW_Public_Module {

	public $module_file;
	public $tpl_dir;
	public $tpl_vars;
	public $module_dir;
	public $lang;
	public $smarty;
	public $errors = Array();
	public $tpl_name;
	public $args = []; 
	public $_args=[];//data passed from application params, request_params	
	
	/**
	 *
	 * @var GW_Site_Application
	 */
	public $app;
	public $options=[];
	public $links;
	// pvz news/list bus modulis/viewsas, news/view/1/images bus - modulis,viewsas o params = [1,'images']
	public $params;
	public $sys_call = false;
	public $cancel_tpl_process = false;
	public $view_name = false;
	public $skipview = false;
	
	function __construct($variables = Array()) {

		foreach ($variables as $key => $val)
			$this->$key = $val;

	
		$this->module_dir = dirname($this->module_file) . '/';
		$this->tpl_dir = $this->module_dir . 'tpl/';

		GW_Lang::$module = $this->module_path[0];
		$this->lang = GW::l('/m/');

		$this->loadErrorFields();

		$this->tpl_vars['options'] = & $this->options;
		$this->tpl_vars['links'] = & $this->links;
		
		
		
	}
	
	function initCommon()
	{
		$this->__processViewSolveViewName();
		$this->solvePageTitle();		
	}

	function init() {
		//nekviecia sitos funkcijos

		$this->initCommon();
	}
	
	function __eventBeforeTemplateAssignTplVars()
	{
		$this->smarty->assignByRef('messages', $this->messages);
		$this->smarty->assign('m', $this);
		$this->smarty->assign($this->tpl_vars);		
	}	

	function processTemplate($name, $fetch=false)
	{		
		$this->fireEvent("BEFORE_TEMPLATE");		
				
		if ($this->tpl_name)
			$file = $this->tpl_dir . $this->tpl_name;
		else
			$file = $this->tpl_dir . $name;
		
		if (!file_exists($tmp = $file . '.tpl'))
			die("Template $tmp not found");
					
		return $this->smarty->{$fetch?'fetch':'display'}($tmp);
	}

	function processView($name, $params = Array()) {		
		if ($name == '')
			$name = "default";

		$p = new stdClass();
		$p->view = $name;
		$p->params = $params;
		$this->fireEvent("BEFORE_VIEW", $p);	
		
		$methodname = "view" . $name;
		
		$vars = $this->$methodname($params);
		
		if(!$this->cancel_tpl_process)
			$this->processTemplate($name);
		
		return $vars;
	}

	function doJson() {
		$params = $_REQUEST['params'];
		$func = 'json' . $_REQUEST['function'];

		$result = call_user_func_array(Array($this, $func), $params);

		echo json_encode($result);
		exit;
	}

	function processAction($name) {
		if (substr($name, 0, 2) != 'do')
			die('Invalid action name');


		$methodname = $name;
		return $this->$methodname();
	}

	/**
	 * Validate view,action name
	 * @param $str
	 * @return unknown_type
	 */
	function __funcVN($str) {
		if (!$str)
			return; // jei tuscias stringas pasidarys 1 simbolio stringas




			
//valid method name
		$str = preg_replace('/[^a-z0-9]/i', '_', $str);


		$str[0] = preg_replace('/[^a-z]/i', '_', $str[0]);

		return strtolower(str_replace('_', '', $str));
	}

	function process($params) {
		$act_name = self::__funcVN(isset($this->args['act']) ? $this->args['act'] : false);
		
		
		if(!$this->view_name)
			$this->__processViewSolveViewName();
		
		$view_name = $this->view_name;
		
		$this->params = [];
		
		//d::dumpas($params);

		
		if ($act_name){
			$this->fireEvent("BEFORE_ACTION", $act_name);
			$vars = $this->processAction($act_name);
			
			if($view_name=="noview"){
				return $vars;
			}
		}
		
		if($this->skipview)
			return false;
		
		return $this->processView($view_name, $params);
	}



	function loadErrorFields() {
                if(!isset($this->app->sess['messages']))
                    return;
                
		foreach((array)$this->app->sess['messages'] as $msg)
		{
			if($msg["type"]==2 && isset($msg["field"]))
				$this->error_fields[$msg["field"]] = $msg["field"];
		}
		
	}

	function fireEvent($event, &$context = false) {
		if (!is_array($event))
			$this->EventHandler($event, $context);
		else
			foreach ($event as $e)
				$this->EventHandler($e, $context);
	}

	public $__attached_events;

	function attachEvent($event, $callback) {
		$this->__attached_events[$event][] = $callback;
	}

	//overrride me || extend me
	function eventHandler($event, &$context) {
		switch ($event) {
			case 'AFTER_SAVE':
				$item = $context;
				break;
			case 'BEFORE_TEMPLATE':
				$this->app->preloadBlocks();
				$this->__eventBeforeTemplateAssignTplVars();
			break;
		}

		$tmp = '__event' . str_replace('_', '', $event);
		if (method_exists($this, $tmp)) {
			$this->$tmp($context);
		} else {
			//d::dump('method '. $tmp.'notexists');
		}

		if (isset($this->__attached_events[$event])) {
			foreach ($this->__attached_events[$event] as $callback)
				call_user_func($callback, $context);
		}


		//pass deeper
		//parent::eventHandler($event, $context);
	}

	function setErrorItem($vals, $name) {
		$this->app->sess['error_item_vals_' . $name] = $vals;
	}

	function getErrorItem($name) {
		if (!isset($this->app->sess['error_item_vals_' . $name]))
			return null;

		$vals = $this->app->sess['error_item_vals_' . $name];
		unset($this->app->sess['error_item_vals_' . $name]);
		return $vals;
	}

	function links($name, $args=[])
	{
		return $this->app->buildUri($this->links[$name],$args,['carry_params'=>1]);
	}

	function buildDirectUri($path = false, $getparams = [], $params = []) {
		if (!isset($params['level']))
			$params['level'] = 2;

		if ($params['level'] == 2) {
			$path = implode('/', $this->module_path) . ($path ? '/' : '') . $path;
		} elseif ($params['level'] == 1) {
			$path = $this->module_path[0] . ($path ? '/' : '') . $path;
		}
		
		return $this->app->buildURI('direct/' . $path, $getparams, $params);
	}

	function getViewPath($view) {
		return $this->app->page->path . '/' . $view;
	}
	
	function setUpPaging($last_query_info) {
		$current = (int) $this->list_params['page'] ? (int) $this->list_params['page'] : 1;
		$length = ceil($last_query_info['item_count'] / $this->list_params['page_by']);

		$this->smarty->assign('paging_tpl_page_count', $length);

		if ($length < 2)
			return;


		$this->smarty->assign('paging', $paging = Array
			(
			'current' => $current,
			'length' => $length,
			'first' => $current < 2 ? 0 : 1,
			'prev' => $current <= 1 ? 0 : $current - 1,
			'next' => $current >= $length ? 0 : $current + 1,
			'last' => $length,
		));
	}	
	
	function setError($text)
	{
		$this->setMessage(["text"=>$text,"type"=>GW_MSG_ERR]);
	}
	
	
	function setMessage($message)
	{
		if(is_string($message))
			$message=["text"=>$message, GW_MSG_INFO];
		
		if(substr($message['text'], 0, 1)=='/'){
			$message['code'] = $message['text'];
			$message['text']=GW::l($message['text']);
		}		
		
		if(is_array($message) && isset($message['vars']))
			GW_String_Helper::replaceVarsInTpl($message['text'], $message['vars']);
		
		
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
	
	function jump($path, $args=[])
	{		
		$this->app->jump($this->app->page->path.'/'.$path, $args);
	}
	
	function buildUri($path, $args=[])
	{
		$path = $this->app->page->path.'/'.$path;
		$path = $this->app->path_arg[0] == 'direct' ? 'direct/'.$path : $path;
		
		return $this->app->buildUri($path, $args,['carry_params'=>1]);
	}
	
	function attachFieldOptions($list, $fieldname, $obj_classname, $options=[])
	{
		$ids = [];
		foreach($list as $itm){
			if($itm->$fieldname)
			$ids[]=$itm->$fieldname;
		}
		
		
		$o = new $obj_classname;
			
		if(!$ids)
			return false;
		
		$cond = GW_DB::inCondition('id', $ids);
		
		if(isset($options['simple_options']))
		{
			$key=$options['simple_options'];
			$this->options[$fieldname] = $o->getAssoc(['id', $key], $cond);
		}else{
			$this->options[$fieldname] = $o->findAll($cond, ['key_field'=>'id']);
		}	
	}		
	
	function getCurrentItemId()
	{
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

		if ($id)
			return $id;
	}	
	
	function getDataObjectById($load = true, $class = false, $access=GW_PERM_READ)
	{
		$id = $this->getCurrentItemId();

		if (!$id)
			return $this->setError('/g/GENERAL/BAD_ARGUMENTS');

		if ($class){
			$item = new $class($id);
		}else{
			
			$item = $this->model->createNewObject($id);
			
			//must be outside of create new object, in case of inheritProps is needed before load
			if($load)
				$item->load();
		}

		if ($load && !$item->loaded)
			return $this->setError('/g/GENERAL/ITEM_NOT_EXISTS');

		$this->canBeAccessed($item, ['access'=>$access]);

		return $item;
	}

	function canBeAccessed($item, $opts=[])
	{
		$result = false;
		
		if($item->id)
			$item->load_if_not_loaded();
		
		$requestAccess = $opts['access'] ?? GW_PERM_WRITE;
				
		if(isset($item->content_base['access']))
		{
			$availAccess = $item->content_base['access'];
			//1-read, 2-write check //admin/config/main.php for permission list
			if ($availAccess & $requestAccess) {
				$result = true;
			}
				
		}else{
			$result = true; //$item->canBeAccessedByUser($this->app->user);
		}

		
		if (isset($opts['nodie']) || $result)
			return $result;

		$this->setError('/G/GENERAL/ACTION_RESTRICTED');
		$this->jump();
	}	
	
	
	
	function jsonResponse($array)
	{
		header('Content-type: text/json');
		
		if(isset($_GET['debug']))
			die(json_encode($array, JSON_PRETTY_PRINT));
		
		$this->skipview = true;
		
		echo json_encode($array);
	}	
	

	function inputFilePreview($name, $onlyids=false)
	{
		$item = $this->app->user;
		
		$files = is_array($item->$name) ? $item->$name : false;
		$file = $files ? false : $item->$name;
		
		$that = $this;
		
		$preview1 = function() use ($that, &$file){
			if($file instanceof GW_Image)
				return "<a href='{$that->app->app_base}tools/img/{$file->key}' target='_blank'><img  src='{$that->app->app_base}tools/img/{$file->key}?size=128x128' /></a>";
			else
				return "<a href='{$that->app->app_base}tools/download/{$file->key}'  target='_blank'><img style='width:50px' src='{$that->app->app_root}assets/img/files/{$file->getIcon('assets/img/files')}' /></a>";
		};
		
		$preview2 = function() use ($that, &$file, &$name){
			$remurl = $this->buildDirectUri('removefile', ['id'=>$file->id,'name'=>$name]);
			
			return ['caption'=>$file->original_filename, 'url'=>$remurl, 'key'=>$file->key];
		};
		
		$initialPreview=[];
		$initialPreviewConfig=[];
		
		if($files){
			
			foreach($files as $file)
			{
				if($onlyids && !in_array($file->id, $onlyids))
					continue;
					
				$initialPreview[]=$preview1();
				$initialPreviewConfig[]=$preview2();
			}
		}else if($file){
			$initialPreview[]=$preview1();
			$initialPreviewConfig[]=$preview2();
		}
		
		
		return json_encode(['initialPreview'=>$initialPreview, 'initialPreviewConfig' => $initialPreviewConfig]);
	}
	
	function viewInputFilePreview()
	{
		echo $this->inputFilePreview($_GET['field']);
		exit;
	}

	function viewUploadFile()
	{
		$item = $this->getDataObjectForFiles();

		foreach ($_FILES as $name => $data) {
			
			
			//leisti pdf uploadint
			if($item->composite_map[$name][0]=='gw_image' && $data['type']=='application/pdf'){
				
				GW_File_Helper::unlinkOldTempFiles(GW::s('DIR/TEMP'));
				
				$newpdf=GW::s('DIR/TEMP').'/test_'.date('Ymd_His').'.pdf';
				$newim=GW::s('DIR/TEMP').'/test'.date('Ymd_His').'.jpg';
				$newim0=GW::s('DIR/TEMP').'/test'.date('Ymd_His').'-0.jpg';
				$log=GW::s('DIR/TEMP').'/log'.date('Ymd_His').'-0.jpg';
				

				copy($data['tmp_name'], $newpdf);
				$out = shell_exec($cmd = "convert -density 150 $newpdf $newim");
				
				file_put_contents($log, $cmd."\n\n".$out);
				
				$_FILES[$name]['tmp_name'] =  file_exists($newim0) ? $newim0 : $newim;
				$_FILES[$name]['name'] =  $_FILES[$name]['name'].'.jpg';
			}
			
			//d::ldump($data['tmp_name']);
			//sleep(60);

			
			
			if (isset($_FILES[$name]) && $item->isCompositeField($name))
				GW_Image_Helper::__setFile($item, $name);


			if (!$item->validate()) {
				foreach ($item->errors as $errorc => $err){
					
					if(is_array($err)){
						$item->errors[$errorc] = GW::ln($err['text']);
					}else{
						$item->errors[$errorc] = GW::ln($err);
					}
				}
				
				die(json_encode(['error' => implode(', ', $item->errors)]));
			} else {
				$item->save();	

				if ($tmp = $this->inputFilePreview($name, $item->saved_composite_ids[$name]??false)) {
					
					//die(json_encode($dat));
					//parodo paskutini tik tai jei atrast kaip padaryt kad atnaujint visus, arba kaip identifikuot ar uploadino ar ne
					die($tmp);
					
					
				} else {
					die(json_encode(['error' => 'Error uploading file']));
				}
			}
		}

		die(json_encode(['errors' => 'File not received']));
	}
	
	function viewRemoveFile() {

		$item = $this->getDataObjectForFiles(); 

		$id = $_GET['id'] ?? false;
		$name = $_GET['name'] ?? false;
		
		//istrins visus failus
		//$item->removeCompositeItem($name, '*');
		
		$item->removeCompositeItem($name, $id);

		echo $this->inputFilePreview($name);
		exit;
		
	}
	
	function userRequired()
	{
		if($this->app->user)
			return;
		
		$this->setMessage([
		    'text'=>GW::ln('/g/PLEASE_LOGIN_TO_CONTINUE'),
		    'type'=>GW_MSG_INFO
		]);
		
		
		$this->app->sess('navigate_after_auth', $_SERVER['REQUEST_URI']);
		
		
		if(method_exists($this, "noUserCame")){
			$this->noUserCame();
		}
		
		
		
		$this->app->jump('direct/users/users/login');				
	}
	
	public $extenions;
	
	function ext($name)
	{
		if(is_array($name)){
			list($name, $ext_name) = $name;
		}else{
			$ext_name = get_class($this).'_'.$name;
		}
		
		if(!isset($this->extensions[$name]))
		{
			$this->extensions[$name] = new $ext_name;
			$this->extensions[$name]->mod = $this;
		}
				
		return $this->extensions[$name];
	}	
	
	
	public $redirRules=[];
	public $ext_events=[]; //add $this->addRedirRule('events', 'myModuleExtension'); and function extEventHander in extension
	
	function addRedirRule($rule, $ext)
	{
		if($rule == 'events')
			$this->ext_events[$ext]=1;
			
		$this->redirRules[]=['re'=>$rule, 'ext'=>$ext];
		
	}
	
	function scanRedirRules($name){
		foreach($this->redirRules as $rule)
			if(preg_match($rule['re'], $name, $m))
				return $rule['ext'];
	}
	
	
	function methodTest($name)
	{
		return method_exists($this, $name) || $this->scanRedirRules($name);
	}
	
	function __call($name, $arguments)
	{
		$name = strtolower($name);
		
		if($ext = $this->scanRedirRules($name)){			
			return call_user_func_array([$this->ext($ext), $name], $arguments);
		}else{
			trigger_error('method "' . $name . '" not exists', E_USER_NOTICE);
		}
	}

	/**
	 * 
	 * @param type $name
	 * @param type $type js|css
	 * @param type $src
	 */
	function addIncludes($name = false, $type, $src)
	{
		$this->includes[$name] = [$type, $src];
	}	
	
	function isPublic($name)
	{
		return (stripos($name,'view')===0 || stripos($name,'do')===0);
	}	
	
	function __processViewSolveViewName()
	{
		$params = $this->_args['params'];
		
		
		if (isset($params[0])) {
			$view_name = $params[0];

			if (!$this->methodTest('view' . $view_name) && $view_name != 'noview') {
				$view_name = 'default';
			} else {
				array_shift($params);
			}
		} else {
			$view_name = 'default';
		}	
		
		$this->view_name = $view_name;

				
		if(!$this->isPublic("view{$view_name}"))
			$this->view_name = 'default';
	}	
	
	
	function solvePageTitle()
	{		
		$p = $this->app->page;
		
		if($p->type==3 && isset($m->lang['VIEWS'][$p->path]['TITLE']))
			$p->title = $m->lang['VIEWS'][$p->path]['TITLE'];	
		
				
		if($p->type==3 && !$p->title){
			
			$p->title = GW::ln('/m/VIEWS/'.$this->view_name);
		}
	}
}



