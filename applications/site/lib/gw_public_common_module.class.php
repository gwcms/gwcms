<?php

class GW_Public_Common_Module extends GW_Public_Module
{
	
	function init()
	{
		
		parent::init();

		$this->list_params['paging_enabled'] = false;

		//d::dumpas($this->app->page);
		//specifu model name in lang file

		$this->tpl_vars['options'] = & $this->options;
		$this->tpl_vars['links'] = & $this->links;

		if (isset($_GET['sys_call'])) {
			$this->sys_call = 1;

			if ($_GET['sys_call'] == 2)
				header('Content-type: text/plain');
		}
		
		
		if(isset($this->app->path_arr[count($this->app->path_arr)-2]['data_object_id'])){
			$this->acive_object_id = $this->app->path_arr[count($this->app->path_arr)-2]['data_object_id'];
			$_REQUEST['id'] = $this->acive_object_id;
		}elseif(isset($_REQUEST['id'])){
			$this->acive_object_id = $_REQUEST['id'];
		}
		
		

		
		$this->app->carry_params['searchreplace']=1;
		$this->app->carry_params['filterhide']=1;
		
		$this->initModCfg();
		$this->app->carry_params['clean']=1;
		$this->initErrorHandler();
		
		
		if($this->allowed_ids = GW_Permissions::getTempReadAccess(implode('/',$this->module_path)) ){
			$this->filters['id'] = $this->allowed_ids;
		}		
		
		$this->userRequired();
		
		$this->filters = ['user_id'=> $this->app->user->id];

	}
	
	
	
	public $permit_fields = [
	    'title'=>1
	];
	
	
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
	
	
	
	public $auto_images = true;

	
	
