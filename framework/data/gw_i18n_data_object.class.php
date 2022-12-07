<?php

class GW_i18n_Data_Object extends GW_Composite_Data_Object
{

	public $i18n_fields = Array();
	public $i18next_langs = [];
	public $skip_i18next = false;
		
	var $_lang; //i18n

	function __construct($values = Array(), $load = false, $lang = false)
	{
		if ($lang)
			$this->_lang = $lang;
		
		
		if (GW::$context->app->i18next && !$this->skip_i18next)
		{
			$this->extensions = $this->extensions + ['i18next'=>1];	
			$this->i18next_langs = array_keys(GW::$context->app->i18next);
		}		

		parent::__construct($values, $load);
	}

	function createNewObject($values = array(), $load = false, $lang = false)
	{
		$class = get_class($this);
		$o = new $class($values, $load, $lang);
		return $o;
	}

	function isI18NField($name)
	{
		return isset($this->i18n_fields[$name]);
	}

	function getDefaultLn()
	{
		if ($this->_lang)
			return $this->_lang;

		if ($tmp = GW::$context->app->ln)
			return $tmp;

		return GW::$settings['LANGS'][0];
	}

	function getI18NFieldName($name, $ln = false)
	{
		if (!$this->isI18NField($name))
			return $name;

		$ln = $ln ? $ln : $this->getDefaultLn();

		return "{$name}_{$ln}";
	}

	function set($name, $value, $ln = false)
	{
		/*
		  if($key=='_lang')
		  {
		  $this->_lang = $val;
		  return false;
		  } */

		return parent::set($this->getI18NFieldName($name, $ln), $value);
	}

	function get($name, $ln = false)
	{
		
		if($this->isI18NField($name) && GW::$context->app->app_name == 'SITE'){
			$ln = $ln ? $ln : $this->getDefaultLn();
			
			
			if($tmpO = parent::get($this->getI18NFieldName($name, $ln)))
					return $tmpO;
			
			if($ln == 'ua'){
				$tmp = parent::get($this->getI18NFieldName($name, 'ru'));
				if($tmp && is_string($tmp) && !is_numeric($tmp)){
					GW_Auto_Translate_Helper::collectTrans($this, $name,'ru','ua');
				}
				
				if($tmpO = parent::get($this->getI18NFieldName($name, $ln)))
					return $tmpO;
			}
			
			if($ln !='en'){
				$tmp = parent::get($this->getI18NFieldName($name, 'en'));
				
				$LNINF = "";
				if(GW::$context->app->user){
					if(GW::$context->app->user->group_ids){
						$LNINF = "EN: ";
					}
				}

				return $tmp && is_string($tmp) && !is_numeric($tmp) ? $LNINF.$tmp: $tmpO;
			}
		}
		
		return parent::get($this->getI18NFieldName($name, $ln));
	}
	
	function getOrig($name, $ln=false)
	{
		return parent::get($this->getI18NFieldName($name, $ln));
	}
	

	function setValues($vals, $ln = false)
	{
		foreach ($vals as $key => $val)
			$this->set($key, $val, $ln);
	}

	function addLang($default_lang, $create_lang)
	{
		$list = $this->getDB()->fetch_rows_key("SHOW COLUMNS FROM `$this->table`", 'Field');

		$copy = [];
		foreach ($this->i18n_fields as $field => $x)
			$copy[$field . '_' . $default_lang] = 1;

		$structures = array_intersect_key($list, $copy);

		$sqls = [];

		foreach ($this->i18n_fields as $fname => $x) {
			$s = $structures[$fname . '_' . $default_lang];

			$type = $s['Type'];
			$null = $s['Null'] == 'YES' ? "NULL" : "NOT NULL";

			$default = $s['Default'] ? "DEFAULT " . $s['Default'] : '';
			$new = $fname . '_' . $create_lang;
			$old = $fname . '_' . $default_lang;
			$comment = "COMMENT  'copy from $old'";

			$sqls[] = "ALTER TABLE  `$this->table` ADD  `$new` $type $null $default $comment  AFTER  `$old` ;";
		}

		foreach ($sqls as $sql) {
			$this->getDB()->query($sql, true);
			if($err=$this->getDB()->getError())
			{
				d::ldump($err);
			}
		}

		return $sqls;
	}

	function dropLang($lang)
	{
		$del = [];

		$sqls = [];
		foreach ($this->i18n_fields as $field => $x)
			$sqls[] = "ALTER TABLE  `$this->table` DROP  `{$field}_{$lang}` ";

		foreach ($sqls as $sql) {
			$this->getDB()->query($sql);
		}

		return $sqls;
	}
	
	
	function buildFieldCond($field, $search, $eq="LIKE", $join="OR"){

		$conds = [];
		
		foreach(GW::s('LANGS') as $ln)
		{
			$conds[]="{$field}_{$ln} $eq $search";
		}
		
		return '('.implode(" $join " , $conds).')';
	}
}
