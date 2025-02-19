<?php

class Module_Pages extends GW_Common_Module_Tree_Data 
{

	public $multisite = true;
		
	function init()
	{	
		$this->app->carry_params['site_id']=1;
		
		
		parent::init();

		$this->config = new GW_Config($this->module_path[0].'/');
		$this->config->preload('');	
		$this->tpl_vars['additfields'] = json_decode($this->config->additfields, true);
					

		

		
		
		$this->app->carry_params['clean']=1;
		

	}
	
	
	
	function viewDefault() {
		$this->viewList();
	}

	function __getParentOpt($parent = false) {
		$arr = Array();

		if (!$parent) {
			$parent = $this->model->createNewObject(-1);
			$parent->set('title', $this->lang['ROOT']);
			$parent->level = -1;
		}

		$arr[-1] = $parent->title;

		/*
		  if($parent)
		  $arr[$parent->get('id')] = str_repeat('&nbsp;&nbsp;',$parent->level+1). $parent->get('title');

		  foreach($parent->getChilds() as $item)
		  $arr+=$this->__getParentOpt($item);
		 */
		//multisite
		$conds = null;

		if(isset($this->filters['site_id']))
			$conds = ['site_id=?', $this->filters['site_id']];

		
		//d::dumpas($conds);
		
		
		$list = $parent->findAll($conds, Array('order' => 'path'));

		foreach ($list as $item)
		//$arr[$item->id] = $item->path.' ('. $item->title.')';		
			$arr[$item->id] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", substr_count($item->path, '/')) . ($item->title ? $item->title : '* ' . $item->path);


		return $arr;
	}

	function &getParentOpt($current_id) {
		$list = $this->__getParentOpt();
		unset($list[$current_id]);

		return $list;
	}

	function getTemplateList() {
		return GW_Template::getAssocStatic(Array('id', 'title'), 'active');
	}

	function preparePage(GW_Page $item) 
	{
		$item->prepare();
	}

	function getMoveCondition($item) {
		$tmp = $this->filters;

		return GW_SQL_Helper::condition_str($tmp);
	}
	
	function saveContent($item,$list)
	{
		
		
		
		$default = ['page_id'=> (int)$item->get('id'), 'user_id'=>$this->app->user->id];
		$db = GW::db();
		
		$vals=Array();

		$inputs = $item->getInputs(['index'=>'name']);
		
		$langs = $this->app->langs;
		$list_found = [];
		
		
		foreach($inputs as $key => $opts)
		{
			if(!isset($inputs[$key]))
				continue;
			
			if($inputs[$key]->multilang){
				foreach($langs as $ln){
					if(isset($list[$key.'_'.$ln])){
						$list_found[] = ['ln'=>$ln,  'key'=>$key, 'content'=>$list[$key.'_'.$ln] ];
						unset($list[$key.'_'.$ln]);
					}
						
				}
			}else{
				if(isset($list[$key])){
					$list_found[] = ['ln'=>'',  'key'=>$key, 'content'=>$list[$key] ];
					unset($list[$key]);
				}			
			}
		}
		
		foreach($list as $key){
			$item->errors[] = GW::s("/G/validation/UNKNOWN_FIELD", ['v'=>['field'=>$key]]);
		}
			
		foreach($list_found as $key => $value){
			if(is_array($value['content']))
				$value['content'] = json_encode($value['content']);
				
			$vals[]= $value+$default;
		}	
		
		$prev0 =  $db->fetch_rows("SELECT * FROM gw_sitemap_data WHERE page_id=".(int)$item->get('id'));
		$prev = [];
		foreach($prev0 as $row){
			$prev[$row['ln'].'/'.$row['key']] = $row;
		}
		
		$changes = [];
		$old=[];
		foreach($vals as $row){
			$key0 = $row['key'];
			$key = $row['ln'].'/'.$key0;
			if( !isset($prev[$key]) || $prev[$key]['content'] != $row['content'] ) {
				$changes[] = $row;
				
				$type = $inputs[$key0]->type;
				
				
				if(isset($prev[$key])){
					$prevr = $prev[$key];
					if(in_array($type, ['code_smarty','htmlarea', 'textarea'])){

						$prevr['diff'] = GW_String_Helper::createDiff($row['content'], $prev[$key]['content']);
						$prevr['content'] = 'diffc';
					}


					$prevr['time'] = $prevr['update_time'];

					unset($prevr['update_time']);
					unset($prevr['id']);

					$old[] = $prevr;
				}
			}
		}
		
		
		if($changes){
			if($old){
				$db->fieldfunc['diff'] = 'compress';
				$db->multi_insert('gw_sitemap_data_versions', $old, true);
			}
			
			$db->multi_insert('gw_sitemap_data', $changes, true);
		}
	}	

