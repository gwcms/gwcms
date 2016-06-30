<?php

class GW_Common_Module extends GW_Module
{

	public $allow_auto_actions = [
	    'dosave' => 1,
	    'dodelete' => 1,
	    'doinvertactive' => 1,
	    'domove' => 1,
	    'viewform' => 1,
	    'viewitem' => 1,
	    'viewitemactions' => 1,
	    'viewlist' => 1,
	    'viewdialogconfig' => 1,
	    'dodialogconfigsave' => 1,
	    'doclone' => 1,
	];
	public $filters = [];
	//to easy adjust list for printing
	public $paging_enabled = true;
	// 1 - integer
	public $data_object_id_type = 1;
	// share with smarty
	public $options;
	public $links;
	public $default_view = 'list';
	public $load_before_save = true;
	//include [type:js/css/jsstring,data/path]
	public $includes = [];
	public $sys_call = false;

	/**
	 * to use this function you must store in $this->model GW_Data_Object type object
	 */
	function init()
	{
		parent::init();

		$this->list_params['paging_enabled'] = false;

		//d::dumpas($this->app->page);
		//specifu model name in lang file
		if (!isset($this->model) && ($tmp = $this->app->page->getDataObject()))
			$this->model = $tmp;

		$this->tpl_vars['options'] = & $this->options;
		$this->tpl_vars['links'] = & $this->links;

		if (isset($_GET['sys_call'])) {
			$this->sys_call = 1;

			if ($_GET['sys_call'] == 2)
				header('Content-type: text/plain');
		}
	}

	function initLogger()
	{
		$this->lgr = new GW_Logger(GW::s('DIR/LOGS') . 'mod_' . $this->module_name . '.log');
		$this->lgr->collect_messages = true;
	}

	function getCurrentItemId()
	{
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

		if ($id)
			return $id;

		if (isset($this->app->path_arr_parent['data_object_id']) && $tmp = $this->app->path_arr_parent['data_object_id'])
			$id = $tmp;

		if ($this->data_object_id_type == 1)
			$id = (int) $id;

		return $id;
	}

	/**
	 * 
	 * @param type $load
	 * @param type $class
	 * @return GW_Data_Object
	 */
	function getDataObjectById($load = true, $class = false)
	{

		$id = $this->getCurrentItemId();


		if (!$id)
			return $this->setErrors('/g/GENERAL/BAD_ARGUMENTS');

		if ($class)
			$item = new $class($id);
		else
			$item = $this->model->createNewObject($id);

		if ($load && !$item->load())
			return $this->setErrors('/g/GENERAL/ITEM_NOT_EXISTS');

		$this->canBeAccessed($item, true);

		return $item;
	}

	/**
	 * common doDelete action override this if diferent functionality needed
	 */
	function common_doDelete()
	{
		if (!$item = $this->getDataObjectById())
			return false;

		$this->fireEvent('BEFORE_DELETE', $item);

		$item->delete();
		$this->app->setMessage($this->app->lang['ITEM_REMOVE_SUCCESS']);

		$this->fireEvent('AFTER_DELETE', $item);

		$this->jump();
	}

	function common_doClone()
	{

		if (!$item = $this->getDataObjectById())
			return false;


		$this->fireEvent('BEFORE_CLONE', $item);
		$this->__doCloneAfterClone($item);



		$this->app->sess['item'] = $item->toArray();
		unset($this->app->sess['item']['id']);
		unset($_GET['id']);


		//d::dumpas($this->app->path);
		$this->app->jump();
	}

	function __doCloneAfterClone($item)
	{
		$item->title = $item->title . ' (' . $this->app->lang['ITEM_COPY'] . ')';
	}

	public $auto_images = 1;