	function dataLossPrevent($item)
	{
		if(isset($_POST['last_update_time']) && $_POST['last_update_time']!=$item->update_time)
		{
			$item->errors['update_time']='/g/ITEM_SAVE_PREVENT_DATA_LOSS';
		}		
	}	
	
	
	function filterPermitFields(&$vals, $permit_fields)
	{
		$vals = array_intersect_key($vals, $permit_fields);		
	}
	
	
	function doSave()
	{
		$vals = $_REQUEST['item'];

		$this->filterPermitFields($vals,$this->permit_fields+['id'=>1]);
		
		$vals+=$this->filters;
		
		//duplicate save protection
		if(isset($vals['temp_id']) && $vals['temp_id']){
			$tmpid = $vals['temp_id'];
			if($this->app->sess('last_temp_id') == $vals['temp_id']){
				$this->setMessage(['text'=>GW::ln('/G/validation/DUPLICATE_SAVE_PROTECION_WORKED'), 'type'=>GW_MSG_INFO, 'float'=>1]);
				GOTO saveFinish;
			}
		}
		
		if(isset($_POST['fields'])){
			$this->searchEmptyVals($vals, $_POST['fields']);
		}

		if (isset($vals['id']) && $vals['id'] === '')
			unset($vals['id']);
		


		$item = $this->model->createNewObject(isset($vals['id']) ? ['id'=>$vals['id']]: [], false);


		$item->load();

		$this->dataLossPrevent($item);
		
		
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
				
		$message["float"] = 1;
		$message["id"]=$item->id;
		
		$this->setMessageEx($message);

		$this->fireEvent('AFTER_SAVE', $item);
		
		if(isset($tmpid))
			$this->app->sess('last_temp_id', $tmpid);
		
		saveFinish:

		//jeigu saugome tai reiskia kad validacija praejo
		if(isset($_GET['reloadparent']) && $_REQUEST['submit_type']??false != 1)
		{
			echo "<script type='text/javascript'>parent.location.reload()</script>";
			exit;
		}
		if(isset($_GET['dialog']) && ($_REQUEST['submit_type']??false) != 1) {
			
			//reik tiketis kad dvigubos apsaugos atveju neuzeis cia
			$contextdata = json_encode(['item'=>['id'=>$item->id,'title'=>$item->title]]);
			
			$messages=$this->app->acceptMessages(1);
			foreach($messages as $msg)
				echo "<script type='text/javascript'>window.parent.gw_adm_sys.notification(".json_encode($msg).")</script>";
						
			echo "<script type='text/javascript'>window.parent.rootgwcms().close_dialog2($contextdata)</script>";
			exit;
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
	
	function setMessageEx($opts=[])
	{
		
		if(isset($opts['params']))
		{
			$str = "";
			foreach($opts['params'] as $key => $val)
			{
				$str.=GW::ln($key).': <i>'.htmlspecialchars($val).'</i><br/>';
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
					$opts['text'] = GW::ln($opts['text']);
				
				$this->app->addPacket(['action'=>'notification'] + $opts);
			}else{
				$this->app->setMessage($opts);
			}
		}
				
		$this->loadErrorFields();		
	}
		
	
	function isPacketRequest()
	{
		return isset($_REQUEST['packets']) && $_REQUEST['packets']==1;
	}	
	
	function jumpAfterSave(){
		
		if ($_REQUEST['submit_type']??false == 1) {//apply
			
			if(isset($_POST['activetabs']))
				$_GET['activetabs']=$_POST['activetabs'];
		
			$options = $item ? ['id' => $item->get('id')] : [];
			$this->jump(false, $options + $_GET);
		} else { //save
			Navigator::jump($this->buildDirectUri(''));
		}		
	}
	
	function viewList($params = [])
	{
		//$this->loadViews();
		$this->setDefaultOrder(); //for template
		
		if(isset($_GET['form_ajax']) && !isset($params['ajax_one_item_list']))
		{
			$this->processView('form');
			exit;
		}

		
		$this->setListParams($params);

		$cond = isset($params['conditions']) ? $params['conditions'] : false;

		//d::Dumpas($cond);


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
		
		if(isset($this->app->sess['debug']) && $this->app->sess['debug'] && $this->app->user->isRoot())
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

		
		$this->fireEvent('AFTER_LIST', $list);	
		
		$this->tpl_vars['list'] = $list;
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

	function setListParams(&$params = [])
	{
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
		
		
		if(isset($this->list_params['search']) && $this->list_params['search'])
		{
			
			$is_utf8 = mb_detect_encoding($this->list_params['search']) != 'ASCII';
			
			$cols = $this->getModelCols($is_utf8 ? 'text':'all');		
			$subcond = '';
			
			foreach ($cols as $key => $x){
				$this->buildConds(['field' => $key, 'value' => $this->list_params['search'], 'ct' => 'LIKE'], $subcond, 'OR');
			}
			
			$cond.=($cond ? ' AND ':'')."($subcond)";
		}
	
			
		foreach ($search as $filter) {
			
			$this->buildConds($filter, $cond);
		}
		
		
		
			
		if ($this->paging_enabled && $this->list_params['paging_enabled'] && $this->list_params['page_by']) {
			$page = isset($this->list_params['page']) && $this->list_params['page'] ? $this->list_params['page'] - 1 : 0;
			$params['offset'] = $this->list_params['page_by'] * $page;
			$params['limit'] = $this->list_params['page_by'];
		}
		
		if ($pview && $pview->group_by){
			$params['group_by'] = $pview->group_by;
		}		
		
		if ($pview && $pview->select){
			$params['select'] = $params['select'] ?? '';
			$params['select'] = ($params['select'] ? $params['select'].', ':'').$pview->select;
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
				
		$this->fireEvent('AFTER_LIST_PARAMS', $params);		
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
		
		
		$this->list_config['dl_fields'] = $this->getDisplayFields($display_fields);
		$this->list_config['display_fields'] = $display_fields;
		
		
		$this->list_config['dl_order_enabled_fields'] = $order_enabled;		
		$this->list_config['dl_filters'] = $filters;
		

		
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
	function getDisplayFields(&$fields, $onlyenabled=1)
	{	
		$saved = [];
		
		//d::dumpas($this->tpl_vars['views']);
		//d::dumpas("test");

		
		if(isset($this->list_params['fields']))
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
	function getPageView4use()
	{
		if(isset($_GET['pview']) && $_GET['pview']=='default'){
			return $this->getDefaultPageView();
		}
		
		return $this->list_config['pview'] ?? false;
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
	}


	
	
	function buildConds($filter, &$cond, $joiner="AND")
	{
			$compare_type = $filter['ct'];
			$value = $filter['value'];
			$field = $filter['field'];
			
			if($value==="" || $value===null)
				return;	
			
			if($value=="#empty#")
				$value="";
			
			if($value=="#zero#")
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
	function buildCond($field, $compare_type, $value, $encap_val=true, $encap_fld=true)
	{
		$encapChr = $encap_val ? "'" : '';
		
		$cond =  $field. ' ';
		if($encap_fld && isset($this->field_alias[$field])){
			$field = $this->field_alias[$field]; //get orig field
			$encap_fld = false;
		}
			
		$cond = ($encap_fld ? GW_DB::escapeField($field, 'a') : $field). ' ';
				
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
				break;
			case 'EQ':
			default:
				$cond .= "= $encapChr" . $value . "$encapChr";
				break;
		}

		return $cond ? "($cond)" : "";
	}	
	
	

	
	function procError($errStr, $errMail)
	{
		if($this->app->user->isRoot()){
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
				$errstr = "$type on line $errline in file $errfile:<br /> [$errno] $errstr";
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
	
	
	function process($params)
	{
		
		//d::dumpas($this->_args);
		extract($this->_args);
				
		
		if(isset($request_params['act']) && ($act=$request_params['act']))
		{
			$this->processAction($act);
			
			if(isset($request_params['just_action'])) //prevent from displaying view
				return true;
		}
		
		
		$this->processView($this->view_name, array_splice($params,1));
	}
		
	/**
	 * common view - viewForm. override this if diferent functionality needed
	 */
	function viewForm()
	{
		$item = $this->model->createNewObject();
		
		$id = $this->getCurrentItemId();

		//only form i18n objects
		if (isset($this->i18n_fields) && $this->i18n_fields)
			$item->_lang = $this->app->ln;

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
			
			
			$item = $this->model->createNewObject($id, false, $this->app->ln);
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
		
		$this->prepareListConfig();
		
		if(! $this->canBeAccessed($item, ['access'=>GW_PERM_WRITE, 'nodie'=>1])){
			$this->tpl_vars['readonly'] = true;
		}
						
		$this->tpl_vars['update'] = $item->get('id');
		$this->tpl_vars['item'] = $item;
	}

		
}