	function eventHandler($event, &$context) {
		switch ($event) {
			case 'BEFORE_SAVE_0':
			case 'AFTER_FORM':
				$context->addImageSettings();
				break;

			case 'BEFORE_SAVE':
				$item = $context;

				if ($item->id && isset($item->changed_fields['parent_id']) || isset($item->changed_fields['pathname']))
					$this->afterParentIdChanges($item);
				

				if (GW::$settings['LANGS'][0] == $this->lang())
					$this->preparePage($context);
				
				
				if($item->id){
					if(isset($item->content_base['input_data']))
					{
						$this->saveContent($item, $item->content_base['input_data']);
						unset($item->content_base['input_data']);
						unset($item->changed_fields['input_data']);
					}
				}
			break;
	
			
			case 'AFTER_SEARCH_COND_BUILD':
				
				$ids = $this->model->searchContent($this->list_params['search']);
				$subcond =& $context;
				
				if($ids){	
					$this->buildConds(['field' => 'id', 'value' => $ids, 'ct' => 'IN'], $subcond, 'OR');
				}
				
			break;
		}

		parent::eventHandler($event, $context);
	}

	//fix subitems paths
	function afterParentIdChanges($item) 
	{
		/*
		$list = $item->findAll(['parent_id=?', $item->id]);

		foreach ($list as $item) {
			$item->fixPath();
			$item->updateChanged();
			$this->afterParentIdChanges($item);
		}
		*/
		
		$this->doFixPaths();
	}
	
	function doPreview()
	{
		$item = $this->getDataObjectById();

		$args=[];
		
		if(isset($_GET['shift_key']))
			$args['clean']=1;
		$host="";
		
		if($item->site_id){
			
			
			$site = GW_Site::singleton()->createNewObject($item->site_id, true);
			$host = explode(',',$site->hosts)[0];
			
			if($host=='*'){
				$host=GW::s('SITE_URL');
			}else{
				$host = "http://".$host;
			}			
		}
		
		
		
		header("Location: $host/".$this->app->ln.'/'.$item->path.($args ? '?'. http_build_query($args): ""));
	}
	
	
	function doFixUniqPathId()
	{
		foreach($this->model->findAll() as $page)
		{
			$page->fixUniqPathId(true);
			$page->updateChanged();
		}
	}
	
	function doFixPaths()
	{
		$t = new GW_Timer;
		$pages= $this->model->findAll('1=1');
		
		foreach($pages as $page)
		{
			$page->fixPath();
			$page->updateChanged();
		}
		
		
		//GW_Page::singleton();
		
		$this->setMessage('Fix path complete in: '.$t->stop().' secs');
			
	}

	/////////////////---------------------IMPORT-EXPORT-----------------------------------------
	function getAllChilds($item, &$data, $opts)
	{
		$subdata = [];
		
		if($item->id && !$item->skip_export){
			$vals = $item->toArray();
			unset($vals['site_id']);
			unset($vals['parent_id']);
			unset($vals['path']);
			unset($vals['id']);
			unset($vals['unique_pathid']);
			unset($vals['insert_time']);
			unset($vals['update_time']);
			unset($vals['visit_count']);
			
			if($opts['content'])
				$vals['content'] = $item->exportContent(['lns'=>$opts['export_lns_vals']]);
			
			
			foreach($item->i18n_fields as $field => $x)
				foreach($this->app->langs as $ln)
					if(!isset($opts['alllns']) && !isset($opts['export_lns'][$ln])){
						unset($vals["{$field}_{$ln}"]);
					}
						
					

			$subdata['item'] = $vals;
		}
		$data['childs'][] =& $subdata;	
			
		
		if($opts['export_type']=='page_only')
			return;	
		
		foreach($item->getChilds($opts) as $child)
		{
			$this->getAllChilds($child, $subdata, $opts);
		}
	}
	
	function doExportTree($item=false, $opts=[])
	{
		if(!$item)
			$item = $this->getDataObjectById();
		
		//if root is selected
		$data = [];
		
		if(isset($_GET["opts"])){
			$opts = array_merge($opts, $_GET['opts']);
		}
		
		if($opts['export_type']=='only_childs')
			$item->skip_export = true;
		
		if(isset($_GET['site_id']))
			$opts['site_id'] = $_GET['site_id'];					
		
		$this->getAllChilds($item, $data, $opts);
		
		if(isset($_GET['shift_key']) || $_POST['item']['show_json'] ?? false)
		{
			header('Content-type: text/plain');
		}else{
			$filename = ($item->pathname ?: 'root').'.json';
				
			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: Binary"); 
			header("Content-disposition: attachment; filename=\"".$filename."\""); 
		}		
		
		echo json_encode($data, JSON_PRETTY_PRINT);
		exit;
	}
	
	
	private $importcnt = 0;
	private $import_paths = [];
	
	
	function getSiteId()
	{
		return $_GET['site_id'] ?? $this->app->site->id;
	}
	