	/**
	 * common doSave action override this if diferent functionality needed
	 */
	function common_doSave()
	{
		$vals = $_REQUEST['item'];
		$vals+=$this->filters;

		if ($vals['id'] === '')
			unset($vals['id']);

		$item = $this->model->createNewObject($vals, false, $this->lang());

		if ($this->load_before_save)
			$item->load();



		$this->canBeAccessed($item, true);
		$item->setValues($vals);

		$this->fireEvent('BEFORE_SAVE_0', $item);

		if ($this->auto_images && count($_FILES))
			GW_Image_Helper::__setFiles($item);

		if (!$item->validate()) {
			if (!isset($_POST['ajax'])) {
				$this->setErrors($item->errors);
			} else {
				$this->error_fields = array_merge($this->error_fields, $item->errors);
			}


			$this->processView('form');
			exit;
		}

		$this->fireEvent('BEFORE_SAVE', $item);

		//jeigu nustatomas id naujo iraso sukurimo atveju GW_Data_Object::save() funkcija interpretuoja kad norima atlikti update veiksma
		//jei i forma dadesime
		//{if !$smarty.request.id}
		//	<input type="hidden" name="SAVE-TYPE" value="INSERT">
		//{/if}
		//isvengsime sio nesklandumo
		$item->prepareSave();

		if (isset($_REQUEST['SAVE-TYPE']) && $_REQUEST['SAVE-TYPE'] == "INSERT" || !$item->id) {
			$item->insert();
			$this->app->setMessage($this->app->lang['SAVE_SUCCESS']);
		} else {
			
			if ($item->changed_fields) {
				$item->updateChanged();
				$this->app->setMessage($this->app->lang['SAVE_SUCCESS']);
			} else {
				$this->app->setMessage($this->app->lang['NO_CHANGES']);
			}
		}

		$this->fireEvent('AFTER_SAVE', $item);

		//jeigu saugome tai reiskia kad validacija praejo
		if (!isset($_POST['ajax'])) {

			$this->jumpAfterSave($item);
		} else {
			header("GW_AJAX_FORM: OK");
			header("GW_AJAX_FORM_ITEM_ID: " . $item->id);

			$this->tpl_vars['ajax_rows_only'] = 1;
			$this->processView('list', ['ajax_one_item_list' => $item->id]);
			exit;
		}
	}

	function jumpAfterSave($item = false)
	{
		//show last operated item in list
		if ($item)
			$_REQUEST['id'] = $item->get('id');

		if ($_REQUEST['submit_type'] == 1) {//apply
			$options = $item ? ['id' => $item->get('id')] : [];
			$this->jump(false, $options + $_GET);
		} else { //save
			if (isset($_REQUEST['return_to']) && ($tmp = $_REQUEST['return_to']))
				return $this->jump($tmp);

			if (isset($_REQUEST['RETURN_TO']) && ($tmp = $_REQUEST['RETURN_TO']))
				return die(header('Location: ' . $tmp));


			$this->jump(dirname($this->app->path));
		}
	}

	/**
	 * common view - viewForm. override this if diferent functionality needed
	 */
	function common_viewForm()
	{
		$item = $this->model->createNewObject();

		$id = $this->getCurrentItemId();

		//only form i18n objects
		if (isset($this->i18n_fields) && $this->i18n_fields)
			$item->_lang = $this->lang();

		//pvz kelias: articles/77/form
		//istrauks 77


		if (isset($this->app->sess['item']) && $_REQUEST['item'] = $this->app->sess['item'])
			unset($this->app->sess['item']);

		//if we encounter error during the submit
		//fill out form with values that user submited
		if ((isset($_REQUEST['item']) && $vals = $_REQUEST['item'])) {

			if (isset($vals['id'])) { //redaguojamas su klaidom
				$item->set('id', $vals['id']);
				$item->load();

				$this->canBeAccessed($item, true);
			} else {
				//nuklonuotas
			}

			$item->setValues($vals);
		} elseif ($id) { // edit existing
			$item = $this->model->createNewObject($id, true, $this->lang());

			$this->canBeAccessed($item, true);
		} else { // create new
		}

		$this->fireEvent("AFTER_FORM", $item);

		return ['update' => (int) $item->get('id'), 'item' => $item];
	}

