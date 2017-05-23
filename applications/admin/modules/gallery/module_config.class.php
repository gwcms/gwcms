<?php


class Module_Config extends GW_Common_Module
{	
	
	public $default_view = 'default';
	
	function init()
	{
		
		
		parent::init();
		$this->model = new GW_Config('gallery/');		
	}

	
	function viewDefault()
	{
		$this->tpl_vars['item']=$this->model;
	}
	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->setPlainMessage('/g/SAVE_SUCCESS');
		
		$this->jump();
	}

}
