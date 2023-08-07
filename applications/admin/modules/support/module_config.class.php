<?php


class Module_Config extends GW_Common_Module
{	

	public $default_view = 'default';
	
	function init()
	{
		$this->model = new GW_Config($this->module_path[0].'/');
		
		parent::init();
	}

	
	function viewDefault()
	{
		return ['item'=>$this->model];
	}
	
		
	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->setPlainMessage('/g/SAVE_SUCCESS');
		
		
		
		//$this->__afterSave($vals);
		
		
		$this->jump();
	}

}
