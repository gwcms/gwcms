<?php

class GW_Translation extends GW_i18n_Data_Object
{
	public $table = 'gw_translations';
	public $i18n_fields = ['value'=>1];

	
	
	function storeOne($db, $module, $key,$lang,$value){
		$db->save($this->table, ['module'=>$module, 'key'=>$key, 'value_'.$lang=>$value]);
	}
	
	function store($module, $key, $value, $lang)
	{
		$db = $this->getDB();
		
		
		if(is_array($value)){			
			$list = GW_Array_Helper::arrayFlattenSep ('/', $value);
			
			foreach($list as $akey => $value)
				$this->storeOne($db, $module, trim(str_replace('//','/',$key.'/'.$akey),'/'), $lang, $value);
					
		}else{
			$this->storeOne($db, $module, $key, $lang, $value);
		}
			
		
		
	}
}