<?php


class GW_i18n_Data_Object extends GW_Data_Object
{
	var $i18n_fields=Array();
	var $_lang;//i18n
	
	function __construct($values=Array(), $load=false, $lang=false)
	{
		if($lang)
			$this->_lang = $lang;
					
		parent::__construct($values, $load);
	}
	
	function createNewObject($values = array(), $load=false, $lang=false)
	{
		$class = get_class($this);
		$o = new $class($values, $load, $lang);
		return $o;
	}	
		
	function isI18NField($name)
	{
		return isset($this->i18n_fields[$name]) ;
	}		
		
	function getDefaultLn()
	{
		if($this->_lang)
			return $this->_lang;

		if($tmp = GW::$context->app->ln)
			return $tmp;
			
		return GW::$settings['LANGS'][0];
	}


	function getI18NFieldName($name, $ln=false)
	{
		if(!$this->isI18NField($name))
			return $name;

		$ln = $ln ? $ln : $this->getDefaultLn();
			
		return "{$name}_{$ln}";
	}

	function set($name, $value, $ln=false)
	{
		/*
		if($key=='_lang')
		{
			$this->_lang = $val;
			return false;
		}*/		
		
		return parent::set($this->getI18NFieldName($name, $ln), $value);
	}

	function get($name, $ln=false)
	{
		return parent::get($this->getI18NFieldName($name, $ln));
	}

	function setValues($vals, $ln=false)
	{
		foreach($vals as $key => $val)
			$this->set($key,$val, $ln);
	}
	
	function addLang($default_lang, $create_lang)
	{
		$list = $this->getDB()->fetch_rows_key("SHOW COLUMNS FROM `$this->table`",'Field');
		
		$copy = [];
		foreach($this->i18n_fields as $field => $x)
			$copy[$field.'_'.$default_lang]=1;
		
		$structures = array_intersect_key($list, $copy);
		
		$sqls = [];
		
		foreach($this->i18n_fields as $fname =>$x){
			$s = $structures[$fname.'_'.$default_lang];
			
			$type = $s['Type'];
			$null = $s['Null']=='YES' ? "NULL" : "NOT NULL";
			
			$default = $s['Default'] ? "DEFAULT ".$s['Default'] : '';
			$new = $fname.'_'.$create_lang;
			$old = $fname.'_'.$default_lang;
			$comment = "COMMENT  'copy from $old'";
				
			$sqls[] = "ALTER TABLE  `$this->table` ADD  `$new` $type $null $default $comment  AFTER  `$old` ;";
		}
		
		foreach($sqls as $sql){
			$this->getDB()->query($sql);
		}
		
		return $sqls;
		
	}
	
	function dropLang($lang)
	{
		$del=[];
		
		$sqls = [];
		foreach($this->i18n_fields as $field => $x)
			$sqls[]="ALTER TABLE  `$this->table` DROP  `{$field}_{$lang}` ";
			
		foreach($sqls as $sql){
			$this->getDB()->query($sql);
		}
		
		return $sqls;		
		
	}	

}

