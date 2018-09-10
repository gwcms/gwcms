<?php


class Module_Articles extends GW_Public_Module
{
	var $view_path_index=2;

	function init()
	{
		$this->model = new GW_Article();
		
		
	}


	function viewDefault()
	{

		
		$list=$this->model->findAll(Array('active=1 AND lang=?',$this->app->ln));

		$this->smarty->assign('list', $list);
	}

	function viewShortList()
	{		
		$list=$this->model->findAll(Array('active=1 AND lang=?',$this->app->ln), Array('limit'=>3));

		$this->smarty->assign('list', $list);
	}

	function viewItem()
	{
		$item = $this->model->createNewObject($_REQUEST['id'], 1);

		$this->smarty->assign('item', $item);
	}
	
	
	function viewIndex()
	{
		$list=$this->model->findAll(Array('active=1'));
		
		$this->tpl_vars['list'] = $list;
	}


}