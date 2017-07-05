<?php

class GW_Common_Module extends GW_Module
{

	public $allow_auto_actions = [
		'dosave' => 1,
		'dodelete' => 1,
		'doseriesact' => 1,
 		'doinvertactive' => 1,
		'dosetactive' => 1,
		'doseriesact' => 1,
		'domove' => 1,
		'viewform' => 1,
		'viewitem' => 1,
		'viewitemactions' => 1,
		'viewlist' => 1,
		'viewdialogconfig' => 1,
		'viewdialogremove' => 1,
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
		
		
		if(isset($this->app->path_arr[count($this->app->path_arr)-1]['data_object_id'])){
			$this->acive_object_id = $this->app->path_arr[count($this->app->path_arr)-1]['data_object_id'];
		}elseif(isset($_GET['id'])){
			$this->acive_object_id = $_GET['id'];
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
			return $this->setError('/g/GENERAL/BAD_ARGUMENTS');

		if ($class)
			$item = new $class($id);
		else
			$item = $this->model->createNewObject($id);

		if ($load && !$item->load())
			return $this->setError('/g/GENERAL/ITEM_NOT_EXISTS');

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
		$this->setMessage(["text"=>"/g/ITEM_REMOVE_SUCCESS", "type"=>GW_MSG_SUCC, "title"=>$item->title, "obj_id"=>$item->id]);

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
		
		//TODO : palyginti senas vertes, jei bus pakitusios mest errora
		/*
		$original_vals = json_decode($_POST['original_values'], true);
		
		foreach($original_vals as $key => $val)
			$data[]=  urlencode($key).'='.urlencode($val);
		
		parse_str(implode('&',$data), $original_vals);
		d::dumpas($original_vals);
		*/
		
		$vals = $_REQUEST['item'];
		$vals+=$this->filters;

		if ($vals['id'] === '')
			unset($vals['id']);

		$item = $this->model->createNewObject(isset($vals['id']) ? ['id'=>$vals['id']]: [], false, $this->lang());

		if ($this->load_before_save)
			$item->load();

		$this->fireEvent('BEFORE_SAVE_00', $item);

		$this->canBeAccessed($item, true);
		$item->setValues($vals);

		$this->fireEvent('BEFORE_SAVE_0', $item);
		
		if ($this->auto_images && count($_FILES))
			GW_Image_Helper::__setFiles($item);

		if (!$item->validate()) {
			if (!isset($_POST['ajax'])) {
				$this->setItemErrors($item);
				
				$this->processView('form');
				exit;
			} else {
				$this->error_fields = array_merge($this->error_fields, $item->errors);
			}
		}

		$this->fireEvent('BEFORE_SAVE', $item);

		//jeigu nustatomas id naujo iraso sukurimo atveju GW_Data_Object::save() funkcija interpretuoja kad norima atlikti update veiksma
		//jei i forma dadesime
		//{if !$smarty.request.id}
		//	<input type="hidden" name="SAVE-TYPE" value="INSERT">
		//{/if}
		//isvengsime sio nesklandumo
		$item->prepareSave();

		$message = ["title"=>$item->title];
				
		if (isset($_REQUEST['SAVE-TYPE']) && $_REQUEST['SAVE-TYPE'] == "INSERT" || !$item->id) {
			$item->insert();
			$message["text"]="/g/SAVE_SUCCESS";
		} else {
			if ($item->changed_fields || $item->changed) {
				$item->updateChanged();
				$message["text"]="/g/SAVE_SUCCESS";
			} else {
				
				$message['text']="/g/NO_CHANGES";
				$message['type']=GW_MSG_INFO;
			}
		}
		
		$message["id"]=$item->id;
		$this->setMessage($message);

		$this->fireEvent('AFTER_SAVE', $item);

		//jeigu saugome tai reiskia kad validacija praejo
		if(isset($_GET['reloadparent']) && $_REQUEST['submit_type'] != 1)
		{
			echo "<script type='text/javascript'>parent.location.reload()</script>";
			exit;
		}
		if(isset($_GET['dialog']) && $_REQUEST['submit_type'] != 1) {
			$contextdata = json_encode(['item'=>['id'=>$item->id,'title'=>$item->title]]);
			
			echo "<script type='text/javascript'>window.parent.gwcms.close_dialog2($contextdata)</script>";
			exit;
		}elseif(!isset($_POST['ajax'])) {

			$this->jumpAfterSave($item);
		} else {
			header("GW_AJAX_FORM: OK");
			header("GW_AJAX_FORM_ITEM_ID: " . $item->id);
			header("GW_AJAX_FORM_ITEM_TITLE: " . $item->title);
			header("GW_AJAX_MESSAGES: ".json_encode($this->app->acceptMessages(true)));

			if(isset($_POST['inlistform'])){
				$this->tpl_vars['ajax_rows_only'] = 1;
				$this->processView('list', ['ajax_one_item_list' => $item->id]);
				exit;
			}else{
				echo json_encode($vals);
			}
			
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
		

		if(isset($_GET['ajax']))
			$this->tpl_file_name = $this->tpl_dir.'form_ajax';
		
		$this->prepareListConfig();
		
		
		
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

	
	function getDataObjectByIds() 
	{
		$ids = explode(',', $_GET['ids']);
		$cond = GW_DB::inCondition('id', $ids);
		$items = $this->model->findAll($cond);
				
		return $items;
	}
	
	function common_doSeriesAct()
	{
		$items = $this->getDataObjectByIds();
		$method = $_GET['action'];
				
		$prev_syscall = $this->sys_call;
		$this->sys_call = true;
		
		foreach($items as $item)
			$this->$method($item);
		
		$this->sys_call = false;
		
		$script="<script>require(['gwcms'], function(){";
		foreach($items as $item)
			$script.="animateChangedRow($item->id, 1000);";
		$script.="})</script>";
		
		$this->setMessage("Action <b>\"".GW::l("/A/VIEWS/$method")."\"</b> performed on ".count($items)." item".(count($items)>1?'s':'').$script);
		
		if(!$this->sys_call)
			$this->jump();
	}
	
	function common_doSetActive($item=false)
	{
		if(!$item)
			if (!$item = $this->getDataObjectById())
				return false;
		
		$this->canBeAccessed($item, true);
		
		$item->active = true;
		$item->updateChanged();
		
		if(!$this->sys_call)
			$this->jump();
	}	
	
	/**
	 * common action do:invert_active 
	 * to forbid executing 
	 * remove from unset($this->allow_auto_actions['doinvertactive'])
	 * supports seriesAct
	 */
	function common_doInvertActive($item=false)
	{
		if(!$item)
			if (!$item = $this->getDataObjectById())
				return false;

		$this->canBeAccessed($item, true);

		if (!$item->invertActive())
			return $this->setError('/g/GENERAL/ACTION_FAIL');

		$this->fireEvent("AFTER_INVERT_ACTIVE", $item);

		if(!$this->sys_call)
			$this->jump();
	}

	function buildCond($field, $compare_type, $value, $encap_val=true, $encap_fld=true)
	{
		$encapChr = $encap_val ? "'" : '';
		
		$cond = ($encap_fld ? "a.`$field`" : $field). ' ';
				
		switch ($compare_type) {
			case 'LT':
				$cond .= "< $encapChr" . $value . "$encapChr";
				break;
			case 'GT':
				$cond .= "> $encapChr" . $value . "$encapChr";
				break;
			case 'NEQ':
				$cond .= "!= $encapChr" . $value . "$encapChr";
				break;
			case 'INSTR':
				$value = explode(",", $value);		
			case 'IN':
				if(is_array($value))
					$cond .= "IN ('" . implode("','", GW_DB::escape($value)) . "')";
				break;
			case 'NOTIN':
				if(is_array($value))
					$cond .= "NOT IN ('" . implode("','", GW_DB::escape($value)) . "')";
				break;
			case 'LIKE':
				$cond .= "LIKE ".($encap_val ? "'%" . $value . "%'" : $value);
				break;
			case 'LIKE%,,%':
				$cond .= "LIKE ".($encap_val ? "'%," . $value . ",%'" : $value);
			case 'EQ':
			default:
				$cond .= "= $encapChr" . $value . "$encapChr";
				break;
		}

		return $cond ? "($cond)" : "";
	}

	function setListParams(&$params = [])
	{
		
		$this->prepareListConfig();
		$this->tpl_vars+=$this->list_config;
		
		$cond = isset($params['conditions']) ? $params['conditions'] : '';

		if (isset($this->list_params['views']->condition) && $this->list_params['views']->condition)
			$cond .= ($cond ? ' AND ' : '') . $this->list_params['views']->condition;

		$search = isset($this->list_params['filters']) ? (array) $this->list_params['filters'] : [];

		foreach ($this->filters as $key => $val)
			$search[] = ['field' => $key, 'value' => $val, 'ct' => 'EQ'];


		

		foreach ($search as $filter) {
			
			$compare_type = $filter['ct'];
			$value = $filter['value'];
			$field = $filter['field'];
			
			if($value==="" || $value===null)
				continue;			

			if ($compare_type == "IN" || $compare_type == "NOTIN") {
				
				if($value==='null')
					continue;
				//d::dumpas($filter);
				$value = json_decode($value);
			} else {
				$value = GW_DB::escape($value);
			}
			
			$cond.= ($cond ? ' AND ' : '');

			if (method_exists($this, $ofmethod = "overrideFilter$field")) {
				$cond.=$this->$ofmethod($value, $compare_type);
			} else {
				$cond.=$this->buildCond($field, $compare_type, $value);
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
			if (method_exists($this->model, 'getDefaultOrderBy'))
				$this->list_params['order'] = $this->model->getDefaultOrderBy();
	}

	
	
	function __viewsSearchPaths()
	{
		return array_unique([$this->app->path_clean,$this->app->path, $this->module_path_clean]);
	}
	//uzkrauna sarasui viewsus
	//viewsai savyje turi pavadinima, salyga, rikiavima, suskaiciuoti direktyva, 
	function loadViews($page=false)
	{		
		if(!$page)
			$page = $this->app->page;
			
		$pview0 = GW_Adm_Page_View::singleton();
		$views = $pview0->getByPath($this->__viewsSearchPaths());
		
		$store = & $this->list_params['views'];
		
		if (!$store || !$store->id)
			$store = $pview0->selectDefault($views);

		foreach ($views as $i => $view) {

			//set current view
			if ($store->id == $view->id) 
				$view->current = true;
			
			//calculate results
			if ($view->calculate) {
				$key = $this->app->page->path . '::views::' . $view->id;

				if (!($view->count_result = GW_Session_Cache::get($key))) {
					
					try{	
						$view->count_result = $tmp = $this->model->count($view->condition);
					} catch (Exception $e) {
						$this->setError("Can't calculate '$view->title' {$e->getMessage()}");
						$view->count_result = $tmp = "!Err";
					}
					
					GW_Session_Cache::set($key, $tmp, '10 seconds');
				}
			}
		}
		
		$this->tpl_vars['views'] = & $views;
	}



	function doSetView()
	{
		
		//$this->initListParams(false, 'list');
		$this->prepareListConfig();
		$this->loadViews();
		
		
		
		$pview = GW_Adm_Page_View::singleton()->selectById($this->tpl_vars['views'], $_REQUEST['view_id']);
		$this->list_params['views'] = $pview;
		
		
		//jump to first page
		$this->list_params['page']=1;

		if ($pview instanceof GW_Adm_Page_View) {
			
			if($pview->order)
				$this->list_params['order'] = $pview->order;
			
			
			if($pview->fields){	
				$this->list_params['fields'] = json_decode($pview->fields, true);
				$this->list_params['updatetime'] = time();
			}
			
			if($pview->page_by)
			{
				$this->list_params['page_by']=$pview->page_by;
			}
			
			
		}



		unset($_GET['view_id']);
		session_write_close();
		
		
		$this->jump();
	}

	// key=>value rule fieldname=>1


	function __parseOrders($order)
	{
		preg_match_all('/(\w+) (ASC|DESC)/i', $order, $matches, PREG_SET_ORDER);

		$orders = [];

		foreach ($matches as $match)
			$orders[$match[1]] = $match[2];

		return $orders;
	}

	//pereina per visus stulpelius (norimus rikiuoti) ir patikrina ar yra stulpeliu sarase
	//jei nera arba jei nematchina grazina false
	function __validateOrder(&$order, $columns)
	{
		if ($parsed = $this->__parseOrders($order)) {
			$orders = [];

			foreach ($parsed as $fieldname => $dir) {
				if (!isset($columns[$fieldname]))
					return false;

				$orders[] = "$fieldname $dir";
			}

			$order = implode(',', $orders);

			return true;
		}else {
			return false;
		}
	}

	function common_viewList($params = [])
	{
		$this->loadViews();


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
		
		$last_querty=$this->model->getDB()->last_query;
		
		if(isset($_GET['verbose']) && $this->app->user->isRoot())
		{
			print_r([
			    'last_query'=>$last_querty
			]);
		}
		

		if ($list === null) {
			$this->list_params = [];

			if ($this->app->user->isRoot())
				$this->setError("Last query: " . $this->model->getDB()->error_query);

			foreach($this->model->errors as $error)
				$this->setError($error);
			
			return false;
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
				
		//d::Dumpas($this->list_params['views']);
				
		if(isset($this->list_params['fields']))
		{
			$saved = $this->list_params['fields'];
			
		}else{
			$saved = (array) $this->app->page->fields;
		}
		
		//prideti fields tam kad programavimo eigoje pridejus nauja laukeli veiktu
		if ($saved){
			//bet neturi likti neegzistuojanciu
			foreach($saved as $fieldname => $x)
				if(!isset($fields[$fieldname]))
					unset($saved[$fieldname]);

			$fields = $saved + $fields;
		}

		$rez = [];

		foreach ($fields as $id => $enabled)
			if ($enabled)
				$rez[] = $id;

		return $rez;
	}

	
	function __doDialogConfigPrepareOrders(){
		$neworders = [];
		foreach ($_REQUEST['order_fields'] as $fieldname => $info)
			if ($info['enabled'])
				$neworders[] = "$fieldname " . $info['dir'];
		
			
		$neworders = implode(', ', $neworders);
		
		

		
		
		//if($this->__validateOrder($neworders, $this->list_config['dl_order_enabled_fields'] + $this->model->getColumns()))
			$this->list_params['order'] = $neworders;	
	}
	
	function common_doDialogConfigSave()
	{

		//atstatyti numatytuosius nustatymus
		/////////////////////////////////////////////////////////FIELDS
		if ($_REQUEST['defaults'])
			$fields = $this->list_config['display_fields'];
		else
			$fields = $_REQUEST['fields'];

		
		$this->list_params['fields'] = $fields;		
		$this->list_params['updatetime'] = time();
		
		$this->__doDialogConfigPrepareOrders();		
		
		if($_REQUEST['savetodefaultpageview']=='1')
		{
			
			$paths = $this->__viewsSearchPaths();
			if($pview=GW_Adm_Page_View::singleton()->find(['title="Default" AND path=?',$paths[0]]))
			{
				
			}else{
				$pview = GW_Adm_Page_View::singleton()->createNewObject();	
				$pview->path = $paths[0];
				$pview->title = "Default";
				$pview->title_short = '<i class="fa fa-home"></i>';
			}
			
			$pview->fields = json_encode($fields);
			$pview->order = $this->list_params['order'];
			$pview->active = 1;
			$pview->priority = 100;
			$pview->default = 1;
			
			$pview->save();
			
		}
		

		


		$this->jump();
	}

	
	
	//paruosti laukelius redagavimui
	function __viewDialogConfigPrepareOrders()
	{
		$this->initListParams(false, "list");
		$edit_orders = $this->__parseOrders($this->list_params['order']);


		$formatorders = [];
		if($edit_orders)
			foreach ($edit_orders as $fieldname => $dir)
				$formatorders[$fieldname] = ['enabled' => 1, 'dir' => $dir];

		$order_enabled_fields = [];
		foreach ($this->list_config['dl_order_enabled_fields'] as $fieldname => $x)
			$order_enabled_fields[$fieldname] = ['enabled' => 0, 'dir' => 'ASC'];


		$this->tpl_vars['order_fields'] = $formatorders + $order_enabled_fields;		
	}
	
	
	function common_viewDialogConfig()
	{
		$this->initListParams(false, "list");
		$this->prepareListConfig();
		
		$fields = $this->list_config['display_fields'];
		
		$saved = isset($this->list_params['fields']) && $this->list_params['fields'] ? (array) $this->list_params['fields'] : [];
		
		foreach($saved as $fieldname => $x)
			if(!isset($fields[$fieldname]))
				unset($saved[$fieldname]);		

		
		$this->tpl_vars['fields'] = $saved + $fields;
		
		$this->__viewDialogConfigPrepareOrders();

		$this->tpl_file_name = GW::s("DIR/" . $this->app->app_name . "/TEMPLATES") . "list/dialogconfig";
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

		$this->setError('/g/GENERAL/ACTION_RESTRICTED');
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

	function doGetFilters()
	{
		$this->prepareListConfig();
		
		$filters_config = $this->list_config['dl_filters'];
		
		$filtername = isset($_GET['fieldname']) ? $_GET['fieldname'] : false;

		if (isset($filters_config[$filtername]))
			$filters_config = [$filtername => $filters_config[$filtername]];
		
		
				
		$this->tpl_vars['dl_filters'] = $filters_config;
		
		$this->tpl_file_name = GW::s("DIR/" . $this->app->app_name . "/TEMPLATES") . "list/filtersajax";
		$this->processTemplate();
	}
	
	function viewDialogRemove()
	{
		$items = explode(',',$_GET['ids']);
		$this->tpl_vars['ids']=$_GET['ids'];
		$this->tpl_vars['items_count']=count($items);
		
		$this->tpl_file_name = GW::s("DIR/" . $this->app->app_name . "/TEMPLATES") . "list/dialogremove";
	}
	
	function doDialogRemove()
	{
		$ids = explode(',',$_REQUEST['ids']);
		
		
		foreach($ids as $id)
		{
			$item = $this->model->createNewObject($id, true);
			$item->delete();
		}
		
		$this->setPlainMessage(sprintf(GW::l('/g/SELECTED_ITEMS_REMOVED'), count($ids)), GW_MSG_SUCC);

		
		$this->jump($this->app->path_arr_parent['path']);
	}	
	
	
	function prepareListConfig()
	{
		//neuzkraut jei jau uzkrautas
		if(isset($this->list_config['dl_fields']))
			return false;
		
		$conf = $this->getListConfig();
		
		$display_fields = [];
		$order_enabled = [];
		$filters = [];
 
		foreach($conf['fields'] as $fieldname => $set){
			if(strpos($set,'L')!==false)
				$display_fields[$fieldname] = 1;
			if(strpos($set,'l')!==false)
				$display_fields[$fieldname] = 0;
			if(strpos($set,'o')!==false)
				$order_enabled[$fieldname] = 1;	
			if(strpos($set,'f')!==false)
				//if there is extra config on filters it is loaed from getListConfig['filters'][$fieldname]
				$filters[$fieldname] = isset($conf['filters'][$fieldname]) ? $conf['filters'][$fieldname]: 1;					
		}
		
		$vars['display_fields'] = $display_fields;
		$vars['dl_fields'] = $this->getDisplayFields($display_fields);
	
		$vars['dl_order_enabled_fields'] = $order_enabled;
		
				
		//padaryti kad filtrai susirikiuotu pagal per "Rodymo parinktys" sustatyta eiliškumą
		if(is_array($this->app->page->fields)){
			
			$tmp = $this->app->page->fields + $filters;
			
			foreach($tmp as $key => $val)
			{
				if(isset($filters[$key]) && $filters[$key]!=1 && $filters[$key]!=0)
					$tmp[$key] = $filters[$key];
			}
			$filters = $tmp;
		}
		
		$vars['dl_filters'] = $filters;
		
		
		
		
		$this->list_config = $vars;
		
		return true;
	}
	
	
	/**
	 * saraso laukeliu konfiguracija
	 *
	 * l - yra sarase ir po defaultu nera matomas reikia isijungt matomuma
	 * L - yra sarase ir po defaultu matomas
	 * f - standartinis filtras
	 * o - galima panaudot rikiavimui
	 *
	 * laukelis => lLfo
	 * 
	 * override me
	 * @return array
	 */
	function getListConfig()
	{
		$cfg = [
			'fields'=>[
				/*
				'id'=>'Lof',
				'name'=>'Lof',
				'insert_time'=>'Lof'
				 * 
				 */
			]
		];
		
		if($this->model)
		foreach($this->model->getColumns() as $fieldname => $x)
		{
			$cfg['fields'][$fieldname] = 'Lof';
		}
		
		return $cfg;
	}
	
	
	function viewIframeClose()
	{
		echo "<script type='text/javascript'>window.parent.iframeClose()</script>";
		exit;
	}
	
	/**
	* to extend simple table with related objects
	 * example use add related users after list
	function __eventAfterList(&$list)
	{
		$this->attachFieldOptions($list, 'user_id', 'GW_User');
		
	}
	 */
	
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
	
	
	/**
	 * 
	 Naudoti filtravimui susijusio objekto laukelius kai duomenyse turime tik asociacija su objektu
	 Example use:
		function overrideFilterInstrument_id($value, $compare_type)
		{		
			return $this->__overrideFilterExObject("GW_Data_Instrument", "instrument_id", ["title_lt","title_en"], $value, $compare_type);
		}

	 */
	function __overrideFilterExObject($object, $field, $searchInFields, $value, $compare_type)
	{
		$cond = [];
		foreach($searchInFields as $fieldname){
			$cond[] = $this->buildCond($fieldname, $compare_type, $value);
		}
		
		$cond = '('.implode(' OR ', $cond).')';
		
		$ids = $object::singleton()->findAll($cond,['select'=>'id','return_simple'=>1,'key_field'=>'id']);
		
		if($ids)
			return GW_DB::inCondition($field, array_keys($ids));
		
		return "1=0";		
	}
	
	
	/**
	 * 
	 * 
	 Example use:
		function overrideFilterInstrument_id($value,$compare_type){
			return $this->__overrideFilterOptions('instrument_id', $value, $compare_type);
		}
	 Init:
		$this->options['instrument_id'] = GW_Data_Instrument::singleton()->getOptions($this->app->ln);
	 */
	function __overrideFilterOptions($field, $value, $compare_type, $options_index=false)
	{		
		$ids = [];
		
		$index = $options_index ? $options_index : $field;
		
		
		if($compare_type=='EQ'){
			foreach($this->options[$index] as $id => $v)
				if(mb_strtolower($v)==mb_strtolower($value))
					$ids[] = $id;
		}else{
			//$compare_type=='LIKE'
			foreach($this->options[$index] as $id => $v)
				if(mb_stripos($v, $value)!==false)
					$ids[] = $id;			
		}
			
		if($ids)
			return GW_DB::inConditionStr($field, $ids);
		
		return "`$field`=0";		
	}


	function doManagePageViews()
	{
		$this->app->carry_params['clean']=1;
		$this->app->jump("system/page_views", ['filterpaths'=>implode(',', $this->__viewsSearchPaths())]);
	}
	
	function __currentFields()
	{
		$allfields = $this->list_config['display_fields'];
		$present_fields = $this->list_config['dl_fields'];
		
		foreach($allfields as $field => $x)
			$allfields[$field] = 0;
		
		foreach($present_fields as $field)
			$allfields[$field] = 1;
		
		return $allfields;
	}
	
	
	function doCreatePageView()
	{
		$params = [];
		$cond = '';
		$this->initListParams(false, 'list');
		$this->setListParams($params);
		$this->prepareListConfig();
		$this->fireEvent("AFTER_LIST_PARAMS", $params);
		
		$cond = $params['conditions'];
		
		
		
		$args = isset($_GET['saveasorder'])? '&saveasorder=1':'';
		$url = $this->app->buildUri("system/page_views/form?id=0&clean=2".$args);
		
		
		//trys galimi path 
		
		$vals = [
		    'condition'=>$cond, 
		    'order'=>$params['order'], 
		    'path_options'=>$this->__viewsSearchPaths(), 
		    'fields'=>json_encode($this->__currentFields()),		    
		    'page_by'=>$this->list_params['page_by']
		];
		
		$this->fireEvent("BEFORE_CREATE_PAGE_VIEW", $vals);
		
		$this->app->sess['item'] = $vals;
				
		header('Location: '.$url);
		exit;
	}

	// key=>value rule fieldname=>1
	public $allowed_order_columns = [];

	function doSetOrder()
	{
		$this->prepareListConfig();
		
		$this->loadOrders();
		$orders = $this->tpl_vars['list_orders'];
		
		$foundorder = false;

		if (isset($_REQUEST['order'])) {

			if (!$this->__validateOrder($_REQUEST['order'], $this->list_config['dl_order_enabled_fields'] + $this->model->getColumns())) {
				$this->setError('/g/GENERAL/BAD_ORDER_FIELD');
				$this->jump();
			} else {
				$this->list_params['orders']['name'] = 'NIEKAS';
				$this->list_params['order'] = $_REQUEST['order'];
			}
		}



		unset($_GET['name']);

		$this->jump();
	}
	
	
	
}
