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
	
	/**
	 *
	 * @var GW_Site_Application
	 */
	public $app;
	public $options;
	public $links;
	// pvz news/list bus modulis/viewsas, news/view/1/images bus - modulis,viewsas o params = [1,'images']
	public $params;
	public $sys_call = false;

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

	function init() {
		//nekviecia sitos funkcijos
	}

	function processTemplate($name) {

		$this->fireEvent("BEFORE_TEMPLATE");

		$this->smarty->assignByRef('messages', $this->messages);
		$this->smarty->assign('m', $this);
		$this->smarty->assign($this->tpl_vars);

		if ($this->tpl_name)
			$file = $this->tpl_dir . $this->tpl_name;
		else
			$file = $this->tpl_dir . $name;

		if (!file_exists($tmp = $file . '.tpl'))
			die("Template $tmp not found");

		$this->smarty->display($tmp);
	}

	function processView($name, $params = Array()) {
		if ($name == '')
			$name = "default";

		$methodname = "view" . $name;
		$this->$methodname($params);

		$this->processTemplate($name);
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
		$this->$methodname();
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
		$act_name = self::__funcVN(isset($_REQUEST['act']) ? $_REQUEST['act'] : false);


		if (isset($params[0])) {
			$view_name = $params[0];


			if (!method_exists($this, 'view' . $view_name)) {
				$view_name = 'default';
			} else {
				array_shift($params);
			}
		} else {
			$view_name = 'default';
		}

		$this->params = $params;

		if ($act_name)
			$this->processAction($act_name);


		$this->processView($view_name, $params);
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
			'prev' => $current <= 2 ? 0 : $current - 1,
			'next' => $current >= $length ? 0 : $current + 1,
			'last' => $current >= $length ? 0 : $length,
		));
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
