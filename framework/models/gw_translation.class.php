<?php

class GW_Translation extends GW_i18n_Data_Object
{

	public $table = 'gw_translations';
	public $i18n_fields = ['value' => 1];
	public $calculate_fields = ['title'=>'fullkey','modnamefix'=>'modnamefix'];
	public $skip_i18next = true;

	function storeOne($db, $module, $key, $lang, $value, $backend=0)
	{		
		$db->save($this->table, $opts=[
		    'module' => $module, 
		    'key' => $key, 
		    'value_' . $lang => $value, 
		   // 'backend'=>$backend
		]);
	}

	function store($module, $key, $value, $lang, $backend=0)
	{
		$db = $this->getDB();

		if (is_array($value)) {
			$list = GW_Array_Helper::arrayFlattenSep('/', $value);

			foreach ($list as $akey => $value)
				$this->storeOne($db, $module, trim(str_replace('//', '/', $key . '/' . $akey), '/'), $lang, $value, $backend);
		} else {
			$this->storeOne($db, $module, $key, $lang, $value, $backend);
		}
	}
	
	function fullkey()
	{
		if($this->get('module') && $this->get('key'))
			return $this->get('module').'/'.$this->get('key');
	}
	
	function modnamefix()
	{
		$split = explode('/',$this->get('module'));
		
		
		//pasalinti blogus vertimus
		if(count($split) != 2){
			$this->delete();
		}
			
			
		return $split[0] .'/'.strtolower($split[1]);
	}
	
	
	static function fullkeyToModAndKey($fullkey)
	{
		list($group,$module, $key) = explode('/', $fullkey, 3);
		$module = $group."/".$module;		
		
		return [$module, $key];
	}

	function findByFullKey($fullkey)
	{
		list($module, $key) = $this->fullkeyToModAndKey($fullkey);
			
		return $this->find(['module=? AND `key`=?', $module, $key]);		
	}
	
	function validate() 
	{	
		$split = explode('/',$this->get('module'));
		
		if(count($split) != 2)
			$this->errors['module'] = '/M/datasources/BAD_TRANSLATION_MODULE_NAME';				
	
		return parent::validate();
	}	
	
}
