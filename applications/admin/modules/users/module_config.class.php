<?php


class Module_Config extends GW_Common_Module
{	

	public $default_view = 'default';
	
	function init()
	{
		$this->model = new GW_Config('gw_'.$this->module_path[0].'/');
		
		parent::init();
	}

	function initEnabledFields()
	{
		$list = explode(',',$this->model->available_fields);
		$opts = [];
	
		foreach($list as $field)
			$opts[$field] = GW::l('/A/FIELDS/'.$field);
		
		$this->options['fields_enabled'] = $opts;
		
		//d::dumpas($this->model->available_fields);
	}
	
	function viewDefault()
	{
		$this->initEnabledFields();
		
		
		$cfg = $this->model;
		$cfg->preload('');
		$vals=$cfg->exportLoadedValsNoPrefix();
		$item = (object)$vals;
		
		
		
		$item->fields_enabled = @json_decode($item->fields_enabled, true);
		
		
		
		return ['item'=>$item];
	}
	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		$vals['fields_enabled'] = json_encode($vals['fields_enabled']);

		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->setMessage($this->app->lang['SAVE_SUCCESS']);		
		
		$this->jump();
	}

}

?>