	function createPageFromFile($parent, $arr)
	{
		$item = GW_Page::singleton()->createNewObject();
		
		
		if(isset($arr->content)){
			$content = $arr->content;
			unset($arr->content);
		}
		
		
		$item->setValues($arr);
		$item->parent_id = $parent->id ?: -1;
		
		if(GW::s('MULTISITE')){
			$item->site_id = $this->getSiteId();
		}
				
		
		
		$item->insert();
		
		if(isset($content) && $content){
			$item->importContent($content);
		}
		
		$this->importcnt++;
		$this->import_paths[] = $item->path;
		
		return $item;
	}
	
	function importTree($parent, $data)
	{
		if(isset($data->item)){
			$newparent = $this->createPageFromFile($parent, $data->item);
		} else {
			$newparent = $parent;
		}
			
		if(isset($data->childs))
			foreach($data->childs as $vals)
					$this->importTree($newparent, $vals);
		
	}
	
	function viewImportExportTree()
	{
		
	}
	
	function doImportExportTree()
	{
		$vals = $_POST['item'];
		$pid = $vals['parent_id'];;
		$act = $vals['action'];
		
		$item0 = GW_Page::singleton();
		$parent = $pid == -1 ? $item0 : $item0->find(['id=?', $pid]) ;
		
		if($act=='export'){
			$opts=[
			    'export_type'=>$vals['export_type'] ?: 'only_childs', 
			    'content'=>$vals['include_content'],
			];
			
			$lns = [];
			foreach($this->app->langs as $ln){
				if($vals['export_lns_'.$ln] || isset($_GET['alllns']))
					$lns[$ln]=1;
			}
			$opts['export_lns_vals'] = array_keys($lns);
			
			
			$opts['export_lns'] = $lns;			
			
			$this->doExportTree($parent, $opts);
		}elseif($act=='import'){
			
			$data = file_get_contents($_FILES['importfile']['tmp_name']);
			$data = json_decode($data);
			
					
			if(!$data){
				$this->setError("Invalid data");
			}else{				
				$this->importTree($parent, $data);
			}
			
			$this->setMessage('Import pages: '. $this->importcnt);
			$this->setMessage('New paths:<br><li>'.implode('<li>', $this->import_paths));
		}
	}
	
