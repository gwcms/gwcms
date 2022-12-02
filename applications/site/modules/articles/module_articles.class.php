<?php


class Module_Articles extends GW_Public_Module
{
	var $view_path_index=2;

	function init()
	{
		parent::init();
		$this->model = new GW_Article();
		
		
		
		$this->tpl_vars['list_url']= $this->app->buildUri(GW::s('PATH_TRANS/articles/articles').'/list');		
		
		
		$this->tpl_vars['item_url']= $this->app->buildUri(GW::s('PATH_TRANS/articles/articles').'/item');	
		
		
	
				
	}


	function viewDefault()
	{

		$this->tpl_name = 'articles';
		
		//$list=$this->model->findAll(Array('active=1 AND lang=?',$this->app->ln));
		
		$cond = 'active=1';
		
		if($groupids = $this->app->page->getContent('group_id')){
			$groupids = json_decode($groupids, true);
			$cond = GW_DB::mergeConditions($cond, GW_DB::inCondition('group_id', $groupids));
			
		}elseif(isset($_GET['group'])){
			$cond = GW_DB::mergeConditions($cond, GW_DB::prepare_query(['group_id=?', $_GET['group']]));
		}
		
		

		
		$list=$this->model->findAll($cond);

		
		$this->tpl_vars['groups'] = GW_Articles_Group::singleton()->getOptions(true);
		
		
		$this->smarty->assign('list', $list);
		
		
		if(isset($_GET['id']))
		{
			$item = $this->model->createNewObject($_REQUEST['id'], 1);

			
			
			$this->tpl_vars['breadcrumbs_attach'][] =  [
			    'title' => GW_String_Helper::truncate($item->title, 40),
			    'url' => $_SERVER['REQUEST_URI']
			];			
			
			
			$this->smarty->assign('item', $item);
		}
		
		///d::dumpas('what a heck');
	}
	
	

	function viewShortList()
	{		
		$list=$this->model->findAll(Array('active=1 AND lang=?',$this->app->ln), Array('limit'=>3));

		$this->smarty->assign('list', $list);
	}

	function viewItem()
	{
			
		
		$this->viewDefault();
	}
	
	
	function viewIndex()
	{
		

		$list=$this->model->findAll('group_id=21 AND active=1', ['limit'=>4,'order'=>'insert_time DESC']);
		
		$this->tpl_vars['list'] = $list;
	}
	
	
	function viewClubs()
	{
		//AND active=1
		$list=$this->model->findAll('group_id=22 ', ['select'=>'id, title, short','limit'=>8,'order'=>'insert_time DESC']);
		
		
		$this->tpl_vars['list'] = $list;
	}


}