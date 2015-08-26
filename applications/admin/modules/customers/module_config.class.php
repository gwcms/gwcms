<?php


class Module_Config extends Module_MIS_Common
{	

	public $default_view = 'default';
	
	function init()
	{
		$this->options['customer_group']=  GW_Users_Group::singleton()->getOptions();
		
	
		
		
		
		$this->model = new GW_Config($this->module_path[0].'/');
		
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
