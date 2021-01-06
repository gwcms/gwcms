<?php

class Module_Pages extends GW_Common_Module_Tree_Data 
{

	
	function init()
	{	
		$this->app->carry_params['site_id']=1;
		
		
		parent::init();

		
	

		
		if(isset($_GET['site_id']))
		{
			$this->filters['site_id']=$_GET['site_id'];
		}elseif(GW::s('MULTISITE')){
			$this->filters['site_id'] = $this->app->site->id;
		}
		
		
		
		
		
		if($this->filters['site_id']){
						
			
			$this->site = GW_Site::singleton()->createNewObject($this->filters['site_id'], true);
			$this->tpl_vars['breadcrumbs_attach'] = $this->tpl_vars['breadcrumbs_attach'] ?: [];
			array_unshift($this->tpl_vars['breadcrumbs_attach'], [
			    'title'=>$this->site->title, 
			    'path'=>$this->buildUri('', ['site_id'=>$this->site->id,'pid'=>0])
			    ]);
		}
		
		
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
		
		if(isset($_GET['site_id'])){
			$site = GW_Site::singleton()->createNewObject($_GET['site_id'], true);
			$host = "http://".explode(',',$site->hosts)[0];
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
}
