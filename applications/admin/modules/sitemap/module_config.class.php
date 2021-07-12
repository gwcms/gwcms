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
		
		foreach($vals as $key => $val)
			if(is_array($val))
				$vals[$key] = json_encode($val);
			
		
		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->app->setMessage(GW::l('/g/SAVE_SUCCESS'));
		
		
		
		//$this->__afterSave($vals);
		
		
		$this->jump();
	}

}

?>
