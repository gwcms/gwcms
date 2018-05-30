<?php


class Module_Config extends GW_Common_Module
{	

	public $default_view = 'default';
	
	function init()
	{		
		$this->model = new GW_Config($this->module_path[0].'/');
		
		$this->options['user_groups'] = GW_Users_Group::singleton()->getOptions();
			
		parent::init();
	}

	
	function viewDefault()
	{
		return ['item'=>$this->model];
	}
	
	
	function viewInvoice()
	{
		return $this->viewDefault();
	}	
	
	function viewEmailTemplates()
	{
		return $this->viewDefault();
	}		
	

	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		//$vals['array'] = json_encode($vals['array']);
		
		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->app->setMessage($this->app->lang['SAVE_SUCCESS']);
		
		
		
		$this->__afterSave($vals);
		
		
		$this->jump();
	}

}

?>