	function common_viewItem()
	{

		$item = $this->getDataObjectById();
		$this->tpl_vars['item'] = $item;
	}

	function common_ViewItemActions()
	{
		$item = $this->getDataObjectById();
		$this->tpl_vars['item'] = $item;

		$this->tpl_file_name = GW::s("DIR/" . $this->app->app_name . "/TEMPLATES") . 'tools/item_actions_menu';
	}

	/**
	 * common action do:invert_active 
	 * to forbid executing 
	 * overload method 
	 */
	function common_doInvertActive()
	{
		if (!$item = $this->getDataObjectById())
			return false;

		$this->canBeAccessed($item, true);

		if (!$item->invertActive())
			return $this->setErrors('/g/GENERAL/ACTION_FAIL');

		$this->fireEvent("AFTER_INVERT_ACTIVE", $item);

		$this->jump();
	}

	function setListParams(&$params = [])
	{
		$cond = isset($params['conditions']) ? $params['conditions'] : '';

		if (isset($this->list_params['views']['conditions']) && $this->list_params['views']['conditions'])
			$cond .= ($cond ? ' AND ' : '') . $this->list_params['views']['conditions'];

		$search = isset($this->list_params['filters']) ? (array) $this->list_params['filters'] : [];


		foreach ($this->filters as $key => $val)
			$search[$key] = ['=', $val];

		foreach ($search as $field => $val) {
			$compare_type = isset($val[0]) ? $val[0] : '=';
			$value = isset($val[1]) ? $val[1] : null;

			if ($value === '' || $value === null)
				continue;

			if ($compare_type == "IN") {
				$value = array_splice($val, 1);
			} else {
				$value = GW_DB::escape($value);
			}

			$cond.= ($cond ? ' AND ' : '');

			if (method_exists($this, $ofmethod = "overrideFilter$field")) {

				$cond.=$this->$ofmethod($value);
			} else {
				switch ($compare_type) {
					case '<':
						$cond .= "(`$field` < ('" . $value . "'))";
						break;
					case '>':
						$cond .= "(`$field` > ('" . $value . "'))";
						break;
					case '!=':
						$cond .= "(`$field` != ('" . $value . "'))";
						break;
					case 'IN':
						$cond .= "(`$field` IN ('" . implode("','", $value) . "'))";
						break;
					case 'LIKE':
						$cond.="(`$field` LIKE '%" . $value . "%')";
						break;
					case 'LIKE%,,%':
						$cond.="(`$field` LIKE '%," . $value . ",%')";
					default:
						$cond.="(`$field` = '" . $value . "')";
						break;
				}
			}
		}

		if ($this->paging_enabled && $this->list_params['paging_enabled'] && $this->list_params['page_by']) {
			$page = isset($this->list_params['page']) && $this->list_params['page'] ? $this->list_params['page'] - 1 : 0;
			$params['offset'] = $this->list_params['page_by'] * $page;
			$params['limit'] = $this->list_params['page_by'];
		}



		if (isset($this->list_params['order']) && $ord = $this->list_params['order'])
			$params['order'] = $ord;


		//perrasoma is modulio konfig. views
		//if(isset($this->list_params['views']['order']) && $ord=$this->list_params['views']['order'])
		//	$params['order']=$ord;
		//perrasoma is modulio konfig. orders
		//if(isset($this->list_params['orders']['order']) && $ord=$this->list_params['orders']['order'])
		//	$params['order']=$ord;		
		//unset($this->list_params['order']);

		$params['conditions'] = $cond;
	}

	function setDefaultOrder()
	{
		if (!isset($this->list_params['order']) || !$this->list_params['order'])
			$this->list_params['order'] = $this->model->getDefaultOrderBy();
	}