	function doAddExtLn()
	{
		$form = ['fields'=>[
		    'from'=>['type'=>'select','options'=>GW::s("LANGS"), 'empty_option'=>1, 'options_fix'=>1, 'required'=>1], 
		    'to'=>['type'=>'select','options'=>array_keys($this->app->i18next), 'empty_option'=>1, 'options_fix'=>1, 'required'=>1], 
		    ],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, GW::l('/m/i18n_EXT_ADD_LN'))))
			return false;		
		
		
		$src = $answers['from'];
		$dst = $answers['to'];
		
		
		$cond = false;
		
		if(GW::s('MULTISITE')){
			$cond = "site_id = ".$this->getSiteId();
		}
		
		$list = $this->model->findAll($cond);
		
		$cnt=0;
		
		foreach($list as $item){
			$item->set("in_menu_{$dst}", $item->get("in_menu_{$src}"));
			$item->updateChanged();
			$this->autotranslate($item);
			$cnt++;
		}
		
		$this->setMessage("Prepare pages: $cnt");
		
		$this->jump();
		
	}
	/////////////////---------------------IMPORT-EXPORT-----------------------------------------
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		foreach($cfg['fields'] as $field => $opts){
			$cfg['fields'][$field] = 'lof';
		}
		
		$cfg['fields']['ico'] = 'Lo';
		$cfg['fields']['path'] = 'lof';
		$cfg['fields']['pathname'] = 'Lof';
		$cfg['fields']['title'] = 'Lof';
		$cfg['fields']['in_menu'] = 'Lof';
		

		
		if(!GW::s('MULTISITE')){
			unset($cfg['fields']['multisite']);
			unset($cfg['fields']['site_id']);
		}		
		

		
		$cfg['filters']['type'] = ['type'=>'select','options'=>GW::l('/m/TYPE_OPT')];

		
		if(isset($this->tpl_vars['additfields'])){
			foreach($this->tpl_vars['additfields'] as $field)
				$cfg['fields'][$field] = 'lof';
		}
		
		//d::dumpas($cfg);
					
		
		
		return $cfg;
	}	
	
	function __eventAfterListConfig()
	{
		if(($this->list_params['search'] ?? false) || ($this->list_params['filters'] ?? false) && !in_array('path', $this->list_config['dl_fields'])){
			$this->list_config['dl_fields'][]='path';
		}
		
	}
	
	function __eventAfterList()
	{
		$this->options['template_id'] = $this->getTemplateList();
	
	}
	
	

	
	function doChangeParent()
	{
		$ids = $this->acceptIds(__FUNCTION__);
		
		$form = ['fields'=>[
		    'parent_id'=>['type'=>'select','options'=>$this->getParentOpt($ids[0]),'default'=>$_GET['pid'],'required'=>1,'size'=>10]
		    ],'cols'=>1];

		
		$opt = ['v'=>['cnt'=>count($ids)]];
		
		if(!($answers=$this->prompt($form, GW::l('/m/MOVE_X_ITEMS_TO',$opt))))
			return false;
		
		
		$list = GW_Page::singleton()->findAll(GW_DB::inCondition('id', $ids));
		$succ = 0;
		
		
		
		foreach($list as $item){
			if($this->canBeAccessed($item)){
				$item->parent_id = $answers['parent_id'];
				$item->updateChanged();
				$succ++;
			}else{
				$this->setError("{$item->title} cant access");
			}
		}
		$this->doFixPaths();
		$this->setMessage("Action performed on $succ items");
		$this->jump();
		
	}
	
	
	function viewTree()
	{
		if(GW::s('MULTISITE')){
			$site_id = isset($_GET['site_id']) ? explode(',',$_GET['site_id']) : [(int)$this->app->site->id];
			$this->tpl_vars['siteid'] = $site_id;
		}
	}
	
	
	function doGetTree()
	{
		//$list = $this->model->getFullTree();
		$ln = $this->app->ln;
		
		$cond = null;
		
		if(GW::s('MULTISITE')){
			$site_id = isset($_GET['site_id']) ? explode(',',$_GET['site_id']) : [(int)$this->app->site->id];
			$this->tpl_vars['siteid'] = $site_id;
			
			$cond = GW_DB::inCondition('site_id', $site_id);
		}

		
		$list0 = $this->model->findAll($cond, ['select'=>"id, site_id, pathname, title_{$ln}, active, parent_id, type, priority", 'key_field'=>'id', 'order'=>'priority']);
		
		$lostfound=0;
		
		
		foreach($list0 as $item){
			
			
			$sites[$item->site_id]=1;
			$parent =  $item->parent_id==-1 ? 's'.$item->site_id : $item->parent_id;
			
			if($item->parent_id != -1 && !isset($list0[$item->parent_id]) || (!GW::s('MULTISITE') && $item->site_id))
			{
				$lostfound=1;
				$parent="lost";;
			}
			
			$vals=[
			    "text"=>
			    '<span class="  '.($item->active?'text-success':'text-muted').' ">'.GW_String_Helper::truncate($item->pathname,25).'</span> '. GW_String_Helper::truncate(htmlspecialchars($item->title), 40)///.' '.$item->priority
			    , //. " ($item->id) ($item->priority)",debug
			    "parent"=>$parent,
			    "id"=>$item->id,
			    //"state" => ["opened" => true, "selected" => true],
			    "type" => 't'.$item->type
			];
			
			//if(!$item->active)
			//	$vals['a_attr'] = ['style'=>"opacity:0.5"];
			
				
			$list[] = $vals;
		}
		

		if(GW::s('MULTISITE')){
			foreach($sites as $id => $x){

				$list[]=[
				    "text"=>$this->options['site_id'][$id], //. " ($item->id) ($item->priority)",debug
				    "parent"=>'#',
				    "id"=>"s{$id}",
				    //"state" => ["opened" => true, "selected" => true],
				    "type" => 'tsite'
				];

			}			
		}else{
				$list[]=[
				    "text"=>"Website", //. " ($item->id) ($item->priority)",debug
				    "parent"=>'#',
				    "id"=>"s0",
				    //"state" => ["opened" => true, "selected" => true],
				    "type" => 'tsite'
				];			
		}

		
		if($lostfound)
			$list[]=['text'=>'Lost & found', "id"=>'lost','type'=>'t4','parent'=>'#'];
		

		header('content-type: text/plain');
		echo json_encode($list, JSON_PRETTY_PRINT);
		exit;
	}
	
	function doMoveNode()
	{
		if (!$item = $this->getDataObjectById())
			return false;

		$oldparent = $item->parent_id;
		$newparent = (string)($_GET['parent']=='#' ? -1 : $_GET['parent']); // string nes jei tipai nesutaps bus nustatytas pokytis
		
		
		
		
		if($newparent[0]=='s'){
			if(GW::s('MULTISITE')){
				$site_id = substr($newparent,1,999999);
				$item->site_id = $site_id;
			}
			$newparent = '-1';
			
		}
		
		
		$prevparent = $item->get('parent_id');
		$item->set('parent_id', $newparent);
		
		
		
			
		if($item->isChanged() || $_GET['old_priority']!=$_GET['priority'] ){
			
			if(isset($item->changed_fields['parent_id'])){
				$root = (object)['title'=>$this->options['site_id'][$item->site_id]." ROOT"];
				$prevparento = $prevparent == -1 ? $root  :  $this->model->find(['id=?',$prevparent]);
				$newparento = $newparent == -1 ? $root :   $this->model->find(['id=?',$newparent]);
				
				$this->afterParentIdChanges($item);
				
				$item->priority = $_GET['priority'];
				$item->updateChanged();
				$item->fixOrder();
				$this->setMessage(["text"=>"Moved to new parent $oldparent|{$prevparento->title}  -> {$item->parent_id}|{$newparento->title}", "type"=>GW_MSG_SUCC, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
				
			}else{
				
				$inf = $item->updatePositions($_GET['old_priority'], $_GET['priority']);
				$this->setMessage(["text"=>'Positions updated', "type"=>GW_MSG_SUCC, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
			}
		}else{
			$this->setMessage(["text"=>GW::l("/g/NO_CHANGES"), "type"=>GW_MSG_INFO, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
		}

		

		//if($this->isPacketRequest())	
		//	$this->app->addPacket(['action'=>'delete_row','id'=>$item->id, 'context'=>get_class($this->model)]);
	
		if(!$this->sys_call)
			$this->jump();
	}
	
	
	function doCreateNode()
	{
		$item = $this->model->createNewObject();
		
		
		$newparent = $_GET['parent'];
		
		if($newparent[0]=='s'){
			$site_id = substr($newparent,1,999999);
			$item->site_id = $site_id;
			$item->parent_id = '-1';
			
		}else{
			$newparento = $newparent == -1 ? $root :   $this->model->find(['id=?',$newparent]);
			$item->site_id = $newparento->site_id;
			$item->parent_id = $newparent;
		}		
		
		
		$item->title = "Unnamed";
		$item->priority = 9999999;
		$item->insert();
		$item->fixOrder();
		
		die(json_encode($item->toArray()));
	}	
	
	
	

	function __eventBeforeClone($ctx)
	{	
		
		
		//JSTREE after copy, paste
		//admin/lt/sitemap/pages/tree		
		if(is_array($ctx) && isset($ctx['src'])){
		
			$source = $ctx['src'];
			$dest = $ctx['dst'];
		
	
			$parent = GW_Page::singleton()->find($_GET['parent']);

			$dest->parent_id = $parent->id;
			$dest->site_id = $parent->site_id;

		}
	}
		
	function __eventAfterClone($ctx)
	{
		
		//JSTREE after copy, paste
		//admin/lt/sitemap/pages/tree		
		if(is_array($ctx) && isset($ctx['src'])){
			
		
			$source = $ctx['src'];
			$dest = $ctx['dst'];


			$content = $source->exportContent(['lns'=>$opts['export_lns_vals']]);

			$dest->importContent($content);

			$this->setMessage("Copied tpl vars: ".count($content));
			$this->doFixPaths();


			if(isset($_GET['returnonlynewid'])){
				echo $dest->id;
				exit;
			}
		}
	}	
	
	
	function injectVars($params, $vars)
	{
		$paramsjson= json_encode($params);
		
		if(strpos($paramsjson,'#')===false)
			return $params;
		
		
		///todo antru etapu escapint string vertes tam tikrus simbolius pavyzdziui eilutes perkelima, arba dvigubas kabutes
		//$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
		//$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
		//$result = str_replace($escapers, $replacements, $value);		
		
		foreach($vars as $field => $val){
			$paramsjson = str_replace("#{$field}#", $val, $paramsjson);
		}
		
		//d::ldump($paramsjson);
		
		return json_decode($paramsjson, true);
	}
	
}
