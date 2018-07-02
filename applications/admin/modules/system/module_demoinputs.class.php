<?php


class Module_DemoInputs extends GW_Common_Module
{	

	public $default_view = 'default';
	
	function init()
	{
		
		
		$this->model = new GW_Config($this->module_path[0].'/');
		
		parent::init();
	}

	
	function viewDefault()
	{
		$vals = (object)$this->model->preload('demo_');
		
		
		return ['item'=>$this->model];
	}
	
	
	
	
	
	function __afterSave(&$vals)
	{
		//;
	}

	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		
		foreach($vals as $key => $val)
		{
			if(is_array($val))
				$vals[$key] = json_encode($val);
		}
		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->setMessage('/g/SAVE_SUCCESS');
		
		
		
		$this->__afterSave($vals);
		
		
		$this->jump();
	}

}

?>
