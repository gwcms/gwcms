<?php


class Module_Cfg extends GW_Common_Module
{	

	public $default_view = 'default';
	
	function init()
	{
		$this->model = new GW_Config('sys/');
		
		parent::init();
	}

	
	function viewDefault()
	{
		return ['item'=>$this->model];
	}
	
	
	
	function __afterSave(&$vals)
	{
		//;
	}
	
	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->app->setMessage($this->app->lang['SAVE_SUCCESS']);
		
		
		
		$this->__afterSave($vals);
		
		
		$this->jump();
	}

}

?>
