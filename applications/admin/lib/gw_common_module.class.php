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
		'viewconfiguremenu' => 1,
		'viewlist' => 1,
		'viewdialogconfig' => 1,
		'viewdialogremove' => 1,
		'viewsearchreplace' => 1,
		'dodialogconfigsave' => 1,
		'viewdialogconfig2'=>1,
		'doclone' => 1,
	];
	public $filters = [];
	public $filtersEx = [];
	
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
	public $extra_cols = [];
	public $lgr;
	public $auto_translate_enabled =  true;
	public $readonly = false;
	
	//go with user_id col to identify owner of record
	public $canCreateNew = null;
	public $canAccessIfOwner=true;
	public $write_permission = false;
	public $dynamic_fields = false;
	public $multisite = false;
	

	/**
	 * to use this function you must store in $this->model GW_Data_Object type object
	 */
	function init()
	{
		parent::init();

		$this->write_permission = $this->canBeAccessed(false, ['access'=>GW_PERM_WRITE,'nodie'=>1]);
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
		
		$this->app->carry_params['searchreplace']=1;
		$this->app->carry_params['filterhide']=1;
		$this->app->carry_params['clean']=1;
		$this->app->carry_params['nopview'] = 1;		
		
		$this->initModCfg();
		
		$this->initErrorHandler();
		
		
		if($this->allowed_ids = GW_Permissions::getTempReadAccess(implode('/',$this->module_path)) ){
			$this->filters['id'] = $this->allowed_ids;
		}
		
		
		if(isset($_GET['dragdropmove'])){
			$this->list_params['paging_enabled']=false;	
			$backurl = $this->buildUri(false);
			
			$this->setMessage("Use drag drop rows to change list order. <a href='{$backurl}' class='btn btn-sm btn-default'>Press this button to exit drag&drop ordering mode</a>");			
		}
		if($this->dynamic_fields){
			$this->mod_fields = GW_Adm_Page_Fields::singleton()->findAll(['parent=?', strtolower(get_class($this->model))],['key_field'=>'fieldname']);
			
		}	
		
		if($this->multisite)
			$this->initMultisite();
	}

	function doDragDropMove()
	{
		/*
		 * implement narrowing by your clasificator in your module extending this function 
		$form = ['fields'=>['country'=>['type'=>'select', 'options'=>$this->getCountryOpt(), 'required'=>1]],'cols'=>1];

		
		if(!($answers=$this->prompt($form, $form)))	
			return false;
		*/
		
		$this->jump(false, ['dragdropmove'=>1]);
	}	

	function initModCfgEx($modp)
	{
		$modp = is_array($modp) ? $modp :  explode('/', $modp);
		return new GW_Config($modp[0].(isset($modp[1]) ? '__'.$modp[1]:'' ).'/');
	}
	
	function initModCfg()
	{
		$this->modconfig = $this->initModCfgEx($this->module_path);
	}
	
	function initConfig()
	{
		$this->config = new GW_Config($this->module_path[0] . '/');		
	}	
	
	function getLogFileName()
	{
		return 'mod_' . $this->module_name . '.log';
	}
	
	function initLogger()
	{
		if(!$this->lgr){
			$this->lgr = new GW_Logger(GW::s('DIR/LOGS').$this->getLogFileName());
			$this->lgr->collect_messages = true;
		}
	}

	
	function procError($errStr, $errMail)
	{
		//jei kaip root useris ARBA darbineje versijoje ARBA yra prisijunges kaip aukstesnis vartotojas
		if($this->app->user->isRoot() || GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV || ($this->app->auth->session['switchUser'] ??false) ){
			$this->setError($errStr);
		}else{
			$subj = GW::s('PROJECT_NAME'). ' - Mod warning env: '.GW::s('PROJECT_ENVIRONMENT');
			$opts = ['to'=>GW::s('REPORT_ERRORS'), 'subject'=>$subj, 'body'=>$errStr.$errMail, 'noAdminCopy'=>1, 'noStoreDB'=>1];
			GW_Mail_Helper::sendMail($opts);		
		}		
	}
	
	public $mute_errors=false;
	
	function errrorHandler($errno, $errstr, $errfile, $errline)
	{
		if (!(error_reporting() & $errno)) {
			// This error code is not included in error_reporting, so let it fall
			// through to the standard PHP error handler
			return false;
		}
		
		if($this->mute_errors)
			return true;

		switch ($errno) {
			case E_USER_ERROR:
				$type="Fatal error";
			case E_USER_WARNING:
				$type="Warn";
			case E_USER_NOTICE:
				$type="Notice";
			default:
				$type = $type ?? "Unknown";
				$file = GW_Debug_Helper::envRootSwitch($errfile);
				
				$errstr = "<a href='http://localhost/gw/tools/netbeanopen/nb_open.php?file=$file&line=$errline'>$type on line $errline in file $errfile:<br /> [$errno] $errstr</a>";
					if($this->action_name)
						$errstr .= " (act:$this->action_name)";
					
						
				//$errstr .= " (uri: {$_SERVER['REQUEST_URI']})";
				
				
				
				if ($this->sys_call && $this->lgr) {
					$this->lgr->msg($_SERVER['REQUEST_URI'].": $errstr");
				}else{
					$errstr .= '<pre>'. GW_Debug_Helper::getCodeCut(['line'=>$errline,'file'=>$errfile], 10).'</pre>';
					
					$errmail= "<pre>".
						d::jsonNice([
						    'user_id'=>$this->app->user->id,
						    'username'=>$this->app->user->username,
						    'request_uri' => $_SERVER['REQUEST_URI'] ?? '-',
						    'referer' => $_SERVER['HTTP_REFERER'] ?? '-',
						    'post' => $_POST,
						    'session' => $_SESSION
							]).
						"</pre>";			

					$this->procError($errstr, $errmail);


					if($errno==E_USER_ERROR)
						exit;
				}
				
				break;
		}

		/* Don't execute PHP internal error handler */
		return true;
	}

	
	
	function initErrorHandler()
	{
		$old_error_handler = set_error_handler(array($this, 'errrorHandler'));		
	}
	
	function getCurrentItemId()
	{
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

		if ($id)
			return $id;
		
		

		if (isset($this->app->path_arr_parent['data_object_id']) && $tmp = $this->app->path_arr_parent['data_object_id'])
			$id = $tmp;
		
		
		

		if ($this->data_object_id_type == 1)
			$id = $id;

		return $id;
	}

	/**
	 * 
	 * @param type $load
	 * @param type $class
	 * @return GW_Data_Object
	 */
	function getDataObjectById($load = true, $class = false, $access=GW_PERM_WRITE)
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
		
		if ($load && !$item->loaded){
			return $this->setError('/g/GENERAL/ITEM_NOT_EXISTS');
		}

		$this->canBeAccessed($item, ['access'=>$access]);
		
		

		return $item;
	}

	/**
	 * common doDelete action override this if diferent functionality needed
	 */
	function common_doDelete()
	{
		if (!$item = $this->getDataObjectById(true, false, GW_PERM_REMOVE))
			return false;

		$this->fireEvent('BEFORE_DELETE', $item);
		

		if($item->delete()){
			$this->setMessage(["text"=>GW::l("/g/ITEM_REMOVE_SUCCESS"), "type"=>GW_MSG_SUCC, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
		}else{
			$this->setMessage(["text"=>GW::l("/g/ITEM_REMOVE_FAILED"), "type"=>GW_MSG_ERR, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
		}
		
		$this->fireEvent('AFTER_DELETE', $item);

		if($this->isPacketRequest())	
			$this->app->addPacket(['action'=>'delete_row','id'=>$item->id, 'context'=>get_class($this->model)]);
		
		if(!$this->sys_call)
			$this->jump();
	}

	function common_doClone()
	{

		if (!$item = $this->getDataObjectById(true, false, GW_PERM_READ))
			return false;


		$this->fireEvent('BEFORE_CLONE', $item);
		$this->__doCloneAfterClone($item);



		$this->app->sess['item'] = $item->toArray();
		unset($this->app->sess['item']['id']);
		unset($_GET['id']);

		$this->fireEvent('AFTER_CLONE', $item);

		//d::dumpas($this->app->path);
		$this->app->jump();
	}

	function __doCloneAfterClone($item)
	{
		$item->title = $item->title . ' (' . GW::l('/g/ITEM_COPY') . ')';
	}

	public $auto_images = 1;

	public $can_be_empty_fields = [];
	
	function searchEmptyVals(&$vals, $fields){
		
		$warnempty = [];
		foreach($fields as $field => $x)
		{
			//ant sitemap dvieju lygiu input_data/laukelio_codas
			if(is_array($fields[$field])){
				foreach($fields[$field] as $field2 => $y){
					if($y=1 && !isset($vals[$field][$field2]) && !isset($_FILES[$field2]))
					{
						$empty_fields[] = $field2;

						if(isset($this->can_be_empty_fields[$field][$field2])){
							$vals[$field][$field2] = "";
						}else{
							//2022-11-22
							//ar tikrai cia reikia apsaugos
							//idejau sita eilute kas pasalina apsauga tik suteikia perspejima
							$vals[$field][$field2] = "";
							$warnempty[] = "$field/$field2";
						}
					}					
				}				
			}else{
				if($x=1 && !isset($vals[$field]) && !isset($_FILES[$field]))
				{
					$empty_fields[] = $field;

					if(isset($this->can_be_empty_fields[$field])){
						$vals[$field] = "";
					}else{
						//2022-11-22
						//ar tikrai cia reikia apsaugos
						//idejau sita eilute kas pasalina apsauga tik suteikia perspejima
						$vals[$field] = "";
						$warnempty[] = $field;
					}
				}				
			}
		}
		
		if($warnempty)
			$this->setMessage(["text"=>"Empty fields found: <b>".implode(", ", $warnempty).'</b>', "type"=>GW_MSG_WARN, 'footer'=>'add them to public $can_be_empty_fields=[field1=>1,field2=>1]', 'float'=>1]);
	}
	
	function dataLossPrevent($item)
	{
		if(isset($_POST['last_update_time']) && $_POST['last_update_time']!=$item->update_time)
		{
			$item->errors['update_time']='/g/ITEM_SAVE_PREVENT_DATA_LOSS';
		}		
	}
	
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
		
		//duplicate save protection
		if(isset($vals['temp_id']) && $vals['temp_id']){
			$tmpid = $vals['temp_id'];
			if($this->app->sess('last_temp_id') == $vals['temp_id']){
				$this->setMessage(['text'=>GW::l('/G/validation/DUPLICATE_SAVE_PROTECION_WORKED'), 'type'=>GW_MSG_INFO, 'float'=>1]);
				GOTO saveFinish;
			}
		}
		
		if(isset($_POST['fields'])){
			$this->searchEmptyVals($vals, $_POST['fields']);
		}

		if (isset($vals['id']) && $vals['id'] === '')
			unset($vals['id']);

		$item = $this->model->createNewObject(isset($vals['id']) ? ['id'=>$vals['id']]: [], false, $this->lang());

		if ($this->load_before_save){
			$item->load();
			
			$this->dataLossPrevent($item);
		}
		
		if($item->id){
			$item->fireEvent('BEFORE_CHANGES');
		}
				
		$this->fireEvent('BEFORE_SAVE_00', $item);

		$this->canBeAccessed($item, ['access'=>GW_PERM_WRITE]);
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
				$this->setItemErrors($item);
				
				header("GW_AJAX_MESSAGES: ".json_encode($this->app->acceptMessages(true)));
				header("GW_ERROR_FIELDS: ".json_encode($this->error_fields));
				exit;
			}
		}

		
		
		$new_item = $item->id ? false : true;

		$this->fireEvent('BEFORE_SAVE', $item);
		$this->fireEvent($new_item ? 'BEFORE_INSERT' : 'BEFORE_UPDATE', $item);
		
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
				
		$message["float"] = 1;
		$message["id"]=$item->id;
		
		$this->setMessageEx($message);

		$this->fireEvent('AFTER_SAVE', $item);
		$this->fireEvent($new_item ? 'AFTER_INSERT' : 'AFTER_UPDATE', $item);
		
		
		if(isset($tmpid))
			$this->app->sess('last_temp_id', $tmpid);
		
		saveFinish:

			
		//jeigu saugome tai reiskia kad validacija praejo

		if(isset($_GET['reloadparent']) && ($_REQUEST['submit_type']??false) != 1)
		{
			echo "<script type='text/javascript'>parent.location.reload()</script>";
			exit;
		}
		if((isset($_GET['dialog']) || isset($_GET['iframe-under-tr']) ) && ($_REQUEST['submit_type']??false) != 1) {			
			$this->dialogJump(['item'=>['id'=>$item->id,'title'=>$item->title]]);
		}elseif(!isset($_POST['ajax'])) {

			$this->jumpAfterSave($item ?? false);
		} else {
			header("GW_AJAX_FORM: OK");
			header("GW_AJAX_FORM_ITEM_ID: " . $item->id);
			header("GW_AJAX_FORM_ITEM_TITLE: " . str_replace(["\n", "\r"],' ',$item->title));
			header("GW_AJAX_MESSAGES: ".json_encode($this->app->acceptMessages(true)));

			if(isset($_POST['inlistform'])){
				$this->tpl_vars['ajax_rows_only'] = 1;
				$this->processView('list', ['ajax_one_item_list' => $item->id]);
				exit;
			}else{
				echo json_encode($item->toArray()+['last_update_time'=>$item->update_time]);
				exit;
			}
			
		}
	}
	
	function dialogJump($contextdata=[], $iframe=false)
	{
		$contextdata = json_encode($contextdata);
		$messages=$this->app->acceptMessages(1);
		foreach($messages as $msg)
			echo "<script type='text/javascript'>window.parent.gw_adm_sys.notification(".json_encode($msg).")</script>";

		if(isset($_GET['iframe-under-tr'])){
			echo '<script>parent.window.closeIframeUnderTr1(window)</script>';
		}else{
			echo "<script type='text/javascript'>window.parent.gwcms.close_dialog2($contextdata)</script>";
		}
		
		exit;		
	}

	function jumpAfterSave($item = false)
	{
		//show last operated item in list
		if ($item)
			$_REQUEST['id'] = $item->get('id');

		if ($_REQUEST['submit_type']??false == 1) {//apply
			
			if(isset($_POST['activetabs']))
				$_GET['activetabs']=$_POST['activetabs'];
		
			$options = $item ? ['id' => $item->get('id')] : [];
			$this->jump(false, $options + $_GET);
		} else {
			if(isset($_GET['dialog']))
				$this->dialogJump();
			//save
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
				$item->load();//4 inheritProps
				$item->copyOriginal();

				$this->canBeAccessed($item, ['access'=>GW_PERM_READ]);
			} else {
				//nuklonuotas
			}

			$item->setValues($vals);
			
			
			
			
		} elseif ($id) { // edit existing
			$item = $this->model->createNewObject($id, false, $this->lang());
			$item->load();
			$item->resetChangedFields();
			
			$this->canBeAccessed($item, ['access'=>GW_PERM_READ]);
		} else { // create new
			$item->temp_id = uniqid();
		}

		$this->fireEvent("AFTER_FORM", $item);
		

		if(isset($_GET['form_ajax'])){
			$this->tpl_file_name = $this->tpl_dir.'form_ajax';
			$this->default_tpl_file_name = GW::s("DIR/".$this->app->app_name."/MODULES")."default/tpl/form_ajax";
		}else{
			$this->default_tpl_file_name = GW::s("DIR/".$this->app->app_name."/MODULES")."default/tpl/form";
		}
		
		$this->prepareListConfig($item);
		
		
		if(! $this->canBeAccessed($item, ['access'=>GW_PERM_WRITE, 'nodie'=>1])){
			$this->tpl_vars['readonly'] = true;
		}
						
		return ['update' => $item->get('id'), 'item' => $item];
	}

	function common_viewItem()
	{

		$item = $this->getDataObjectById(true, false, GW_PERM_READ);
		$this->tpl_vars['item'] = $item;
	}

	function common_ViewItemActions()
	{
		$item = $this->getDataObjectById(true,false, GW_PERM_READ);
		$this->tpl_vars['item'] = $item;
		$this->initListParams(false, 'list');
		
		$this->tpl_file_name = GW::s("DIR/" . $this->app->app_name . "/TEMPLATES") . 'tools/item_actions_menu';
	}
	
	function common_ViewConfigureMenu()
	{
		$this->initListParams(false, 'list');		

		$this->tpl_file_name = GW::s("DIR/" . $this->app->app_name . "/TEMPLATES") . 'list/configure_menu';
	}
	
	function viewColumnMenu()
	{
		$this->tpl_file_name = GW::s("DIR/" . $this->app->app_name . "/TEMPLATES") . 'list/column_menu';		
	}
	
	function doColumnMenu()
	{
		$this->processView('columnmenu');
	}
	
	function common_viewDialogConfig2()
	{
		$this->fireEvent("BEFORE_CONFIG", $this->modconfig);
		$this->tpl_file_name = GW::s("DIR/" . $this->app->app_name . "/TEMPLATES") . 'tools/config';
		
		return ['item'=>$this->modconfig];
	}
	
	function doSaveConfig()
	{

		$vals = $_REQUEST['item'];
		
		
		foreach($vals as $key => $val)
		{
			if(is_array($val))
				$vals[$key] = json_encode($val);
		}
		
		$this->fireEvent("BEFORE_SAVE_CONFIG", $vals);
		
		//$original_vals = json_decode($_REQUEST['original_values'], true);
		//$fields = array_keys($original_vals);		
		
		$this->modconfig->setValues($vals);
		
		$this->fireEvent("AFTER_SAVE_CONFIG", $vals);
		
		
		//jeigu saugome tai reiskia kad validacija praejo
		if(!($this->dialog_iframe_errors ?? false))
			$this->setMessage('/g/SAVE_SUCCESS');
		
		
		$this->jump();
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
		
		if(isset($_GET['all'])){
			$status = $this->$method($items);
		}else{
			foreach($items as $item)
				$this->$method($item);			
			$status = true;
		}
		
		$this->fireEvent('AFTER_SERIES_ACT', $method);
		
		if($status==true){
			$this->sys_call = false;

			$script="<script>require(['gwcms'], function(){";
			foreach($items as $item)
				$script.="animateChangedRow($item->id, 1000);";
			$script.="})</script>";

			$this->setMessage("Action <b>\"".GW::l("/A/VIEWS/$method")."\"</b> performed on ".count($items)." item".(count($items)>1?'s':'').$script);

			if(!$this->sys_call)
				$this->jump();
		}
	}
	
	function common_doSetActive($item=false)
	{
		if(!$item)
			if (!$item = $this->getDataObjectById())
				return false;
		
		$this->canBeAccessed($item, ['access'=>GW_PERM_WRITE]);
		
		$item->active = true;
		$item->updateChanged();
		
		if(!$this->sys_call)
			$this->jump();
	}	
	
	function canInvertActive($item)
	{
		return true;
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

		$this->canBeAccessed($item, ['access'=>GW_PERM_WRITE]);
		
		$this->fireEvent("BEFORE_INVERT_ACTIVE", $item);
		
		$item->fireEvent('BEFORE_CHANGES');

		if ($this->canInvertActive($item) && !$item->invertActive())
			return $this->setError('/g/GENERAL/ACTION_FAIL');

		$this->fireEvent("AFTER_INVERT_ACTIVE", $item);
		
		if(!$this->sys_call)
			$this->jump();
	}

	
	
	function buildCond($field, $compare_type, $value, $encap_val=true, $encap_fld=true, $opts=[])
	{
		$encapChr = $encap_val ? "'" : '';
		
		$cond =  $field. ' ';
		if($encap_fld && !isset($opts['no_alias']) && isset($this->field_alias[$field])){
			$field = $this->field_alias[$field]; //get orig field
			$encap_fld = false;
		}
			
		$cond = ($encap_fld ? GW_DB::escapeField($field, 'a') : $field). ' ';
		
		//d::ldump(['field'=>$field, 'compare_type'=>$compare_type, 'value'=>$value, 'opts'=>$opts]);
				
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
			case 'DATERANGE':
				$value = explode(" - ", $value);	
				return $cond." >= '{$value[0]}.' AND ".$cond." <= '{$value[1]}'";			
			case 'INSTR':
				$value = explode(",", $value);
				
				//specialiai break nededame kad pereitu i sekanti 'IN' case
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
				break;
			case 'EQ':
			default:
				$cond .= "= $encapChr" . $value . "$encapChr";
				break;
		}

		return $cond ? "($cond)" : "";
	}
	
	
	function buildConds($filter, &$cond, $joiner="AND")
	{
			$compare_type = $filter['ct'];
			$value = $filter['value'];
			$field = $filter['field'];
			
			if($value==="" || $value===null)
				return;	
			
			if($value==="#empty#")
				$value="";
			
			if($value==="#zero#")
				$value="0";			

			if (($compare_type == "IN" || $compare_type == "NOTIN") && !is_array($value)) {
				
				if($value==='null')
					return;
				//d::dumpas($filter);
				$value = json_decode($value);
			} else {
				$value = GW_DB::escape($value);
			}
			
			$cond.= ($cond ? ' '.$joiner.' ' : '');

			if (method_exists($this, $ofmethod = "overrideFilter$field")) {
				$cond.=$this->$ofmethod($value, $compare_type);
			} else {
				$cond.=$this->buildCond($field, $compare_type, $value);
			}		
	}
	
	public $prep_list_params;
	public $field_alias = [];
	
	function initFldAliases($params)
	{
		if(!isset($params['select']))
			return false;
		
		$select = explode(',', $params['select']);
		//$list_select = 
		foreach($select as $sel){
			$split = preg_split('/ as /i', $sel);
			if(count($split)==2){
				$orig = str_replace('`', '', trim($split[0]));
				$alias = str_replace('`', '', trim($split[1]));
				$this->field_alias[$alias] = $orig;
			}
			
			$tbl_field = explode('.', $sel);
			if(count($tbl_field)==2){
				if($tbl_field[0]!='a' && $tbl_field[1]!='*'){
					$this->field_alias[$tbl_field[1]] = $sel;
				}
			}
		}
		
		$this->fireEvent('AFTER_INIT_FIELD_ALIAS', $params);	
	}
	
	
	
	/*
		Todo: perdaryt saraso atvaizdavimo opciju surinkima pagal tokia hierarchija
		PageView
			Laukeliai
			Filtrai
			puslapiuot po
			rikiuoti pagal
		TempView
			Laukeliai
			Filtrai
			puslapiuot po
			rikiuoti pagal
		PageView kai nustatomas jis perraso ir tempview
		
	 */
	
	function getPageView4use()
	{
		if(isset($_GET['pview']) && $_GET['pview']=='default'){
			return $this->getDefaultPageView();
		}
		
		if($this->list_params['pviews_and'] ?? false)
			$this->list_config['pviews_and'] = GW_Adm_Page_View::singleton()->findAll(GW_DB::inCondition('id', $this->list_params['pviews_and']),['key_field'=>'id']);
		
		return $this->list_config['pview'] ?? false;
	}
	
	
	function initFieldTypes()
	{
		if($this->model){
			$types = $this->model->getFieldTypes();
			$this->list_config['field_types'] = $types;
		}
				
	}
	
	function setListParams(&$params = [])
	{
		$this->initFieldTypes();
		$this->fireEvent('BEFORE_LIST_PARAMS', $params);
		
		$this->prep_list_params =& $params;
		$this->prepareListConfig();
		$this->tpl_vars+=$this->list_config;
		$pview = $this->getPageView4use();
		
	
		
		$cond = isset($params['conditions']) ? $params['conditions'] : '';
		
		$this->initFldAliases($params);
	
		if ($pview && $pview->condition){
			$cond .= ($cond ? ' AND ' : '') . $this->list_config['pview']->condition;
		}
		
		
		if($this->list_config['pviews_and'] ?? false){
			foreach($this->list_config['pviews_and'] as $andview)
				$cond .= ($cond ? ' AND ' : '') . $andview->condition;
			
		}
		
		

		$search = isset($this->list_params['filters']) ? (array) $this->list_params['filters'] : [];
		
		foreach ($this->filters as $key => $val){
			if(is_array($val)){
				$search[] = ['field' => $key, 'value' => $val, 'ct' => 'IN'];
			}else{
				$search[] = ['field' => $key, 'value' => $val, 'ct' => 'EQ'];
			}
		}

		
		//example use:
		//if(isset($_GET['filterids'])){
		//	$this->filtersEx[]=['field' => 'id', 'value' => explode(',', $_GET['filterids']), 'ct' => 'IN'];		
		//}		
		
		foreach ($this->filtersEx as $filt)
			$search[] = $filt;
		
		
		if(isset($this->list_params['search']) && $this->list_params['search'] && !isset($_GET['skipsearch']))
		{
			
			$is_utf8 = mb_detect_encoding($this->list_params['search']) != 'ASCII';
			
			$cols = $this->getModelCols($is_utf8 ? 'text':'all');	
			$subcond = '';
			
			foreach ($cols as $key => $x){
				$this->buildConds(['field' => $key, 'value' => $this->list_params['search'], 'ct' => 'LIKE'], $subcond, 'OR');
			}
			
			if(isset($this->model->extensions['keyval'])){
				$ids = $this->model->extensionget('keyval')->search($this->list_params['search']);
				if($ids){
					$this->buildConds(['field' => 'id', 'value' => $ids, 'ct' => 'IN'], $subcond, 'OR');
				}
			}

			$this->eventHandler("AFTER_SEARCH_COND_BUILD", $subcond);
			
			$cond.=($cond ? ' AND ':'')."($subcond)";
			
	
			
		}
		
	
			
		foreach ($search as $filter) {
			
			$this->buildConds($filter, $cond);
		}
		
		
		//d::dumpas($cond);
		//d::dumpas([$this->paging_enabled , $this->list_params['paging_enabled'] , $this->list_params['page_by']]);
		
			
		if ($this->paging_enabled && $this->list_params['paging_enabled'] && $this->list_params['page_by']) {
			$page = isset($this->list_params['page']) && $this->list_params['page'] ? $this->list_params['page'] - 1 : 0;
			$params['offset'] = (int)$this->list_params['page_by'] * (int)$page;
			$params['limit'] = max( (int)$this->list_params['page_by'] ,1);
		}
		
		
		if ($pview && $pview->group_by){
			$params['group_by'] = $pview->group_by;
		}		
		
		if ($pview && $pview->select){
			$params['select'] = $params['select'] ?? '';
			$params['select'] = ($params['select'] ? $params['select'].', ':'').$pview->select;
		}

		
		if(isset($_GET['pview'])){
			$params['order'] = $pview->order;
		}else if (isset($this->list_params['order']) && $ord = $this->list_params['order']){
			$params['order'] = $ord;
		}
		
		
		//perrasoma is modulio konfig. views
		//if(isset($this->list_params['views']['order']) && $ord=$this->list_params['views']['order'])
		//	$params['order']=$ord;
		//perrasoma is modulio konfig. orders
		//if(isset($this->list_params['orders']['order']) && $ord=$this->list_params['orders']['order'])
		//	$params['order']=$ord;		
		//unset($this->list_params['order']);

		$params['conditions'] = $cond;
				
		$this->fireEvent('AFTER_LIST_PARAMS', $params);	
		
		//d::dumpas($params);
	}

	function setDefaultOrder()
	{
		if (!isset($this->list_params['order']) || !$this->list_params['order']){
			
			if(isset($this->list_config['pview']) && $this->list_config['pview'] instanceof GW_Adm_Page_View && $this->list_config['pview']->order){
				$this->list_params['order'] = $this->list_config['pview']->order;
			
			}elseif (method_exists($this->model, 'getDefaultOrderBy')){
				$this->list_params['order'] = $this->model->getDefaultOrderBy();
			}
		}
	}
	
	

	
	
	function __viewsSearchPaths()
	{
		$path = implode('/', $this->module_path);
		$path_clean = implode('/', $this->module_path_filtered);
				
		return array_unique([$path, $path_clean, $this->module_path_clean]);
	}
	//uzkrauna sarasui viewsus
	//viewsai savyje turi pavadinima, salyga, rikiavima, suskaiciuoti direktyva, 
	function loadViews($page=false)
	{		
		if(!$page)
			$page = $this->app->page;
			
		$pview0 = GW_Adm_Page_View::singleton();
		$views = $pview0->getByPath($this->__viewsSearchPaths());
		
		
		
		foreach ($views as $i => $view) {
			
			//calculate results
			if ($view->calculate) {
				$key = $this->app->page->path . '::views::' . $view->id;

				if (!($view->count_result = GW_Session_Cache::get($key)) || isset($_GET['recalc_views'])) {
					
					try{							
						$filtercond = GW_DB::prepare_query(GW_DB::buidConditions($this->filters, ' AND '));
						
						//d::dumpas($filtercond);
						
						$cond = GW_DB::mergeConditions($filtercond, $view->condition, "AND");
						//d::dumpas($cond);
						
						
						$view->count_result = $tmp = $this->model->countExt($cond);
					} catch (Exception $e) {
						$this->setError("Can't calculate '$view->title' {$e->getMessage()}");
						$view->count_result = $tmp = "!Err";
					}
					
					GW_Session_Cache::set($key, $tmp, '10 seconds');
				}
			}
		}
				
		if(!$views)
			$this->createRegularPageView();
		
		
		if(!isset($this->list_config['pview']))
		{
			if($pview = $this->getDefaultPageView()){
				$this->setPageView($pview);
				//$this->setPlainMessage("Pview {$pview->id} was set");
			}
		}
		
		$this->tpl_vars['views'] = & $views;
	}

	function setPageView($pview)
	{
				
		if(isset($_GET['nopview']))
			return false;
		
		$this->list_params['pview'] = $pview->id;
		$this->list_config['pview'] = $pview;
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
	}

	function setPageViewAnd($pview)
	{
		if(isset($this->list_params['pviews_and'][$pview->id])){
			unset($this->list_config['pviews_and'][$pview->id]);
			unset($this->list_params['pviews_and'][$pview->id]);
		}else{
			$this->list_config['pviews_and'][$pview->id] = $pview;
			$this->list_params['pviews_and'][$pview->id] = $pview->id;
		}
	}
	
	function doSetView()
	{
		//$this->initListParams(false, 'list');
		$this->prepareListConfig();
		$this->loadViews();
		
		$pview = GW_Adm_Page_View::singleton()->selectById($this->tpl_vars['views'], $_REQUEST['view_id']);
		
		if(isset($_GET['shift_key'])){
			$this->setPageViewAnd($pview);
		}else{
			$this->setPageView($pview);
		}
		

		unset($_GET['view_id']);
		//session_write_close();
		
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
		$this->setDefaultOrder(); //for template
		
		if(isset($_GET['form_ajax']) && !isset($params['ajax_one_item_list']))
		{
			$this->processView('form');
			exit;
		}
		
		
		$this->setListParams($params);

		$cond = isset($params['conditions']) ? $params['conditions'] : false;

		//d::Dumpas($cond);

		if(!isset($this->no_key_field))
			$params['key_field'] = $this->model->primary_fields[0];

		$params['soft_error'] = true;
		
		if(isset($_GET['ajax_row']))
		{
			$this->tpl_vars['ajax_rows_only'] = 1;
			$params['ajax_one_item_list'] = $_GET['ajax_row'];
		}

		
		if(isset($params['ajax_one_item_list'])){
			$cond = ($cond ? "($cond) AND ":'').GW_DB::prepare_query(['a.id=?',$params['ajax_one_item_list']]);
		}

		$list = $this->model->findAll($cond, $params);
		
		
		
		
		if($this->model->is_db_based){
			$last_querty=$this->model->getDB()->last_query;
		}
		
		if(isset($this->app->sess['debug']) && $this->app->sess['debug'] )
		{
			echo SQL_Format_Helper::format($last_querty);
		}
		

		if ($list === null) {
			$this->list_params = [];

			if ($this->app->user->isRoot())
				$this->setError("Last query: " . $this->model->getDB()->error_query);

			foreach($this->model->errors as $error)
				$this->setError($error);
			
			return false;
		}

		if ($this->list_params['page_by'])
			$this->tpl_vars['query_info'] = $this->model->lastRequestInfo();
		
		
		
		$this->prepareCounts($list);
		
			
		$this->fireEvent('AFTER_LIST', $list);	
		
		return ['list' => $list];
	}
	
	
	function isListEnabledField($field)
	{
		return ($this->list_config['display_fields'][$field] ?? 0) ==1;
	}
	
	function prepareCounts($list)
	{
		
		if($this->isListEnabledField("changetrack")){	
			//get first item
			foreach($list as $item)
				break;
			
			if( isset($item) && $item && $item->extensions['changetrack'])
				$item->extensions['changetrack']->prepareList($list);
		}	
		
		if($this->isListEnabledField("orders")){
			
			$t = GW_Order_Item::singleton()->table;
			$t1 = $this->model->table;
			$sql = "SELECT obj_id, count(*) FROM `$t` WHERE obj_type='$t1' GROUP BY obj_id";
			$rez = GW::db()->fetch_assoc($sql);
			$this->tpl_vars['counts']['orders'] = $rez;
			//d::dumpas($this->tpl_vars['counts']);
			
		}		
		
	}
	

	function common_doMove($params = false)
	{
		if (!($item = $this->getDataObjectById()))
			return $this->jump();

		$item->move($_REQUEST['where'], $this->getMoveCondition($item));
		unset($_GET['where']);

		$this->jump(false, ['id' => $item->get('id')]);
	}

	/**
	 * Atvaizdavimui list view'se paruošiamas stulpelių sąrašas pagal išsaugotus nustatymus 
	 * i funkcija paduodamas turimas laukeliu sarasas,
	 * pagal sesijoje arba pageview'se saugomu laukeliu masyva atrenkami laukeliai rodymui
	 * 
	 */
	function getDisplayFields(&$fields, $onlyenabled=1)
	{	
		$saved = [];
		
		//d::dumpas($this->tpl_vars['views']);
		//d::dumpas("test");

		if(isset($_GET['pview'])){
			$pview = $this->getPageView4use();
			$saved = json_decode($pview->fields, true);
		}
		elseif(isset($this->list_params['fields']))
		{
			$saved = $this->list_params['fields'];
		}
		elseif(isset($this->list_config['pview']) && $this->list_config['pview'] instanceof GW_Adm_Page_View)
		{	
			$pview = $this->list_config['pview'];
			$saved = (array) json_decode($pview->fields, true);
			
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
			if($onlyenabled){
				if ($enabled)
					$rez[] = $id;
			}else{
				$rez[] = $id;
			}
						
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
		
		
		if($item->readonly ?? false)
			$this->jump();
		
		if($pageviewid=$_POST['pageviewid'])
		{
			$avail = $this->getImportantPageViews();
			if(isset($avail['list'][$pageviewid]))
			{
				$pageview = $avail['list'][$pageviewid];
				$pageview->fields = json_encode($fields);
				$pageview->order = $this->list_params['order'];
				$pageview->save();
				$this->setPlainMessage(GW::l('/g/UPDATE_SUCCESS').' ('.GW::l('/g/PAGE_VIEW').' - "'.$pageview->title.'")');
				
			}else{
				$this->setError('/g/CANT_SAVE_TO_PAGE_VIEW_BADID');
			}
		}
		

		$this->jump();
	}
	
	//---------------------page views functions-----------------------------
	
	//gauti vienam is pathsu (ieskoma eiles tvarka)
	function getPageView($paths, $default=false, $regular=false)
	{
		$extracond = "";
		
		if($default==1)
			$extracond .= "`default`=1 AND ";
		
		if($regular==1)
			$extracond .= "`regular`=1 AND ";
		
		foreach($paths as $path)
		{
			$pview=GW_Adm_Page_View::singleton()->find(["$extracond path=?", $path]);
			if($pview)
				return $pview;
		}
	}
	
	function createRegularPageView($path=null)
	{
		$pview = GW_Adm_Page_View::singleton()->createNewObject();	
		$pview->path = $path===null ? $this->__viewsSearchPaths()[0] : $path;
		$pview->title = GW::l('/g/PAGE_VIEW_REGULAR');
		$pview->title_short = ''; // rodomas title nei sitas neuzsetintas

		$pview->active = 1;
		$pview->priority = 100;
		$pview->default = 1;
		$pview->regular = 1;
		$pview->save();
		
		return $pview;
	}
		
	function getRegularPageView($createIfNotExists=false){
		
		$paths = $this->__viewsSearchPaths();
		
		if(!($pview=$this->getPageView($paths, false, true)))
		{
			if($createIfNotExists){
				$pview = $this->createRegularPageView($paths[0]);
			}else{
				return null;
			}
		}

		return $pview;	
	}
	
	function getDefaultPageView($createIfNotExists=false)
	{
		$paths = $this->__viewsSearchPaths();
		
		if(!($pview=$this->getPageView($paths, true, false)))
		{
			if($createIfNotExists){
				$pview = $this->createRegularPageView($paths[0]);
			}else{
				return null;
			}
		}

		return $pview;
	}
	
	function getCurrentPageView()
	{		
		if(isset($this->list_config['pview']) && $this->list_config['pview'])
			return $this->list_config['pview'];
	}

	function getImportantPageViews()
	{
		$arr = [];
		
		if($current = $this->getCurrentPageView()) $arr[$current->id] = $current;
		if($tmp1 = $this->getDefaultPageView()) $arr[$tmp1->id] = $tmp1;
		if($tmp2 = $this->getRegularPageView()) $arr[$tmp2->id] = $tmp2;
		

		return ['list'=>$arr, 'current'=>$current];		
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
		$this->loadViews();
		$this->prepareListConfig();
				
		$fields = $this->list_config['display_fields'];
				
		
		$saved = isset($this->list_params['fields']) && $this->list_params['fields'] ? (array) $this->list_params['fields'] : [];
		
		//test
		//&act=doresetListVars
		

		$page_views = $this->getImportantPageViews();	
				
		$this->tpl_vars['page_views'] = $page_views['list'];
		
		
		if($page_views['current']){
			$tmp = json_decode($page_views['current']->fields, true);
						
			if($tmp && is_array($tmp)){
				$this->tpl_vars['current_page_view_id'] = $page_views['current']->id;
				$tmp = $saved;
			}
		}
		
		
		foreach($saved as $fieldname => $x)
			if(!isset($fields[$fieldname]))
				unset($saved[$fieldname]);		

		
		$this->tpl_vars['fields'] = $saved + $fields;
		
		if(isset($this->list_config['pview']) && $this->list_config['pview'] instanceof GW_Adm_Page_View && $this->list_config['pview']->order){
			$this->list_params['order'] = $this->list_config['pview']->order;

		}elseif (method_exists($this->model, 'getDefaultOrderBy')){
			$this->list_params['order'] = $this->model->getDefaultOrderBy();
		}		
		
		$this->__viewDialogConfigPrepareOrders();

		$this->tpl_file_name = GW::s("DIR/" . $this->app->app_name . "/TEMPLATES") . "list/dialogconfig";
	}	
	

	function getMoveCondition($item)
	{
		return GW_DB::buidConditions($this->filters, ' AND ');
	}

	function methodExists($name)
	{
		return method_exists($this, $name) || isset($this->allow_auto_actions[$name]);
	}


	/// siame metode turetu buti apsaugomas irasas
	// -  nuo redagavimo arba pasalinimo jei per sistema prie vartotojo grupes nera nustatyta write teise - ateis teisiu komplektacija per access_level
	// - jei irasas turi access stulpeli ? norint rasyti i irasa turetu tenkinti ir access_level ir  access stulpelio leidimas
	
	function isOwner($item)
	{
		return ($item->user_id == $this->app->user->id) || ($item->admin_id == $this->app->user->id);
	}
	
	function canBeAccessed($item, $opts=[])
	{
		
		$result = false;
		$debug = $this->app->sess['debug'] ?? false;
		
		if($item && $item->id){
			$item->load_if_not_loaded();
		}
		
		$requestAccess = $opts['access'] ?? GW_PERM_WRITE;
		
		
		
		
		if($debug){			
			$info =[
			    'access_level'=>$this->access_level,
			    'request'=>$requestAccess,
			    'access_level_matches_request'=>$this->access_level & $requestAccess ? 'Y':'N'
				];
				
			if($item){
				$info['item'] = $item->id;
				$info['isOwner'] = $this->isOwner($item) ? 'Y':'N';
				
				
				if(isset($item->content_base['access'])){
					$info['item_has_own_permissions'] = $item->content_base['access'];
					$availAccess = $item->content_base['access'];
					//1-read, 2-write check //admin/config/main.php for permission list
					$info['item_has_own_permissions_test'] = $availAccess & $requestAccess ? 'PASS':'DENY';
				}
				

				
			}else{
				$info['canCreateNew'] = $this->canCreateNew? 'Y':'N';
				$info['path'] = implode('/',$this->module_path);
			}
			
			if(isset($info['action']))
				$info['action'] = $opts['action'];
			
			if($item && method_exists($this, "checkOwnerPermission") && ($tmp=$this->checkOwnerPermission($item, $opts)) > 0){
				if($tmp == 1)
					$info['checkOwnerPermission']='PASS';

				if($tmp == 2)
					$info['checkOwnerPermission']='DENY';

			}				
			
			if(!$this->app->user->isRoot() && GW_Permissions::getTempReadAccessMod(implode('/',$this->module_path)) ){
				$info['readOnlyAccess']= $this->readOnlyAccess($item, $opts);
			}			
			
			
			
			if($item && $item->protected &&  $requestAccess == GW_PERM_REMOVE){
				$info['protected_item_cant_remove']=1;
			}
			
			d::ldump($info);
		}
		
		if($item && $item->protected &&  $requestAccess == GW_PERM_REMOVE){
			//kai bandoma suzinoti grazinti tiesiog false
			//kai bandoma ivykdyti galetu ir error mesti
			//$this->setError(GW::l('/G/validation/ITEM_IS_PROTECTED_CANT_REMOVE'));
			return false;
		}		
		
		//permit new item creation
		if(($opts['action'] ?? false)=='create_new' && $this->canCreateNew){
			return true;
		}
		
		//without this line form will be disabled for new item
		if($item && !$item->id && $this->canCreateNew){
			return true;
		}
		
	
		//reiktu pertvarkyt kad butu atskiros teises kai owneris ir is tos pacios grupes, ir kai visiems irasams
		//panasiai kaip failu sistemoje - permissionai katalgui
		//prie kiekvieno iraso tada turetu turbut buti vartotoju grupes id arba funkcija kuri pagal kiekvieno atskirai modulio logika
		//galetu patikrint ar yra susijes su tam tikra grupe		
		
		//grant - 1
		//deny - 2
		if($item && method_exists($this, "checkOwnerPermission") && ($tmp=$this->checkOwnerPermission($item, $opts)) > 0){
			if($tmp == 1)
				return true;
			
			if($tmp == 2)
				return false;
			
		}
			
		if($this->canAccessIfOwner && $item && $this->isOwner($item))
			return true;

		
		
		if(($this->app->user && !$this->app->user->isRoot()) && GW_Permissions::getTempReadAccessMod(implode('/',$this->module_path)) ){
			return $this->readOnlyAccess($item, $opts);
		}
		
		$result0 = ($this->access_level & $requestAccess) || ($this->app->user && $this->app->user->isRoot());
		

		
		if(isset($item->content_base['access']))
		{
			$availAccess = $item->content_base['access'];
			//1-read, 2-write check //admin/config/main.php for permission list
			$result1 = $availAccess & $requestAccess;
				
			
			$result =  $result0 &&  $result1; 
				
		}else{
			$result = $result0; //$item->canBeAccessedByUser($this->app->user);
		}	
		
		if($this->allowed_ids ?? false){
			if(isset($this->allowed_ids[$item->id])){
				return $this->allowed_ids[$item->id] & $requestAccess;
			}else{
				$result = false;
			}
		}
				
		//d::dumpas([$result, $this->allowed_ids, $item->id]);
		
		if (isset($opts['nodie']) || $result)
			return $result;

		
		$this->setError(($item ? $item->id:'-').' '.GW::l('/G/GENERAL/ACTION_RESTRICTED'));
		$this->jump();
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
	
	function __call($name, $arguments)
	{
		$name = strtolower($name);
		
		if (isset($this->allow_auto_actions[$name])){
			return call_user_func_array([$this, "common_$name"], $arguments);
		}elseif($ext = $this->scanRedirRules($name)){			
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

	function doGetFilters()
	{
		
		$this->fireEvent("BEFORE_GET_FILTERS");
		$this->initFieldTypes();
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
		$items = explode(',', $_REQUEST['ids']);
		$this->tpl_vars['ids'] = $_REQUEST['ids'];
		$this->tpl_vars['items_count'] = count($items);
		
		$this->tpl_file_name = GW::s("DIR/" . $this->app->app_name . "/TEMPLATES") . "list/dialogremove";
	}
	
	function doDialogRemove()
	{
		$ids = explode(',',$_REQUEST['ids']);
		
		
		$cnt = 0;
		foreach($ids as $id)
		{
			$item = $this->model->createNewObject($id, true);
			
			if($this->canBeAccessed($item,['nodie'=>1])){
				$this->fireEvent("BEFORE_DELETE", $item);
				
				$item->delete();
				$cnt++;
			}		
		}
		
		$this->setPlainMessage(sprintf(GW::l('/g/SELECTED_ITEMS_REMOVED'), $cnt), GW_MSG_SUCC);
		$this->app->jump();
	}	
	
	
	
	
	function initFilterByType()
	{
		if($this->list_config['field_types'] ?? false){
			
			//galima butu pagerint 
			//kad like operacija isimt tinyint variantams
			//pagal data dadet rangetipa
			//panaudoti fieds/inputs konfiguracija
			
			
			foreach($this->list_config['dl_filters'] as $field => $filtercfg){
				if(isset($this->list_config['field_types'][$field]) && in_array($this->list_config['field_types'][$field], ['datetime','date']) && $filtercfg==1){
					$this->list_config['dl_filters'][$field]=[
					    'addct'=>['DATERANGE'=>'RANGE']
					];
				}
			}
		}			
	}
	
	/**
	 * 
	 * užkrauna $this->list_config kuriame yra rodomi stulpeliai ir jų eiliškumas
	 * filtrai, ir rikiuotinų stulpelių sąrašas
	 * 
	 * paimamas laukelių konfigas
	 * $item paduodama tuo atveju jei paruosiama formos inputai
	 * 
	 * 
	 * @return boolean
	 */
	function prepareListConfig($item=false)
	{
		//neuzkraut jei jau uzkrautas
		if(isset($this->list_config['dl_fields']))
			return false;
		
		$conf = $this->getListConfig($item);
		
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
		
		
		$this->list_config['dl_fields'] = $this->getDisplayFields($display_fields);
		$this->list_config['display_fields'] = $display_fields;
		$this->list_config['inputs'] =& $conf['inputs'];
		
		$this->list_config['dl_order_enabled_fields'] = $order_enabled;		
		$this->list_config['dl_filters'] = $filters;
		

		$this->initFilterByType();
		
		$this->eventHandler("AFTER_LIST_CONFIG", $item);
		
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
		
		
		if($this->model && $this->model->extensions && isset($this->model->extensions['changetrack'])){
			$cfg['fields']['changetrack'] = 'L';
		}
		
		
		if(GW::s('MULTISITE') && $this->multisite)
			$cfg['inputs']['site_id']=['type'=>'select_ajax','options'=>[],'modpath'=>'sitemap/sites','preload'=>1];
		
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
		if(isset($options['ifvisible']))
		{
			if(!($this->list_config['display_fields'][$fieldname] ?? false))
				return false;
		}	
		
		$ids = [];
		foreach($list as $itm){
			if($itm->$fieldname)
			{
				if(is_array($itm->$fieldname)){
					$ids = array_merge($ids, $itm->$fieldname);
				}else{
					$ids[] = $itm->$fieldname;
				}
			}
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
		$opts =[];

		if($object!=get_class($this->model))
			$opts['no_alias']=1;
		
		foreach($searchInFields as $fieldname){
			$cond[] = $this->buildCond($fieldname, $compare_type, $value, true, true, $opts);
		}
		
		$cond = '('.implode(' OR ', $cond).')';
		
		$ids = $object::singleton()->findAll($cond,['select'=>'id','return_simple'=>1,'key_field'=>'id','soft_error'=>1]);
		
		
		if(!$ids && $object::singleton()->errors)
			foreach($object::singleton()->errors as $err)
				$this->setError($err);
		
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
		$this->app->jump("system/page_views", ['path'=>$this->app->path]);
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
		//$this->initListParams(false, 'list');//jau ir taip is list kvieciama
		$this->setListParams($params);
		$this->prepareListConfig();
		$this->fireEvent("AFTER_LIST_PARAMS", $params);
		
		$cond = $params['conditions'];
				
		$id = 0;
		
		$vals = [];
		
		if(isset($_GET['update']))
		{
			if($this->list_config['pview'] instanceof GW_Adm_Page_View)
			{
				$id = $this->list_config['pview']->id;
				$vals = $this->list_config['pview']->toArray();
			}
		}
		
		$args = isset($_GET['saveasorder'])? '&saveasorder=1':'';
		
		if($vals)
			$args .= $args.'&update=1';
		
		$args.='&path='.rawurlencode($this->app->path);
		
		$url = $this->app->buildUri("system/page_views/form?id={$id}&clean=2".$args);
		
		
		//trys galimi path 
		
		$vals['condition'] = $cond;
		$vals['order'] = $params['order'];
		$vals['path_options'] = $this->__viewsSearchPaths();
		$vals['fields'] = json_encode($this->__currentFields());
		$vals['page_by'] = $this->list_params['page_by'];
		
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
		
		//$this->loadOrders();
		$orders = $this->tpl_vars['list_orders'] ?? [];
		
		$foundorder = false;

		if (isset($_REQUEST['order'])) {

			if (!$this->__validateOrder($_REQUEST['order'], $this->list_config['dl_order_enabled_fields'] + $this->model->getColumns())) {
				$this->setError('/g/GENERAL/BAD_ORDER_FIELD');
				$this->jump();
			} else {
				$this->list_params['orders']['name'] = 'NIEKAS';
				
				//papildyt rikiavima papapildomu stulpeliu
				if(isset($_GET['shift'])){
					
					$curr = $this->list_params['order'];
					list($col,$dir) = explode(' ', $_REQUEST['order']);
										
					if(strpos($curr, $col)!==false){
						$this->list_params['order'] = str_replace(["$col ASC","$col DESC"], $_REQUEST['order'], $curr);
					}else{
						$this->list_params['order'] = $curr.', '.$_REQUEST['order'];
					}
					
				}else{
					$this->list_params['order'] = $_REQUEST['order'];
				}
			}
		}



		unset($_GET['name']);

		$this->jump();
	}
	
	function getModelCols($type='all')
	{
		//d::dumpas([$this->extra_cols, $this->model->getColumns($type)]);
		return $this->extra_cols+$this->model->getColumns($type);
	}
	
	
	/**
	 * Search Replaces is in multiple places 
	 * gwcms.js:gw_adm_sys.init_list() , gwcms.js:gwSearchReplace()
	 * templates/list/configure_menu.tpl
	 * default/tpl/searchreplace.tpl
	 * searchreplace=1 - nustatomas filtras laukiama vartotojo patvirtinimo kad rasti rezultatai ok
	 * searchreplace=2 - atfiltruojami pakeisti irasai perziureti ar korektiskai ivyko keitimas
	 * while search replace is active filters is disabled
	 * todo: use module elements.tpl for select fields
	 * todo: remove non varchar fields, non numeric fields | get col types from information schema
	 * todo: add search_replace_na_fields=[id,insert_time,update_time] public variable
	 */
	function common_viewSearchReplace()
	{
		$this->initListParams(false,'list');
		$this->prepareListConfig();
		
		$fields = [];
		$ignore = ['id'=>1,'insert_time'=>1,'update_time'=>1];
		
		foreach($this->list_config['dl_fields'] as $field)
		{
			if(isset($ignore[$field]))
				continue;
			
			$fields[$field] = $this->fieldTitle($field);
		}
				
		$this->options['fields'] = $fields;
		$this->default_tpl_file_name = GW::s("DIR/".$this->app->app_name."/MODULES")."default/tpl/searchreplace";
		
		$sr =& $this->list_params['searchreplace'];
			
		
		//apsauga jei nuimtu filtrus
		if(isset($_GET['searchreplace']) && $_GET['searchreplace']==1 && !$this->getFilterByFieldname($sr['fieldname']))
		{
			$_GET['searchreplace']=0;
		}
	}
	
	function doSearchReplace()
	{
		$this->list_params['searchreplace'] = $_POST['item'];
		$sr =& $this->list_params['searchreplace'];
		
		
		$this->prepareListConfig();			
		$avail_fields = $this->__currentFields();		
		
		//security use only available fields
		if(!isset($avail_fields[$sr['fieldname']])){
			$this->setError($this->fieldTitle($sr['fieldname']).' is not available for this operation');
			$this->jump();
		}
	
		
		//d::dumpas($this->list_params['searchreplace']);
		
		$this->list_params['filters'] = [];
		$this->list_params['page']=1;
		
		$this->setFilter($sr['fieldname'], $sr['searchval'], 'LIKE');
		
		
		if(isset($sr['confirm'])){

			$list = $this->model->findAll([GW_DB::escapeField($sr['fieldname']).' LIKE ?', '%'.$sr['searchval'].'%']);
			
			if($sr['items_count']!=count($list)){
				$this->setError("Search &amp; replace security stop: Number filtered: {$sr['items_count']} does not match real number of matching items: ".count($list));

				$this->jump();
			}
			
			$ids = [];
			
			foreach($list as $item)
			{
				$newval = str_ireplace($sr['searchval'], $sr['replaceval'], $item->get($sr['fieldname']));
				$item->set($sr['fieldname'], $newval);
				$item->update([$sr['fieldname']]);
				$ids[] = $item->id;
			}
			
			$this->list_params['filters'] = [];
			$this->setFilter('id', $ids, 'IN');
			
			$this->jump(false, ['searchreplace'=>2,'filterhide'=>1]);
		}
		
		$this->jump(false, ['searchreplace'=>1,'filterhide'=>1]);
	}	
	
	function doCancelSearchReplace()
	{
		unset($_GET['searchreplace']);
		unset($_GET['filterhide']);
		
		$this->list_params['searchreplace'] = null;
		unset($this->list_params['searchreplace']);
		$this->list_params['filters'] = [];
		$this->list_params['page']=1;
		$this->jump();
	}
	
	function doShowLogFile($jump=true)
	{
		$file = basename($this->lgr->file);
		$this->setMessage("<iframe src='/admin/lt/system/logwatch/iframe?id=$file&padding=1' style='width:100%;height:200px;'></iframe>");
				
		if($jump)
			$this->jump();
	}
	
	public $options_search_field="title";
	
	function viewOptions()
	{
		$i0 = $this->model;
		
		
		if(
			!GW_Permissions::testPermission($this->access_level, GW_PERM_OPTIONS) &&
			!GW_Permissions::testPermission($this->access_level, GW_PERM_READ)
		){
			return $this->setMessage('No options / no read permission');
		}

			
		
		$opts = method_exists($this, 'getOptionsCfg') ? $this->getOptionsCfg() : [];
		
		$idx_field = $opts['idx_field'] ?? 'id';
		
		$params = [];
		$cond = "";
		
		
		
		if(isset($_GET['q'])){
			$exact = GW_DB::escape($_GET['q']);
			$search = "'%".$exact."%'";
	
			
			if(isset($opts['search_fields'])){
				foreach($opts['search_fields'] as $field){
					if(strpos($field, '`')===false && strpos($field, '.')===false)
						$field = "`$field`";
					
					$condarr[] = "$field LIKE $search";
				}
				
				if($joins=$this->model->findJoinsForFields($opts['search_fields'])){
					$params['joins'] = $joins;
				}
				
				$cond = '('.implode(' OR ', $condarr).')';
				
				//simple cond sample:
				//(team_name LIKE '%%'; OR partic1.name LIKE '%%'; OR partic2.name LIKE '%%'; OR partic1.surname LIKE '%%'; OR partic2.surname LIKE '%%';) AND `event_id`=6
			}elseif(isset($opts['search_fields'])){
					
				$cond = ($opts['search_field'] ?? $this->options_search_field)." LIKE $search";	
				
			}elseif(isset($i0->i18n_fields['title'])){
	
				$cond = $i0->buildFieldCond('title',$search);
			}else{
				$cond='1=1';
			}
			

			if($opts['condition'] ?? false){
				$cond = GW_DB::mergeConditions($opts['condition'], $cond);
			}
			
			if($opts['include_ids']??false){
				$cond = GW_DB::mergeConditions($cond, GW_DB::inCondition('`a`.`id`',$opts['include_ids']), 'OR');
			}
			
		}elseif(isset($_REQUEST['ids'])){
			$ids = json_decode($_REQUEST['ids'], true);
			if(!is_array($ids))
				$ids = [$ids];

			//$ids = array_map('intval', $ids);
			$cond = GW_DB::inConditionStr($idx_field, $ids);
		}
		
		
		if(!isset($_REQUEST['ids'])){
			$page_by = $opts['page_by'] ?? 30;
			$page = isset($_GET['page']) && $_GET['page'] ? $_GET['page'] - 1 : 0;
			$params['offset'] = $page_by * $page;
			$params['limit'] = $page_by;	
		}
		

		if(isset($opts['condition_add'])){
			$cond .= ($cond ? " AND " : ''). $opts['condition_add'];
		}	
				
		
	
		if($opts['order'] ?? false)		
			$params['order'] = $opts['order'];
		
		//d::dumpas($params);
		if(isset($_GET['debug']) && $this->app->user->isRoot()){
			$res['params'] = $params;
		}		
		
		
		$list0 = $i0->findAll($cond ?? '', $params);
		$q = GW::db()->last_query;
	
		
		if(isset($_GET['debug']) && $this->app->user->isRoot()){
			$res['params'] = $params;
			$res['dbquery'] = $q;
		}
		if(isset($opts['list_process'])){
			$opts['list_process']($list0);
		}
		
		$list=[];
		
					
		foreach($list0 as $item)
			$list[]=[
			    'id' => $item->get($idx_field), 
			    "title" => isset($opts['title_func']) ? $opts['title_func']($item) : $item->get("title")
			];
		
		$res['items'] = $list;
		
		$info = $this->model->lastRequestInfo();
		$res['total_count'] = $info['item_count'];
		
	
		if(isset($_GET['debug'])){
			header('content-type: text/json');
			//$res['q'] = $q;
			echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		}else{
			echo json_encode($res);
		}
		
		exit;
	}	
	
	
	
	function eventHandler($event, &$context) 
	{
		if($this->ext_events)
			foreach($this->ext_events as $ext => $x)
				$this->ext($ext)->extEventHandler($event, $context);
		
		
		switch($event){
			case 'BEFORE_SAVE':
				if($this->auto_translate_enabled)
					$this->autoTranslate($context);
			break;
			
			case 'BEFORE_DELETE':
				if($this->item_remove_email ?? false)
					$this->recoveryEmail($context);
				
				if($this->item_remove_log ?? false)
					$this->recoveryLog($context, $this->app->user ?? false);
						
			break;
			
			case 'AFTER_LIST':
				
				//changetrack extension
				if($this->isListEnabledField("changetrack"))
					$this->tpl_vars['dl_output_filters']['changetrack'] = 'changetrack';
				
				
				
				if($this->isListEnabledField("site_id")){
					$this->tpl_vars['dl_output_filters']['site_id'] = 'site_id_key';
				}
								
				
				if($this->dynamic_fields)
					$this->initDynFieldsList($context);
			break;
			
			case 'AFTER_LIST_CONFIG':
				
				if($this->multisite && GW::s('MULTISITE') && ($this->list_params['search'] ?? false)){
					$this->list_config['display_fields']['site_id'] = 1;
					
					if(!in_array('site_id', $this->list_config['dl_fields']))
						$this->list_config['dl_fields'][]='site_id';
				}
			break;
			
			case 'AFTER_FORM':
				$item = $context;
				if( isset($item) && $item && isset($item->extensions['changetrack'])){
					$this->tpl_vars['change_track_cnt'] = $item->extensions['changetrack']->prepareCountByField();
					
					
				}
			break;
		}
		
		
		parent::eventHandler($event, $context);
	}
	
	
	function viewVersions()
	{
		
		$item = $this->getDataObjectById();
		$this->tpl_vars['item'] = $item;
		
		$changes = $item->extensions['changetrack']->getChangesByField($_GET['field']);
		
		$this->tpl_vars['changes'] = $changes;
		
		$this->default_tpl_file_name = GW::s("DIR/".$this->app->app_name."/MODULES")."default/tpl/versions";
		
	}
	
	function viewVersion()
	{
		$item = $this->getDataObjectById();
		$field = $_GET['field'];
		$this->tpl_vars['item'] = $item;
		
		list($pastversion, $changesitm) = $item->extensions['changetrack']->getRevertedContent($field, $_GET['changeid']);
		$this->tpl_vars['changesitm'] = $changesitm;
		$this->tpl_vars['changes_user'] = GW_User::singleton()->find($changesitm->user_id);
			
		$this->tpl_vars['pastversion'] = $pastversion;
		$this->tpl_vars['headversion'] = $item->get($field);
		
		$this->tpl_vars['field'] = $_GET['field'];
		
		
		$this->default_tpl_file_name = GW::s("DIR/".$this->app->app_name."/MODULES")."default/tpl/version";
	}

	function doWriteLock()
	{
		if(!$this->app->user->isRoot())
			$this->setError("Error root user only");
		
		$item = $this->getDataObjectById();
		$bitmask = $item->access;
		$bitmask &= $bitmask ^ GW_PERM_WRITE;
		$item->access = $bitmask;
		$item->updateChanged();
		$this->jump();
	}
	
	function doWriteUnlock()
	{
		if(!$this->app->user->isRoot())
			$this->setError("Error root user only");


		$item = $this->getDataObjectById(true, false, GW_PERM_READ);
		$bitmask = $item->access;
		$bitmask |=  GW_PERM_WRITE;
		$item->access = $bitmask;
		$item->updateChanged();
		$this->jump();
		
	}
	
	
	/*
	 * function runPeriodicTasks(modconfig variable)
	 * task is stored in modconfig 
	 * example run consistently:
	 * submodule?act=doSomeAction;other_submodule?act=doSomthing
	 * IMPORTANT TASK SHOULD output json
	 * 
	 * example run parallel:
	 * #submodule?act=doSomeAction;#other_submodule?act=doSomthing
	 */

	function runPeriodicTasks($task_store)
	{
		$tasks = $this->modconfig->{$task_store};
		
		$tasks = explode(';', $tasks);	
		$t = new GW_Timer;
			
		$mod = $this->module_path[0];
		
		foreach($tasks as $task)
			if($task){
				if(substr($task, 0,1)=='#'){
					//parallel
					$task = substr($task, 1);
					$url=Navigator::backgroundRequest("admin/lt/$mod/$task", ["cron"=>1]);
				}else{		
					//consistently
					$url = Navigator::buildURI("admin/lt/$mod/$task", ["cron"=>1]);
					$resp = Navigator::sysRequest($url);
					
					if($resp = json_encode($resp))	
						$this->setMessage($url.': '.$resp);						
				}	
			}
		
		$this->setMessage("Took ".$t->stop().' secs');			
	}
	
	function doCronRun()
	{
		$this->runPeriodicTasks("tasks_".$_GET['every'].'min');
	}
	
	function prompt($form, $title=false, $opts=[])
	{
		
		
		if($title===false){
			$caller = '<b>'.debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS,2)[1]['function'].'()</b>';
			$title = GW::l('/g/DEFAULT_PROMPT',['v'=>['caller'=>$caller]]);
		}
		
		$answers = true;
		
		if(isset($opts['rememberlast'])){
			$lastasnwers = json_decode($this->modconfig->prompt_lastanswers, true);
		}
		
		
		$this->tpl_vars['method'] = $method =  $opts['method'] ?? 'get';
		
		if($method=='get'){
			$src =& $_GET;
		}else{
			$src =& $_POST;
		}
		
		
		foreach($form['fields'] as $fieldname => $el){
			if(($el['type'] ?? false)=='file'){
				$src =& $_POST;
				$this->tpl_vars['method'] = 'post';	
				
			}	
		}
		
		//if already have answers ?
		foreach($form['fields'] as $fieldname => $el){
			
			if(isset($lastasnwers[$fieldname])){
				$form['fields'][$fieldname]['default'] = $lastasnwers[$fieldname];
			}
			
			
			
			if( ($el['type'] ?? false)=='file' && isset($el['required']) && isset($_FILES[$fieldname])){
				continue;
			}
			
			if(isset($el['required']) && !isset($src['item'][$fieldname])){
				
				if(isset($src['item']))
					$this->setError("$fieldname not satisfied");
				
				$answers=false;
			}
		}
				
		
		//d::dumpas($answers);
		
		if($answers){
			if(isset($opts['rememberlast'])){
				$this->modconfig->prompt_lastanswers = json_encode($src['item']);
			}			
			
			
			return array_merge($src['item'] ?? [], $_FILES);
		}
		
		$this->tpl_vars['getargs'] = $_GET;
		unset($this->tpl_vars['getargs']['url']);
		
		$this->tpl_vars['item'] = (object)($src['item'] ?? []);
		$this->tpl_vars['prompt_fields'] = $form;
		$this->tpl_vars['prompt_title'] = $title;
		$this->smarty->assign('m', $this);
		$this->smarty->assign($this->tpl_vars);
					
	
		$tpl_name = GW::s("DIR/".$this->app->app_name."/TEMPLATES").'tools/prompt.tpl';
			
		$str =  $this->smarty->fetch($tpl_name);
		
		$this->setMessageEx(['text'=>$str, 'type'=>4]);
	}

	
	/** 
	* @param type $opts array : ['item'=>(object), 'field'=>(fieldname), 'src'=>(source lang), 'dest'=>(dest lang), 'check'=>(bool)]
	 */
	function getTranslation($opts)
	{
		extract($opts);
		
		if($check && (!$item->get($field,$src) || $item->get($field,$dest))){
			//$this->setMessage("Auto translation field $field. value from '$from' ($src) NO NEED");
			return false;
		}

		$title_array=[$from=$item->get($field, $src)];
		
		//$serviceurl = "https://serv2.menuturas.lt/services/translate/test.php";
		$serviceurl = "http://vilnele.gw.lt/services/translate/test.php";
		
		$opts = http_build_query(['from'=>$src,'to'=>$dest]);
		$resp = GW_Http_Agent::singleton()->postRequest($serviceurl.'?'.$opts, ['queries'=>json_encode($title_array)]);
		$resp = json_decode($resp);;
		
		$to=$resp[0];
		
		if($resp[0]){
			$item->set($field, "[A] ".$to , $dest);
			$item->updateChanged();	
			$this->setMessage("Auto translation field $field. value from '$from' ($src) to: '$to' ($dest)");
			
			return true;
		}else{
			$this->setError("Auto translation field $field. value from '$from' ($src) to: ($dest) failed");
		}		
	}
	
	

	function autoTranslate($item)
	{
		if(!isset($item->i18n_fields))
			return false;
		
		
		$cols =  $item->getFieldTypes();
		
		$upd = false;
		
		//['varchar','text'])
		
		$testsrc = function(&$m) use (&$item, &$field, &$ln, &$cols){
			//$val = $item->get($field, $ln);
			//$m->setMessage("test $field $ln: $val");
			
			if(isset($cols["{$field}_{$ln}"]) && !in_array($cols["{$field}_{$ln}"], ['varchar','text']))
					return 1;
			
			$val = $item->get($field, $ln);
			
			if($val == ""){
				return 2;
			}		
			
			if(is_numeric($val))
				return 3;
			
			if(preg_match('/\d{4}-\d{2}-\d{2}|\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $val))
				return 4;
			
			//too long
			if(strlen($val) > 500){
				return 5;
			}
			
			
			if(strpos($val,"\n") !==false){
				return 6; //dont translate textareas
			}
			
			return 0;
		};
		
		
		
		
		foreach($item->i18n_fields as $field => $x){
			
			$srcln =false;
			
			foreach(GW::s('LANGS') as $ln){
				if(!($reas=$testsrc($this))){
					$srcln = $ln;
					break;
				}//else{
				//	$this->setMessage("src reject $field, $ln. Reason $reas");
				//}
			}
			
			if(!$srcln)
				continue;
			
			$destlns = [];
			foreach(GW::s('LANGS') as $ln)
			{	
				if($ln==$srcln)
					continue;
				
					$upd |= $this->getTranslation (['item'=>$item,'field'=>$field, 'src'=>$srcln,'dest'=>$ln,'check'=>true]);
			}
			
			if($this->app->i18next){
				foreach($this->app->i18next as $ln =>$x)
					$upd |= $this->getTranslation (['item'=>$item,'field'=>$field, 'src'=>'en','dest'=>$ln,'check'=>true]);
			}
		}		
	}
	
	function doClone3()
	{		
		
		$item = $this->getDataObjectById();
		$origitem = $item;
		
		if(!$this->write_permission)
			return $this->setError('No write permission');
		
		$vals= $item->toArray();

		if(isset($item->i18n_fields['title'])){
				foreach(GW::s('LANGS') as $ln)
					$vals['title_'.$ln] = $vals['title_'.$ln].' ('.GW::l('/G/GENERAL/COPY').' orig_id:'.$vals['id'].')';		
		}
		
		$oldid = $vals['id'];
		
		unset($vals['id']);
		$item = $this->model->createNewObject();
		$item->setValues($vals);
		
		$ctx = ['src'=>$origitem,'dst'=>$item];
		
		$this->fireEvent(["BEFORE_CLONE","BEFORE_CLONE3"], $ctx);
		
		$item->insert();
		$newitemid = $item->id;
		
		$this->fireEvent(["AFTER_CLONE","AFTER_CLONE3"], $ctx);
		
		unset($_GET['id']);
		unset($_REQUEST['id']);
		
		$this->setMessage(GW::l('/G/GENERAL/ITEM_CLONE_CREATED',['v'=>['OLDID'=>$oldid,'NEWID'=>$newitemid]]));

		
		Navigator::jump($this->buildUri("$newitemid/form", ['id'=>$newitemid]));
	}	
	
	function incrementFindUniqueVal($field, $lastval)
	{
		for($i=1;$i<1000;$i++){
			$test = $lastval.'_'.$i;
			if(!$this->model->count(["`$field`=?", $test]))
				return $test;
		}
		
		return false;
	}
	
	function incrementSetUniqueField($ctx, $unique_key_field)
	{
		$source = $ctx['src'];
		$dest = $ctx['dst'];
		
		$newkey = $this->incrementFindUniqueVal($unique_key_field, $source->$unique_key_field);
		
		if($newkey==false){
			$this->setError("CLONE ABORT KEY TEST `$unique_key_field` FAIL");
			$this->jump();
		}
		
		$dest->$unique_key_field = $newkey;		
	}

	function recoveryEmail($item)
	{
		$pagetitle = $this->app->page->title;
		

		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_PROD)
		{
			$data = $this->getItemRecoveryData($item);

			$body = "Page path: <a hreg='".$this->buildUri()."'>".$this->buildUri().'</a>';
			
			
			$body .= "<br>Main item recovery tool: <a href='".$tmp."'>".$tmp.'</a> ';
			
			$tmp = $this->buildUri(false,['act'=>'doRecoverFromRecoveryLine']);
			$body .= "<br>Todo recovery tool: <a href='".$tmp."'>".$tmp.'</a> ';
			
			
			$body .= "<br>Recovery json: <hr>";
			$body .= '<small style="color:gray"><pre>'. json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</pre></small>';

			$admintitle = $this->app->user->title;

			$subject = GW::s('PROJECT_NAME')."  :: Env(".GW::s('PROJECT_ENVIRONMENT').") :: Item \"{$item->title}\" (id:{$item->id}) in module \"$pagetitle\" is removed";
			
			$mailopts = ['subject'=>$subject, 'body'=>$body, 'noStoreDB'=>1];

			
			$stat = GW_Mail_Helper::sendMailAdmin($mailopts);

			$this->setMessage([
			    'text'=>"Recovery email ".($stat?'sent':'failed')." to ".implode(',', $mailopts['to']), 
			    'type'=>$stat?GW_MSG_SUCC:GW_MSG_ERR, 
			    'float'=>1,
			    'footer'=>$mailopts['error'] ?? false
			    ]);
		}else{
			$this->setMessage(['text'=>"Environment not production, so recovery mail not sent", 'type'=>GW_MSG_INFO,'float'=>1]);
		}		
	}
	
	function getItemRecoveryData($item)
	{
		if(!method_exists($item, 'getRecoveryData'))
			return false;
		
		$data = $item->getRecoveryData();
		
		$this->fireEvent("AFTER_RECOVERY_DATA_COLLECT", $data);
		
		return $data;
	}
	
	function recoveryLog($item, $user=false, $recoveryReason='')
	{
		if(!$user)
			$user=$this->app->user;
		
		$this->initLogger();
		
		$data = $this->getItemRecoveryData($item);
				
		$this->lgr->msg(($user ? "Delete item user: {$user->id}. {$user->title}." : '') .($recoveryReason?' ('.$recoveryReason.')':'')." Recovery line:");	
		$this->lgr->msg(json_encode($data));		
	}	
	
	
	
	function recoverData($data)
	{
		
		d::ldump('todo: implement');
		d::dumpas($data);
		
		$this->setMessage($msg = "Import done, cnt: ".count($entries).", speed: {$t->stop()}");
	}
	
	function doRecoverFromRecoveryLine()
	{
	//die("u:".$this->app->user->username);
			
		if(!$this->app->user->isRoot())
			return $this->setError("Roor only permission");
		
		$form = ['fields'=>[
		    'codejson'=>['type'=>'code_json','height'=>'200px','width_input'=>'600px', 'required'=>1],
		    'multiple'=>['type'=>'bool', 'required'=>1],
		],'cols'=>3];
		
	
		
		
		
		
		if(!($answers=$this->prompt($form, GW::l('/m/PASTE_MULTIPLE_OR_SINGLE_JSON_DUMP'),['method'=>'post'])))		
			return false;		
		


		
		$data = json_decode($answers['codejson'], true);
		
		$this->recoverData($data);
				
		
		$this->jump();		
	}
	
	function __getListFields(){
		$this->initListParams(false,'list');
		$this->prepareListConfig();
		
		$fields = [];
		$ignore = ['id'=>1,'insert_time'=>1,'update_time'=>1];
		
		foreach($this->list_config['dl_fields'] as $field)
		{
			if(isset($ignore[$field]))
				continue;
			
			$fields[$field] = $this->fieldTitle($field);
		}
		return $fields;		
	}
	
	function doCopyFieldData()
	{
		if(!$this->app->user->isRoot())
		{
			$this->setError("Need root access");
			$this->jump();
		}
		$fields = $this->__getListFields();
				
	
		
		$sel=['type'=>'select','options'=>$fields, 'empty_option'=>1, 'required'=>1];
		$form = ['fields'=>['from'=>$sel, 'to'=>$sel],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, GW::l('/g/SELECT_SOURCE_AND_DEST'))))
			return false;		
		
		
		$vars = $this->viewList();
		
		$changeinf = [];
		
		foreach($vars['list'] as $item){
			
			$changeinf[] = ['id'=>$item->id, 'before '.$answers['to']=>$item->get($answers['to']),'after '.$answers['to']=>$item->get($answers['from'])];
			
			if(isset($_GET['confirm'])){
				$item->set($answers['to'], $item->get($answers['from']));
				$item->updateChanged();
			}
		}
		
		if(!isset($_GET['confirm'])){
			$str = GW_Data_to_Html_Table_Helper::doTable($changeinf);
			
			
			$confirmurl = $this->buildUri(false, $_GET+['confirm'=>1]);
			$str.="<br /><a class='btn btn-primary' href='$confirmurl'>".GW::l('/g/CONFIRM')."</a>";
			$this->setMessageEx(['text'=>$str, 'type'=>4]);	
			
		}else{
			$this->setMessage("action performed on ".count($vars['list'])." items");
			$this->jump();
		}
	}


	function doMultiSetValue()
	{
		if(!$this->write_permission){
			$this->setError("Please ensure you have write permission");
			$this->jump(false);
		}
		
		$fields = $this->__getListFields();
		
		
		$cfg = $this->getListConfig();

		$field = $_GET['field'] ?? false;
		

		$form=['cols'=>1];
		$form['fields']=[];
		
		$fields = $this->__getListFields();
		
		if(!isset($fields[$field])){
			$form['fields']['field']=['type'=>'select','options'=>$fields, 'empty_option'=>1, 'required'=>1];	
		}
		
		$form['fields']['value'] = $cfg['inputs'][ $field ] ?? ['type'=>'text'];
		$form['fields']['value']['required']=1;
			
		
		
		if(!($answers=$this->prompt($form, GW::l('/g/COLUMN_SET_VALUE').' '.($field ? $this->fieldTitle($field) :'') )))
			return false;			
		
		
		if(!$field)
			$field = $answers['field'];
		
		if(!isset($fields[$field])){
			return $this->setError("{$field} not permited");
		}
				
		$vars = $this->viewList();
		
		$changeinf = [];
		
		$list = $vars['list'];		
		
		
		$changeinf = [];
		foreach($list as $item){
			$inf = [];
			$inf['id'] = $item->id;
			$inf['title'] = $item->title;
			$inf['before'] = $item->get($field);
			$inf['after'] = $answers['value'];
			$changeinf[] = $inf;
			
			
			$item->fireEvent('BEFORE_CHANGES');
			
			$item->set($field, $answers['value']);
			
			if(isset($_GET['confirm'])){
				$item->updateChanged();
			}			
		}
		
		if(!isset($_GET['confirm'])){
			$str = GW_Data_to_Html_Table_Helper::doTable($changeinf);
			
			$this->askConfirm($str);
		}else{	
			$this->setMessage("action performed on ".count($vars['list'])." items");
			
			

			unset($_GET['item']['value']);
			$repeaturl = $this->buildUri(false, array_merge($_GET,['confirm'=>null]));
			$addstr="<br/><a class='btn btn-primary' href='$repeaturl'>".GW::l('/g/REPEAT').' '.GW::l('/g/ACTION').': '. GW::l('/g/VIEWS/doMultiSetValue')."</a> <small>(Add filter to show non empty rows)</small>";
			$this->setMessageEx(['text'=>$addstr, 'type'=>4]);
			$this->jump(false, ['field'=>$field]);
		}
		
	}
	
	function doFillSeries()
	{
		if(!$this->write_permission){
			$this->setError("Please ensure you have write permission");
			$this->jump(false);
		}		
		
		$fields = $this->__getListFields();
		
		
		$cfg = $this->getListConfig();

		$field = $_GET['field'] ?? false;
		

		
		$form['fields']=[];
		
		$fields = $this->__getListFields();
		
		if(!isset($fields[$field])){
			$form['fields']['field']=['type'=>'select','options'=>$fields, 'empty_option'=>1, 'required'=>1];	
		}
		
		$form['fields']['offset'] = ['type'=>'number','required'=>1,'default'=>1];
		$form['fields']['increment'] = ['type'=>'number','required'=>1,'default'=>1];
		
			
		
		
		if(!($answers=$this->prompt($form, GW::l('/g/VIEWS/doFillSeries').' <b style="color:blue">'.($field ? $this->fieldTitle($field) :'').'</b>' )))
			return false;			
		
		
		if(!$field)
			$field = $answers['field'];
		
		if(!isset($fields[$field])){
			return $this->setError("{$field} not permited");
		}
				
		$vars = $this->viewList();
		
		$changeinf = [];
		
		$list = $vars['list'];		
		
		
		$changeinf = [];
		
		$offset = (int)$answers['offset'];
		$increment = (int)$answers['increment'];;
		
		foreach($list as $item){
			$inf = [];
			$inf['id'] = $item->id;
			$inf['title'] = $item->title;
			$inf['before'] = $item->get($field);
			$inf['after'] = $offset;
			$changeinf[] = $inf;
			$item->fireEvent('BEFORE_CHANGES');
			$item->set($field, $offset);
			$offset+=$increment;

			if(isset($_GET['confirm'])){
				$item->updateChanged();
			}			
		}
		
		if(!isset($_GET['confirm'])){
			$str = GW_Data_to_Html_Table_Helper::doTable($changeinf);
			
			$this->askConfirm($str);
		}else{	
			$this->setMessage("action performed on ".count($vars['list'])." items");
			
			

			unset($_GET['item']['value']);
			$repeaturl = $this->buildUri(false, array_merge($_GET,['confirm'=>null]));
			$addstr="<br/><a class='btn btn-primary' href='$repeaturl'>".GW::l('/g/REPEAT').' '.GW::l('/g/ACTION').': '. GW::l('/g/VIEWS/doMultiSetValue')."</a> <small>(Add filter to show non empty rows)</small>";
			$this->setMessageEx(['text'=>$addstr, 'type'=>4]);
			$this->jump(false, ['field'=>$field]);
		}
		
	}

	function doCopyColValues()
	{
		if(!$this->write_permission){
			$this->setError("Please ensure you have write permission");
			$this->jump(false);
		}
		
		$fields = $this->__getListFields();
		
		
		$cfg = $this->getListConfig();

		$field = $_GET['field'] ?? false;
		
		$vars = $this->viewList();
		$list = $vars['list'];	
		$changeinf = [];		
		
		$str = "";
		foreach($list as $item){
			$str .= $item->get($field)."\n";
		}
		
		$str = trim($str, "\n");

		
		$form['fields']=[];		
		$form['fields']['colvals'] = ['type'=>'textarea','required'=>1,'value'=>$str, 'height'=>'500px','width'=>"100%"];
		$opts = ['method'=>'post'];
		
		if(!($answers=$this->prompt($form, GW::l('/g/VIEWS/doCopyColValues').' <b style="color:blue">'.($field ? $this->fieldTitle($field) :'').'</b>' , $opts)))
			return false;			
		
		$newvals = explode("\n", $answers["colvals"]);
		
		if(count($list) != count($newvals))
			$this->setError("List count: ".count($list)." and values count:".count($newvals)." does not match");
		//$item->fireEvent('BEFORE_CHANGES');
		

		$i=0;
		foreach($list as $item){
		
			$inf = [];
			$inf['id'] = $item->id;
			$inf['title'] = $item->title;
			$inf['before'] = $item->get($field);
			$inf['after'] = $newvals[$i];
			$changeinf[] = $inf;
			$item->fireEvent('BEFORE_CHANGES');
			$item->set($field, $newvals[$i]);

			if(isset($_GET['confirm'])){
				$item->updateChanged();
			}
			$i++;
		}
	
		
		if(!isset($_GET['confirm'])){
			$str = GW_Data_to_Html_Table_Helper::doTable($changeinf);
			
			$this->askConfirm($str);
		}else{	
			$this->setMessage("action performed on ".count($vars['list'])." items");
			
			

			unset($_GET['item']['value']);
			$repeaturl = $this->buildUri(false, array_merge($_GET,['confirm'=>null]));
			$addstr="<br/><a class='btn btn-primary' href='$repeaturl'>".GW::l('/g/REPEAT').' '.GW::l('/g/ACTION').': '. GW::l('/g/VIEWS/doMultiSetValue')."</a> <small>(Add filter to show non empty rows)</small>";
			$this->setMessageEx(['text'=>$addstr, 'type'=>4]);
			$this->jump(false, ['field'=>$field]);
		}
				
	}
	
	
	function doDragMoveSorting()
	{
		if(!$this->write_permission){
			$this->setError("Please ensure you have write permission");
			$this->jump(false);
		}		
		
		$fields = $this->__getListFields();
		$field = $_GET['field'] ?? false;
				
		if(!isset($fields[$field])){
			return $this->setError("{$field} not permited");
		}		
		
		if(!$this->list_params['order']!="{$field} ASC"){
			$this->list_params['order'] = "{$field} ASC";
			//Navigator::jump($_SERVER['REQUEST_URI']);
		}
		
		
		$ftitle = ($field ? $this->fieldTitle($field) :'');
		
		$this->tpl_vars['dl_dragdropmove'] = 1;
		$this->tpl_vars['dl_dragdropmove_field'] = $field;
			
		
		if(isset($_GET['confirm'])){
			$this->jump();
		}
		
		$this->askConfirm("Drag & drop sorting is active press confirm to exit");
	}
	
	function doAutoTranslate()
	{
		
		if(!$this->write_permission){
			$this->setError("Please ensure you have write permission");
			$this->jump(false);
		}
		
		if(!$this->app->user->isRoot())
		{
			$this->setError("Need root access");
			$this->jump();
		}
						
	
		$fields = array_flip($this->model->i18n_fields);
		
		$selfield=['type'=>'select','options'=>$fields, 'options_fix'=>1, 'empty_option'=>1, 'required'=>1];
		$sellangs=['type'=>'select','options'=>$this->app->langs, 'empty_option'=>1, 'options_fix'=>1, 'required'=>1];
		
		

		$form = ['fields'=>['field'=>$selfield,'fromln'=>$sellangs, 'toln'=>$sellangs],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, GW::l('/g/SELECT_SOURCE_AND_DEST_AND_FIELD'))))
			return false;		
		
				
		$vars = $this->viewList();
		
		$changeinf = [];
		
		$list = $vars['list'];
		
		foreach($list as $item){
			$changeinf[$item->id] = [
			    'id'=>$item->id, 
			    'source'=>$item->get($answers['field'], $answers['fromln']),
			    'before'=>$item->get($answers['field'], $answers['toln'])
			    ];
		}
		
		
		$opts = ['log'=>1,'commit'=>0];
		
		if(isset($_GET['confirm'])){
			$opts['commit']=1;
		}
		
		$t = new GW_Timer;
		
		foreach(array_chunk($vars['list'], 30) as $chunk)
		{
			GW_Auto_Translate_Helper::seriesTranslate($chunk, $answers['field'], $answers['fromln'], $answers['toln'], $opts);
		}
						
		
		foreach($vars['list'] as $item){
			
			$changeinf[$item->id]['after'] = $item->get($answers['field'], $answers['toln']);//json_encode($item->toArray());//
		}
		
		$took = $t->stop();
		$this->setMessage("Translation took: $took secs");
		
		
		if(!isset($_GET['confirm'])){
			$str = GW_Data_to_Html_Table_Helper::doTable($changeinf);
			
			$this->askConfirm($str);
		}else{
			$this->setMessage("action performed on ".count($vars['list'])." items");
			$repeaturl = $this->buildUri(false, array_merge($_GET,['confirm'=>null]));
			$addstr="<br/><a class='btn btn-primary' href='$repeaturl'>".GW::l('/g/REPEAT').' '.GW::l('/g/ACTION').': '. GW::l('/g/VIEWS/doAutoTranslate')."</a> <small>(Add filter to show non empty rows)</small>";
			$this->setMessageEx(['text'=>$addstr, 'type'=>4]);				
			
			$this->jump();
		}
	}
	
	
	function askConfirm($str)
	{
		$confirmurl = $this->buildUri($this->view_name, $_GET+['confirm'=>1]);
		$refreshurl = $_SERVER['REQUEST_URI'];
		
		
		if(isset($_POST) && $_POST){
			$str.="<br>".Navigator::postLink($confirmurl, GW::l('/g/CONFIRM'), $_POST,['aclass'=>'btn btn-primary']);
		}else{
			$str.="<br /><a class='btn btn-primary' href='$confirmurl'>".GW::l('/g/CONFIRM')."</a> <a style='float:right;margin-right:10px;' href='$refreshurl'><i class='fa fa-undo'></i></a>";
		}

		
		$this->setMessageEx(['text'=>$str, 'type'=>4]);			
	}
	
	function confirm($str)
	{
		if(isset($_GET['confirm'])){
			return true;
		}
		$this->askConfirm($str);
		$this->jump();
	}
	
	function initFeatures($modcfg=false)
	{
		$cfg = $modcfg ? $this->modconfig : $this->config;
					
		$this->features = array_fill_keys((array)json_decode($cfg->features), 1);		
	}
	
	function feat($id, $opts=[])
	{
		//pakrauti is kito modulio pvz is formu pakrauti docs configa per $m->feat(a)
		if(isset($opts['mod'])){
			$cfg = new GW_Config($opts['mod'].'/');
			$features = array_fill_keys((array)json_decode($cfg->features), 1);
			return isset($features[$id]);
		}
		
		return isset($this->features[$id]);
	}	
	
	
	function doSavePositions()
	{
		
		if(!$this->write_permission){
			$this->setError("Please ensure you have write permission");
			$this->jump(false);
		}
		
		$positions = json_decode($_POST['positions'], true);
		
		$workfield = $_POST['rearragefield'] ?: 'priority';
		$offset = $_POST['offset'] ?: 0;
		
		foreach($positions as $itm)
			if($itm['id'] ?? false)
				$ids[] = $itm['id'];
		
		$positions = array_flip($ids);

			
		
		$items = $this->model->findAll(GW_DB::inCondition('id', $ids), ['key_field'=>'id']);
		
		$updated = 0;
				
		foreach($items as $item){
			$item->$workfield = $positions[$item->id]+$offset;
			if($item->changed_fields){
				$updated++;
				$item->updateChanged();
			}
		}
		
		
		
		echo "Updated: $updated";
		exit;
	}	
	
	/*
	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action_postids('{$m->buildUri(false,[act=>doChangeParent])}', true)">Perkelti</option>{/capture}
	 */
	
	function acceptIds($store, $clean=false)
	{
		if(isset($_POST['ids']))
		{
			$this->app->sess($store, explode(',', $_POST['ids']));
			exit;
		}else{
			if($clean)
				$this->app->sess($store, false);
			
			
			return $this->app->sess($store);
		}
		
	}
	
	
	function doImportJSON()
	{
		//die("u:".$this->app->user->username);
			
		if(!$this->app->user->isRoot())
			return $this->setError("No translation permission");
		
		$form = ['fields'=>[
		    'codejson'=>['type'=>'code_json','height'=>'200px','width_input'=>'600px', 'required'=>1],
		    'multiple'=>['type'=>'bool', 'required'=>1],
		],'cols'=>3];
		
	
		
		
		
		
		if(!($answers=$this->prompt($form, GW::l('/m/PASTE_MULTIPLE_OR_SINGLE_JSON_DUMP'),['method'=>'post'])))		
			return false;		
		


		
		$entries = json_decode($answers['codejson'], true);
		
		if(!$answers['multiple'])
			$entries = [$entries];
		
		
		//d::dumpas($entries);
		
		if(!$this->confirm(GW_Data_to_Html_Table_Helper::doTable($entries)))
			return false;
		
		
				
		$t = new GW_Timer;
		$item0 = $this->model;
		$item0->multiInsert($entries);
		
		$this->setMessage($msg = "Import done, cnt: ".count($entries).", speed: {$t->stop()}");
		
		
		$this->jump();
	}	
	

	function readOnlyAccess($item, $opts=[])
	{
				
		$requestAccess = $opts['access'] ?? GW_PERM_WRITE;
		
		
		
		$result =  $requestAccess == GW_PERM_READ;
		
		
		if (isset($opts['nodie']) || $result)
			return $result;

		$this->setError('/G/GENERAL/ACTION_RESTRICTED');
		
		
		$this->jump('');			
	}	
	
	function offerFollowingAction($action, $args=[])
	{
		$this->setMessage('<a class="btn btn-primary" href="'.$this->buildUri('', $args+['act'=>$action]).'">'.GW::l('/m/VIEWS/'.$action).'</a>');		
	}
	
	
	function doExportListAsSheet()
	{
		
		$this->processView('list',['return_as_string'=>1]);
		
		$table = GW::$globals['capturelist'];
		
		$rows = [];
		$fields = [];
		$cells=[];
		foreach($table['head'] as $field => $th){
			$cells[] = $th;
			$fields[] = $field;
		}
		$rows[]=$cells;
		

		
		unset($table['head']);
		
		foreach($table as $idx => $x){
			$cells =[];
			
			foreach($fields as $field)
				$cells[] = strip_tags(html_entity_decode($table[$idx][$field]));
			
			$rows[]=$cells;
		}
		
		
		$bc = $this->app->getBreadcrumbs($this->tpl_vars['breadcrumbs_attach'] ?? false);
		
		$file_name=[GW::s('PROJECT_NAME')];
		foreach($bc as $b)
			$file_name[]=$b['title'];
		
		//d::dumpas($rows);
		Others\Shuchkin\SimpleXLSXGen::fromArray($rows)->downloadAs(FH::urlStr(implode("_",$file_name)).'.xlsx');
		$this->view_name = false;
	}
	
	
	function doUndo()
	{
		if(!$this->write_permission){
			$this->setError("Please ensure you have write permission");
			$this->jump(false);
		}
		
		if(!$this->app->user->isRoot())
		{
			$this->setMessage("Feature is experimental yet");
			return $this->app->jump();
		}

		$item = $this->getDataObjectById();
		
		$changes = $item->extensions['changetrack']->undo();
		$item->updateChanged();
		
		$this->setMessage("Undone: <pre>".json_encode($changes));
		$this->jump();
	}
	
	
	function __prepareRelation(&$cfg)
	{		
		if(!isset($cfg['url']) || !isset($cfg['modpath']) || !isset($cfg['title'])){
			$relationgetarg = $cfg['relarg'] ?? 'user_id';
			$cfg['url'] = $cfg['url'] ?? $this->app->buildUri($cfg['modpath'],['clean'=>2,$relationgetarg=>'%ID%']);

			if(!isset($cfg['title']) && isset($cfg['modpath'])){
				$cfg['title'] = $this->getTitleFromPath($cfg['modpath']);
			}
			
			
		}
	}
	
	function prepareRelations($list)
	{
		foreach($list as $idx => $cfg){
			$this->__prepareRelation($list[$idx]);
		}
	}
	
	
	function getModelFromPath($modpath)
	{
		$modpath = explode('/',$modpath);

		if(count($modpath)>1){
			$obj = GW::l('/M/'.$modpath[0].'/MAP/childs/'.$modpath[1].'/info/model');
		}else{
			$obj = GW::l('/M/'.$modpath[0].'/MAP/info/model');
		}
		
		return $obj;
	}
	
	function getTitleFromPath($modpath)
	{
		$modpath = explode('/',$modpath);

		
		if(count($modpath)>1){
			$title = GW::l('/M/'.$modpath[0].'/MAP/childs/'.$modpath[1].'/title');
		}else{
			$title = GW::l('/M/'.$modpath[0].'/MAP/title');
		}
		
		return $title;
	}	
	
	function addRelCount(&$ids, $cfg)
	{
		if(!isset($cfg['nocount'])){
			

			$obj = $this->getModelFromPath($cfg['modpath']);

			$cond = GW_DB::prepare_query(GW_DB::inCondition($cfg['field'], $ids));
			
			if($cfg['andcond'] ?? false){
				$cond .= " AND ".$cfg['andcond']; 
			}

			$counts = $obj::singleton()->countGrouped($cfg['field'], $cond);

			//pagal telefona rasti sms i ids paduodame asociatyvu masyva [id1 => tel1, id2=> tel2]
			if($cfg['map'] ?? false){
				$counts0 = $counts;
				$counts = [];
				
				$ids_flipped = array_flip(array_filter($ids));
				
				foreach($counts0 as $countid => $count)
					if(isset($ids_flipped[$countid]))
						$counts[  $ids_flipped[$countid] ]  = $count;
			}

			$this->tpl_vars['counts'][ $cfg['modpath'] ] = $counts;	
			
			//if($cfg['modpath']=='membership'){
			//	d::ldump($counts);
			//}
			
			//d::ldump([$cfg['modpath'], $cond ,$counts, $obj]);
		}
		
		//$cfg = ['modpath'=>$cfg['modpath'], 'bg'=>$cfg['bg']];
		$this->__prepareRelation($cfg);
		$this->tpl_vars['relations'][ $cfg['modpath'] ] = $cfg;
	}
	
	
	function initDynFieldsList($list)
	{		
		$sources=[];
		$dynfieldsopts = [];
		
		foreach($this->mod_fields as $field){
			if($field->inp_type=="select_ajax"){
				$sources[$field->modpath][] = $field->fieldname;
			}	
			
			$this->dynamicFieldTitles[$field->fieldname] = $field->title;

			if($field->short_title)
				$this->dynamicFieldShortTitles[$field->fieldname] = $field->short_title;
		}
		
		foreach($sources as $modpath => $fields){
			
			$model = $this->mod_fields[ $fields[0] ]->modelFromModpath();
			
			foreach($fields as $field)
				$dynfieldsopts[$field] = $model;
		}
				
		
		
		$ids = array_keys($list);
		
		$dynopts = [];
		
		foreach($list as $item){
			foreach($dynfieldsopts as $field => $class)
				if($item->$field)
					$dynopts[$class][$item->$field]=1;
		}
		
		foreach($dynopts as $class => $ids){
			$ids = array_keys($ids);
			$this->options[$class] = $class::singleton()->findAll(GW_DB::inCondition ('id', $ids),['key_field'=>'id']);
		}
				
		$this->tpl_vars['dynfieldopts']=$dynfieldsopts;		
	}
	
	
	function addDynamicField(&$fields_config, $input, $defaults=[])
	{
		$field = $defaults + [
			'field'=>$input->get('fieldname'),
			'type'=>$input->get('inp_type'),
			'note'=>$input->get('note'),
			'title'=>$input->get('title'),
			'placeholder'=>$input->placeholder,
			'hidden_note'=>$input->hidden_note,
			'i18n'=>$input->get('i18n'),
			//'colspan'=>$input->size,
		];
		
		

		if($input->type == 'extended')
			$field['field']="keyval/{$input->get('fieldname')}";


		$opts = $input->get('config');

		if($input->get('inp_type') == 'select_ajax' || $input->get('inp_type') == 'multiselect_ajax'){
			$opts['preload'] = 1;
			$opts['modpath'] = $input->get('modpath');
			$opts['after_input_f'] = 'editadd';
		}

		if(is_array($opts))
			$field = array_merge($field, $opts);

		$fields_config['fields'][$input->get('fieldname')] = $field;		
	}
	
	function addDynamicFieldsConfig(&$fields_config, $item, $defaults=[])
	{
		
		foreach ($this->mod_fields as $input){
			
			$this->addDynamicField($fields_config, $input, $defaults);
		}		
	}
		
	function initDynFieldInputs(&$cfg)
	{
		$fields_config=[];
		foreach ($this->mod_fields as $input){
			$this->addDynamicField($fields_config, $input);
		}	
		
		if(!isset($fields_config['fields']))
			return false;
			
		foreach($fields_config['fields'] as $field => $input){
			unset($input['field']); // grupinis vertes keitimas
			$cfg['inputs'][$field] = $input;
		}
	}
	
	function initMultisite()
	{
		if(!$this->multisite)
			return false;
		
		if(isset($_GET['site_id']))
		{
			if($_GET['site_id']!=-1)
				$this->filters['site_id'] = $_GET['site_id'];
			
			
		}elseif(GW::s('MULTISITE') && !($this->list_params['search'] ?? false)){
			$this->filters['site_id'] = $this->app->site->id;
		}	

		$this->app->carry_params['site_id']=1;
		
		if(isset($this->filters['site_id'])){
			$this->site = GW_Site::singleton()->createNewObject($this->filters['site_id'], true);
			$this->tpl_vars['breadcrumbs_attach'] = $this->tpl_vars['breadcrumbs_attach'] ?? [];
			array_unshift($this->tpl_vars['breadcrumbs_attach'], [
			    'title'=>$this->site->title, 
			    'path'=>$this->buildUri('', ['site_id'=>$this->site->id,'pid'=>0])
			    ]);
		}	
		
		$this->options['site_id'] = GW_Site::singleton()->getOptions($this->app->ln);	
		$this->options['site_map_id_key'] = GW_Site::singleton()->getOptionsKey();		
		
	}
	
	function setItemImageFromUrl($item, $url) 
	{
		//this allso should be set in background jobs once per day
		GW_File_Helper::unlinkOldTempFiles(GW::s('DIR/TEMP'),'24 hour');
		
		$file = tempnam(GW::s('DIR/TEMP'), 'TMP_');

		file_put_contents($file, file_get_contents($url));
		$this->setMessage("temp file was used: $file");

		$image = Array
		    (
		    'new_file' => $file,
		    'size' => filesize($file),
		    'original_filename' => basename($url),
		);

		$item->set('image', $image);
		$item->validate();
		
		return $item;
	}
	
}
