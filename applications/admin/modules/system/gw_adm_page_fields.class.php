<?php


/**
 * Description of gw_adm_page
 *
 * @author wdm
 */
class GW_Adm_Page_Fields extends GW_i18n_Data_Object
{
	public $default_order = 'priority ASC';	
	public $validators = [
	    'type' => ['gw_string', ['required'=>1]],
	    'fieldname' => ['gw_string', ['required'=>1]],
	];		
	
	public $i18n_fields = [
	    "title"=>1,
	    "placeholder"=>1,
	    "note"=>1,
	    "hidden_note"=>1,
	];
	
	public $encode_fields = ['config'=>'json'];

	
	function getTypes($field)
	{
		return  $this->getDB()->getColumnOptions($this->table, $field);
	}
	
	function modelFromModpath()
	{
		$page = GW_ADM_Page::singleton()->getByPath($this->modpath);
		if($page)
			return $page->info->model;
	}
	
	function validate(){
		
		parent::validate();
		
		$cond=Array
		(
			'parent=? AND `fieldname`=? AND id!=?',
			$this->get('parent'),
			$this->get('fieldname'), 
			(int)$this->get('id')
		);
		
		if($duplicate = $this->find($cond))
			$this->errors['fieldname']='/G/VALIDATION/UNIQUE';
			
		
		
		return !(bool)count($this->errors);
	}
	
	
	
}