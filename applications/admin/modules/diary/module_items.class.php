<?php


class Module_Items extends GW_Common_Module
{	
	function breadcrumbsAttach()
	{
		if(! $this->parent->title)
			return;
		
		$breadcrumbs_attach=Array();
		
		foreach($this->parent->getParents() as $item)
			$breadcrumbs_attach[]=Array
			(
				'path'=>$this->app->fh()->gw_path(Array('params' => Array('pid'=>$item->id) )),
				'title'=>$item->title
			);
		
		$breadcrumbs_attach[]=Array('title'=>$this->parent->title, 'path'=>$this->app->fh()->gw_path(Array('params' => Array('pid'=>$this->parent->id) )));
		
		
		$this->tpl_vars['breadcrumbs_attach']=$breadcrumbs_attach;
	}
	
	function init()
	{	
		parent::init();
		
		$this->app->carry_params['pid']=1;
				
		$this->filters['parent_id'] = isset($_GET['pid']) && (int)$_GET['pid'] ? (int)$_GET['pid'] : -1;
		$this->filters['active']=1;
		
		//jeigu filtravimas
		if(isset($this->list_params['filters']))
			unset($this->filters['parent_id']);
			

		
		$this->parent=$this->model->createNewObject($this->filters['parent_id']);
		$this->parent->load();	
		
		if(!isset($_GET['act']))
			$this->breadcrumbsAttach();		
		
	}

	
	function viewDefault()
	{
		return $this->viewList(Array('order'=>'type DESC, insert_time DESC'));
	}
	
	function getMoveCondition($item)
	{
		$tmp = $this->filters;
		$tmp['type']=$item->get('type');
		
		return GW_SQL_Helper::condition_str($tmp);
	}
	
	function viewImport()
	{

	}
	
	function doDelete()
	{
		$do=$this->getDataObjectById();
		$do->set('active', 0);
		$do->update();
		
		$this->jump();
	}
    
	
	function doAjaxSave()
	{
		
		$vals = $_REQUEST['item'];	
		
		$item = $this->model->createNewObject($vals);
		
		if($item->id)
			$item->load();
			
		$item->setValues($vals);
		
		$item->save();
		
		
		exit;
	}	
	

}