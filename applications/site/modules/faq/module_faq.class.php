<?php


class Module_FAQ extends GW_Public_Module
{
	var $view_path_index=2;

	function init()
	{
		parent::init();
		$this->model = new GW_FAQ();
		
		
		
	}


	function viewDefault()
	{

		$this->tpl_name = 'list';
		
		//$list=$this->model->findAll(Array('active=1 AND lang=?',$this->app->ln));
		
		$cond = 'active=1';
		
		
		

		
		$list=$this->model->findAll($cond);

		
				
		
		$this->smarty->assign('list', $list);
		
		
		
		///d::dumpas('what a heck');
	}
	
	





}