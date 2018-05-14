<?php

class GW_Translation_Over extends GW_i18n_Data_Object
{

	public $table = 'gw_translations_over';
	public $i18n_fields = ['value' => 1];
	public $calculate_fields = ['fullkey'=>1,'title'=>1];
	//public $ignore_fields = ['fullkey'=>1];

	function storeOne($db, $module, $key, $lang, $value)
	{
		$db->save($this->table, ['module' => $module, 'key' => $key, 'value_' . $lang => $value]);
	}

	function store($module, $key, $value, $lang)
	{
		$db = $this->getDB();


		if (is_array($value)) {
			$list = GW_Array_Helper::arrayFlattenSep('/', $value);

			foreach ($list as $akey => $value)
				$this->storeOne($db, $module, trim(str_replace('//', '/', $key . '/' . $akey), '/'), $lang, $value);
		} else {
			$this->storeOne($db, $module, $key, $lang, $value);
		}
	}
	
	function calculateField($name) {
		
		switch($name){
			case "fullkey":
				return  $this->get('module') && $this->get('key') ? $this->get('module') .'/'.$this->get('key') : '';
			break;
			case "title":
				return $this->fullkey;
			break;
		}
		
		parent::calculateField($name);
	}
	
	function eventHandler($event, &$context_data = array()) 
	{
		switch($event){
			case "BEFORE_SAVE":
				$xpld = explode('/',$this->content_base['fullkey'], 3);
				$this->set('module', $xpld[0].'/'.$xpld[1]);
				$this->set('key',$xpld[2]);
				
				
				
				unset($this->content_base['fullkey']);
				unset($this->changed_fields['fullkey']);
			break;
		}		
		parent::eventHandler($event, $context_data);
	}
}
