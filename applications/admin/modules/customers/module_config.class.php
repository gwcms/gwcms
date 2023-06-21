<?php


class Module_Config extends GW_Common_Module
{	

	public $default_view = 'default';
	
	function initEnabledFields()
	{
		$list = explode(',',$this->model->available_fields);
		$opts = [];
	
		foreach($list as $field)
			$opts[$field] = GW::ln('/M/users/FIELDS/'.$field).' ('.$field.')';
		
		$this->options['fields_enabled'] = $opts;
		
		//d::dumpas($this->model->available_fields);
	}	
	
	function init()
	{
		$this->options['customer_group']=  GW_Users_Group::singleton()->getOptions();
		

		$this->model = new GW_Config($this->module_path[0].'/');
		$this->initEnabledFields();
		
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
		$this->setMessage('/g/SAVE_SUCCESS');
		
		
		
		$this->__afterSave($vals);
		
		
		$this->jump();
	}

}

?>
