<?php

class GW_Translation extends GW_i18n_Data_Object
{
	public $table = 'gw_translations';
	public $i18n_fields = ['value'=>1];

	
	
	function store($module, $key, $value, $lang)
	{
		$db = $this->getDB();
		
		$db->save($this->table, ['module'=>$module, 'key'=>$key, 'value_'.$lang=>$value]);
	}
}