<?php


class Module_Config extends GW_Common_Module
{	

	public $default_view = 'default';	
	
	function init()
	{		
		$this->model = new GW_Config($this->module_path[0].'/');
		
		$cachetime="1 hour";

		
		parent::init();
	}

	
	function viewDefault()
	{
		
		
		
		$item=$this->tpl_vars['item']=$this->model;
		$item->preload('');
		
		


		
		//return ['item'=>$this->model];
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
	
	
	
	//nationalities
	//https://api.ryanair.com/userprofile/rest/api/v1/open/nationalities
	

}

?>