	//uzkrauna sarasui viewsus
	//viewsai savyje turi pavadinima, salyga, rikiavima, suskaiciuoti direktyva, 
	function loadViews()
	{
		$views = $this->app->page->VIEWS;
		$store = & $this->list_params['views'];
		$default = false;

		$alli18n = $this->app->lang['FILTER_ALL'];

		$views = [$alli18n => ['name' => $alli18n, 'conditions' => '', 'default' => 1]] + (array) $views;

		foreach ($views as $i => $view)
			if (isset($view['default']))
				$default = $view;

		if (!$store['name'])
			$store = $default;

		foreach ($views as $i => $view) {

			if ($store['name'] == $view['name']) {
				$store = $view;
				$views[$i]['active'] = 1;
			}

			if (isset($view['calculate'])) {
				$key = $this->app->page->path . '::views::' . $view['name'];

				if (!($views[$i]['count'] = GW_Session_Cache::get($key))) {
					$views[$i]['count'] = $tmp = $this->model->count($view['conditions']);
					GW_Session_Cache::set($key, $tmp, '10 seconds');
				}
			}
		}

		$this->tpl_vars['views'] = & $views;
	}

	function loadOrders()
	{
		$orders = $this->app->page->ORDERS;
		$store = & $this->list_params['orders'];
		$default = false;

		$defi18n_name = $this->app->lang['DEFAULT'];

		$orders = ['default' => ['name' => $defi18n_name, 'order' => $this->model->getDefaultOrderBy(), 'default' => 1]] + (array) $orders;

		foreach ($orders as $i => $order)
			if (isset($order['default']))
				$default = $order;

		if (!$store['name'])
			$store = $default;

		foreach ($orders as $i => $order) {

			if (isset($store['name']) && $store['name'] == $order['name']) {
				$store = $order;
				$orders[$i]['active'] = 1;
			}
		}

		$this->tpl_vars['list_orders'] = & $orders;
	}

	function doSetView()
	{
		$this->list_params['views']['name'] = $_REQUEST['name'];
		$this->loadViews();


		if (isset($this->list_params['views']['order']) && $ord = $this->list_params['views']['order']) {
			$this->list_params['order'] = $ord;
		}



		unset($_GET['name']);
		$this->jump();
	}

	// key=>value rule fieldname=>1
	public $allowed_order_columns = [];

	function doSetOrder()
	{

		$orders = $this->app->page->ORDERS;

		$foundorder = false;

		if (isset($_REQUEST['name'])) {
			$defi18n_name = $this->app->lang['DEFAULT'];

			if ($_REQUEST['name'] == $defi18n_name)
				$orders[] = ['name' => $defi18n_name, 'order' => $this->model->getDefaultOrderBy()];


			foreach ($orders as $order)
				if ($order['name'] == $_REQUEST['name'])
					$foundorder = $order;

			if (!$foundorder) {
				$this->setErrors('/g/GENERAL/ORDER_NOT_FOUND');
				$this->jump();
			}

			$this->list_params['order'] = $foundorder['order'];

			$this->list_params['orders']['name'] = $_REQUEST['name'];
		} elseif (isset($_REQUEST['order'])) {

			if (!$this->__validateOrder($_REQUEST['order'], $this->allowed_order_columns + $this->model->getColumns())) {
				$this->setErrors('/g/GENERAL/BAD_ORDER_FIELD');
				$this->jump();
			} else {
				$this->list_params['orders']['name'] = 'NIEKAS';
				$this->list_params['order'] = $_REQUEST['order'];
			}
		}



		unset($_GET['name']);

		$this->jump();
	}

	function __parseOrders($order)
	{
		preg_match_all('/(\w+) (ASC|DESC)/i', $order, $matches, PREG_SET_ORDER);

		return $matches;
	}

	//pereina per visus stulpelius (norimus rikiuoti) ir patikrina ar yra stulpeliu sarase
	//jei nera arba jei nematchina grazina false
	function __validateOrder(&$order, $columns)
	{
		if ($matches = $this->__parseOrders($order)) {
			foreach ($matches as $match)
				if (!isset($columns[$match[1]]))
					return false;


			$orders = [];
			foreach ($matches as $match)
				$orders[] = $match[1] . ' ' . $match[2];

			$order = implode(',', $orders);

			return true;
		}else {
			return false;
		}
	}

