<?php


class GW_Uni_Schema{
	
	public $schema=[];
	
	static function initSchema($type)
	{
		$this->schema[$type] = GW::db()->fetch_assoc("SELECT `str`,`id` FROM gw_uni_schema WHERE ".GW_DB::prepare_query(['`type`=?', $type]));
	}
	
	static function CachedQuery($type, $str)
	{
		//learn tables
		if(!isset($this->schema[$type][$str])){
			GW::db()->insert('gw_i18next_schema', ['type'=>$type,'str'=>$str, 'id'=>count($this->i18next_schema[$type] ?? [])+1]);
			$this->initI18nSchema();
		}		
		
		return $this->schema[$type][$str];
	}
	
	
	static function getByStr($type,$str)
	{
		return GW::db()->fetch_row("SELECT `str`,`id` FROM gw_uni_schema WHERE ".GW_DB::prepare_query(['`type`=? AND str=?', $type, $str]));
	}


	static function getIdxByStr($type, $str, $create=true){
		
		
		//koks ilgis duomenu bazeje nustatytas // kad nekurtu kopijos
		$str = substr($str, 0, 200);
				
		if($tmp=self::getByStr($type, $str))	
			return $tmp['id'];
		
		if($create)
			GW::db()->insert('gw_uni_schema', ['type'=>$type,'str'=>$str]);
		
		return GW::db()->insert_id();
	}
}