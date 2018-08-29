<?php

class Module_Pages extends GW_Common_Module_Tree_Data 
{

	
	function init()
	{	
		parent::init();

		
	

		$this->app->carry_params['site_id']=1;
		
		if(isset($_GET['site_id']))
		{
			$this->filters['site_id']=$_GET['site_id'];
		}elseif(GW::s('MULTISITE')){
			$this->filters['site_id'] = $this->app->site->id;
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

				if ($item->id && isset($item->changed_fields['parent_id']))
					$this->afterParentIdChanges($item);
				

				if (GW::$settings['LANGS'][0] == $this->lang())
					$this->preparePage($context);
				break;
		}

		parent::eventHandler($event, $context);
	}

	//fix subitems paths
	function afterParentIdChanges($item) {

		$list = $item->findAll(['parent_id=?', $item->id]);

		foreach ($list as $item) {
			$item->fixPath();
			$item->updateChanged();
			$this->afterParentIdChanges($item);
		}
	}
	
	function doPreview()
	{
		$item = $this->getDataObjectById();
		
		
		$args=[];
		
		if(isset($_GET['shift_key']))
			$args['clean']=1;
		
		
		
		header('Location: /'.$this->app->ln.'/'.$item->path.($args ? '?'. http_build_query($args): ""));
	}
	
	
	function doFixUniqPathId()
	{
		foreach($this->model->findAll() as $page)
		{
			$page->fixUniqPathId(true);
			$page->updateChanged();
		}
	}
	

}