	function common_viewList($params = [])
	{
		$this->loadViews();
		$this->loadOrders();

		$this->fireEvent('BEFORE_LIST_PARAMS', $params);

		$this->setListParams($params);

		$this->fireEvent('AFTER_LIST_PARAMS', $params);

		$cond = isset($params['conditions']) ? $params['conditions'] : false;

		//d::Dumpas($cond);


		$params['key_field'] = $this->model->primary_fields[0];

		$params['soft_error'] = true;

		if (isset($params['ajax_one_item_list'])) {
			$list = [$this->model->createNewObject($params['ajax_one_item_list'], true)];
		} else {
			$list = $this->model->findAll($cond, $params);
		}

		if ($list === null) {
			$this->list_params = [];

			if ($this->app->user->isRoot())
				$this->setErrors("Last query: " . $this->model->getDB()->error_query);

			return $this->setErrors($this->model->errors);
		}

		$this->setDefaultOrder(); //for template



		if ($this->list_params['page_by'])
			$this->tpl_vars['query_info'] = $this->model->lastRequestInfo();


		$this->fireEvent('AFTER_LIST', $list);


		return ['list' => $list];
	}

	function common_doMove($params = false)
	{
		if (!($item = $this->getDataObjectById()))
			return $this->jump();

		$item->move($_REQUEST['where'], $this->getMoveCondition($item));
		unset($_GET['where']);

		$this->jump(false, ['id' => $item->get('id')]);
	}

	//used to allow user edit field list	
	function getDisplayFields($fields)
	{
		$this->app->sess['current_module_fields'] = $fields;


		if ($saved = (array) $this->app->page->fields)
			$fields = $saved + $fields; //prideti fields tam kad programavimo eigoje pridejus nauja laukeli veiktu

		$rez = [];

		foreach ($fields as $id => $enabled)
			if ($enabled)
				$rez[] = $id;

		return $rez;
	}

	function common_doDialogConfigSave()
	{

		//atstatyti numatytuosius nustatymus
		if ($_REQUEST['defaults'])
			$fields = $this->app->sess['current_module_fields'];
		else
			$fields = $_REQUEST['fields'];


		$this->app->page->fields = $fields;
		$this->app->page->update(['fields']);

		$this->jump();
	}

	function common_viewDialogConfig()
	{
		$fields = $this->app->sess['current_module_fields'] ? $this->app->sess['current_module_fields'] : [];
		$saved = $this->app->page->fields ? (array) $this->app->page->fields : [];

		$this->tpl_vars['fields'] = $saved + $fields;

		ob_flush();
	}

	function getMoveCondition($item)
	{
		return GW_SQL_Helper::condition_str($this->filters);
	}

	function methodExists($name)
	{
		return method_exists($this, $name) || isset($this->allow_auto_actions[$name]);
	}

	function canBeAccessed($item, $die = true)
	{
		$result = true; //$item->canBeAccessedByUser($this->app->user);

		if (!$die || $result)
			return $result;

		$this->setErrors('/g/GENERAL/ACTION_RESTRICTED');
		$this->jump();
	}

	function __call($name, $arguments)
	{
		$name = strtolower($name);

		if (isset($this->allow_auto_actions[$name]))
			return call_user_func_array([$this, "common_$name"], $arguments);
		else
			trigger_error('method "' . $name . '" not exists', E_USER_NOTICE);
	}

	function setMessage($message)
	{
		if (is_array($message))
			$message = json_encode($message);

		if ($this->sys_call) {
			$this->lgr->msg($message);
		} else {
			$this->app->setMessage($message);
		}
	}

	function setErrors($errors, $level = 2)
	{
		if ($this->sys_call) {
			$this->lgr->msg($errors);
		} else {
			parent::setErrors($errors, $level);
		}
	}
}
