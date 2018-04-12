<?php

class GW_Data_Object
{

	use Singleton;

	public $table;
	public $db_die_on_error = true;
	public $primary_fields = array('id');
	public $content_base = Array();
	public $errors = Array();
	public $error_codes = [];
	public $loaded = false;
	public $auto_fields = true;
	public $auto_validation = false; // calls $this->validate before save
	public $default_order = false;
	public $ignore_fields = Array();
	public $encode_fields = Array();
	public $calculate_fields = Array();
	static $_instance;
	public $cache;
	public $changed_fields = [];
	public $changed=false; //indicates if changed related composite objects

	/**
	 * pvz 
	 * 
	 * reiksmiu perdavimas
	 * $article = new Article(Array('title'=>'abc','active'=>1)); $article->save();
	 * 
	 * //gavimas pagal id
	 * $article=new Article(15); $article->load(); echo $article->title
	 * $article=new Article(15, true); echo $article->title
	 */
	function __construct($values = Array(), $load = false)
	{
		if (is_array($values))
			$this->setValues($values);
		elseif (!is_null($values) && count($this->primary_fields) == 1)
			$this->set($this->primary_fields[0], $values);

		if ($load)
			$this->load();

		$this->fireEvent('AFTER_CONSTRUCT');
	}

	function decodeFields()
	{
		foreach ($this->encode_fields as $key => $val) {
			if (!isset($this->content_base[$key]))
				continue;

			$func = "encode" . $this->encode_fields[$key];
			$this->content_base[$key] = $this->$func($key, $this->content_base[$key], true);
		}
	}

	function getStoreVal($key)
	{
		if (isset($this->encode_fields[$key])) {
			$func = "encode" . $this->encode_fields[$key];
			return $this->$func($key, $this->content_base[$key], false);
		}

		return $this->content_base[$key];
	}

	/**
	 * Atkreipti demensi i tai kad atliekamas laukelio pazymejimas i pakeista net tuo
	 * atveju kai paduodama (string)6 o buvo (int)6 (cast sensitive palyginimas)
	 */
	function set($key, $val)
	{
		if(strpos($key, '/')!==false)
		{
			$keys=explode('/', $key);
			$k1= array_shift($keys);
	
			if(isset($this->calculate_fields[$k1]) && is_object($this->$k1)){
				$store = $this->$k1;
			} else {
				$store =& $this->content_base[$k1];
				$this->changed_fields[$k1] = 1;
			}
			
			$this->__objAccessWrite($store, $keys, $val);			
			
			return true;
		}		
		
		
		if (!isset($this->content_base[$key]) || $this->content_base[$key] !== $val) {
			//d::ldump('item:'.$this->id.' CHANGE '.$this->content_base[$key].' -> '.$val);

			$this->content_base[$key] = $val;
			
			if(!isset($this->ignore_fields[$key]))
				$this->changed_fields[$key] = 1;
		}
	}

	function resetChangedFields()
	{
		$this->changed_fields = [];
	}

	function __objAccessRead($o, $keys)
	{		
		$tmp = $keys;
		
		$key = array_shift($keys);
		
		if(is_object($o->{$key}) && count($keys)>0)
			return $this->__objAccessRead ($o->{$key}, $keys);
		
		return $o->{$key};
	}
	
	function __objAccessWrite(&$o, $keys, $val)
	{
		$key= array_shift($keys);
		
		if(!is_object($o))
			$o = new stdClass();
		
		if(count($keys) > 0){
			if(!isset($o->$key))
				$o->$key = new stdClass();	
			
			return $this->__objAccessWrite($o->$key, $keys, $val);
		}
		
		$o->$key = $val;
		
		return $val;
	}
	
	function get($key)
	{
		
		if(strpos($key, '/')!==false)			
			return $this->__objAccessRead($this, explode('/', $key));
		
		if (isset($this->calculate_fields[$key])) {
			$func = $this->calculate_fields[$key];
			$func = $func == 1 ? 'calculateFieldCache' : $func;
			return $this->$func($key);
		}

		return isset($this->content_base[$key]) ? $this->content_base[$key] : false;
	}

	public function __unset($name)
	{
		unset($this->content_base[$name]);
	}

	function getCached($key, $f = 'get')
	{
		$cache = & $this->cache[$f . 'Cached'];

		if (isset($cache[$key]))
			return $cache[$key];

		$cache[$key] = $this->$f($key);

		return $cache[$key];
	}

	function setValues($vals)
	{
		foreach ($vals as $key => $val)
			$this->set($key, $val);
	}

	/**
	 * @return GW_DB
	 */
	function &getDB()
	{
		return GW::$context->vars['db'];
	}

	function createNewObject($values = array(), $load = false)
	{
		$class = get_class($this);
		$o = new $class($values, $load);
		return $o;
	}

	function &objResult(&$list)
	{
		$new = Array();

		foreach ($list as $key => $item) {
			$item = $this->createNewObject($list[$key]);
			$item->loaded = true;
			$new[$key] = $item;
		}

		return $new;
	}

	function lastRequestInfo()
	{
		$db = & $this->getDB();

		$info = Array
		    (
		    'last_query_time' => $db->last_query_time,
		    'item_count' => $db->fetch_result("SELECT FOUND_ROWS()")
		);

		return $info;
	}

	/**
	 * Overwrite for example to use view instead of real table
	 */
	function findAllTable($params)
	{
		$tables = ["`$this->table` AS a"];

		if (isset($params['from_extra'])) {
			foreach ($params['from_extra'] as $index => $table)
				$tables = "`$table` AS " . chr(97 + 1 + $index);
		}

		return implode(', ', $tables);
	}
	/*
	 * PAGAL susitarima FROM lenteles gauna aliasus a-z
	 * LEFT JOIN lenteles aa-az
	 * RIGHT JOIN lenteles ba-bz
	 * INNER JOIN ca-cz
	 * SUBQUERIU lenteles is select aaa-aaz
	 */

	function buildSql($options)
	{

		if (isset($options['sql']))
			return $options['sql']; // nothing to build, already have sql

		$conditions = isset($options['conditions']) ? $options['conditions'] : '';
		$select = isset($options['select']) ? $options['select'] : 'a.*';

		$offset = isset($options['offset']) ? $options['offset'] : 0;
		$order = isset($options['order']) ? $options['order'] : $this->getDefaultOrderBy();
		$data = array();

		$options['conditions'] = $conditions;
		$sql = "SELECT SQL_CALC_FOUND_ROWS {$select} FROM " . $this->findAllTable($options);


		//ussage example $options=['joins'=>[['RIGHT','table_name','condition AND condition']]]
		if (isset($options['joins']))
			foreach ($options['joins'] as $join)
				$sql.=" " . $join[0] . " JOIN " . $join[1] . " ON " . $join[2];


		if ($conditions)
			$sql.= ' WHERE ' . GW_DB::prepare_query($conditions);

		if (isset($options['group_by']) && $options['group_by'])
			$sql.= ' GROUP BY ' . $options['group_by'];

		if ($order)
			$sql.= ' ORDER BY ' . $order;

		if (isset($options['limit']))
			$sql.= " LIMIT {$offset}, {$options['limit']}";

		if (isset($options['dump'])) {
			dump($sql);
			exit;
		}

		return $sql;
	}

	function findAll($conditions = Null, $options = Array())
	{
		if ($conditions)
			$options['conditions'] = $conditions;

		$sql = $this->buildSql($options);


		$db = & $this->getDB();

		$nodie = isset($options['soft_error']) && $options['soft_error'] ? true : false;

		if (
		    isset($options['assoc_fields']) && $options['assoc_fields'] &&
		    isset($options['return_simple']) && $options['return_simple']
		) {
			$entries = $db->fetch_assoc($sql, $nodie);
		} elseif (isset($options['key_field'])){
			$fields = explode(',',$options['key_field']);
			
			if(count($fields) > 1){
				$entries = $db->fetch_assoc($sql, $nodie);
			}else{
				$entries = $db->fetch_rows_key($sql, $options['key_field'], $nodie);
			}
		}else{
			$entries = $db->fetch_rows($sql, 1, $nodie);
		}

		if ($db->error) {
			$this->errors[] = $db->getError();
			return null;
		}

		if (isset($options['return_simple']))
			return $entries;

		$entries = $this->objResult($entries);

		foreach ($entries as $item) {
			$item->loaded = true;
			$item->fireEvent('AFTER_LOAD');
		}

		return $entries;
	}

	/**
	 * example usage:
	 * $options = $groups->getAssoc(Array('id','title'));
	 * 
	 * //dump($options);:
	 * //Array(
	 * //'group1id'=>'group1title',
	 * //'group2id'=>'group2title'
	 * //)
	 * 
	 * //smarty:
	 * {html_options options=$options}
	 */
	function getAssoc($fields = Array(), $conditions = '', $options = Array())
	{
		$options['return_simple'] = 1;
		$options['assoc_fields'] = $fields;
		$options['select'] = '`'.implode('`,`', $fields).'`';
		if(count($fields) > 2)
			$options['key_field'] = $options['select'];


		return $this->findAll($conditions, $options);
	}
	
	function getDistinctVals($fieldname, $conditions = '', $options = Array())
	{
		$options['return_simple'] = 1;
		$options['assoc_fields'] = [$fieldname,1];
		$options['select'] = "DISTINCT `$fieldname`, 1";

		$arr = $this->findAll($conditions, $options);
		
		return array_keys($arr);		
	}
	

	function find($conditions = Null, $options = Array())
	{
		$options['limit'] = 1;

		return count($r = $this->findAll($conditions, $options)) ? $r[0] : false;
	}

	function load($fields = '*')
	{
		$vals = $this->find($this->getIdCondition(), Array('select' => $fields, 'return_simple' => 1));

		if (!$vals)
			return false;

		$this->setValues($vals);
		$this->loaded = true;

		$this->fireEvent('AFTER_LOAD');

		return true;
	}

	function load_if_not_loaded()
	{
		if (!$this->loaded)
			$this->load();
	}

	function count($condition)
	{
		$db = & $this->getDB();
		return $db->count($this->table, $condition, $this->db_die_on_error);
	}

	function countGrouped($groupby, $condition)
	{
		$counts_sql = "SELECT `$groupby`, count(*) AS cnt FROM `{$this->table}` WHERE $condition GROUP BY `$groupby`";

		return $this->getDb()->fetch_assoc($counts_sql);
	}

	function getColumns()
	{
		$db = & $this->getDB();
		$cols = $db->fetch_one_column("SELECT column_name FROM information_schema.columns WHERE table_name =  '" . $this->table . "'");

		return array_flip($cols);
	}
	
	function getColumnOptions($column)
	{
		return $this->getDB()->getColumnOptions($this->table, $column);
	}

	function getIdCondition()
	{
		$idfield = $this->primary_fields[0];
		return $this->getDB()->prepare_query(Array('`' . $idfield . '`=?', $this->get($idfield)));
	}

	/**
	 * by default updates all fields
	 * @param Array $field_names
	 * @return unknown_type
	 */
	function update($field_names = [], $params = [])
	{
		if ($this->auto_validation && !$this->validate())
			return false;

		if ($this->auto_fields && $field_names)
			$field_names[] = 'update_time';

		$context = [];

		if ($field_names)
			$context['update_only'] = $field_names;



		$this->fireEvent(['BEFORE_UPDATE', 'BEFORE_SAVE'], $context);

		if(isset($params['onlychanged']))
		{
			$field_names = array_keys($this->changed_fields);
			
			if(! $field_names)
				return false;
		}
		
		$entry = Array();
		$idfield = $this->primary_fields[0];
		$field_names = count($field_names) ? $field_names : array_keys($this->content_base);


		foreach ($field_names as $field)
			if (!isset($this->ignore_fields[$field]))
				$entry[$field] = $this->getStoreVal($field);

		unset($entry[$idfield]);

		$db = & $this->getDB();
		$rez = $db->update($this->table, $this->getIdCondition(), $entry);

		$this->fireEvent(['AFTER_UPDATE', 'AFTER_SAVE'], $context);

		return $rez;
	}

	function updateChanged()
	{
		return $this->update([], ['onlychanged'=>1]);
	}

	function showChanged()
	{
		$rez = [];
		foreach ($this->changed_fields as $fieldname => $x)
			$rez[$fieldname] = $this->get($fieldname);

		return $rez;
	}

	/**
	 * 2lines
	 * $user->setValues(Array('check_time'=>1));
	 * $user->update(Array('check_time'));
	 * 
	 * to
	 * 
	 * 1line
	 * $user->saveValues(Array('check_time'=>1));
	 */
	function saveValues($values)
	{
		$this->setValues($values);
		return $this->update(array_keys($values));
	}

	/**
	 * TODO: Panaikinti šį metodą (pasikartojantis kodas - blogis)
	 * vietoj to būtų galima idėti optiona i update($fieldnames, $options=Array())
	 * ir
	 * if(!isset($params['silent']))
	 * 		$this->fireEvent(Array('BEFORE_UPDATE','BEFORE_SAVE'));
	 * 
	 * 
	 * @param $field_names
	 * @return unknown_type
	 */
	function silentUpdate($field_names = Array())
	{
		if ($this->auto_validation && !$this->validate())
			return false;

		//$this->fireEvent(Array('BEFORE_UPDATE','BEFORE_SAVE'));	

		$entry = Array();
		$idfield = $this->primary_fields[0];

		if ($this->auto_fields && $field_names)
			$field_names = array_merge($field_names, Array('update_time'));

		$field_names = count($field_names) ? $field_names : array_keys($this->content_base);


		foreach ($field_names as $field)
			if (!$this->ignore_fields[$field])
				$entry[$field] = $this->getStoreVal($field);

		unset($entry[$idfield]);

		$db = & $this->getDB();

		$rez = & $db->update($this->table, $this->getIdCondition(), $entry);

		//$this->fireEvent(Array('AFTER_UPDATE','AFTER_SAVE'));

		return $rez;
	}

	function insert($replace = false)
	{
		$this->fireEvent(Array('BEFORE_INSERT', 'BEFORE_SAVE'));

		if ($this->auto_validation && !$this->validate())
			return false;

		$entry = Array();
		$idfield = $this->primary_fields[0];

		foreach ($this->content_base as $field => $x)
			if (!isset($this->ignore_fields[$field]))
				$entry[$field] = $this->getStoreVal($field);

		$db = & $this->getDB();

		$db->insert($this->table, $entry, false, $replace);
		$this->set($idfield, $db->insert_id());

		$rez = $this->get($idfield);

		$this->fireEvent(Array('AFTER_INSERT', 'AFTER_SAVE'));

		return $rez;
	}

	function replaceInsert()
	{
		return $this->insert(true);
	}

	function increase($field, $amount = 1)
	{
		$db = & $this->getDB();
		$db->increase($this->table, $this->getIdCondition(), $field, $amount);

		$this->set($field, (float) $this->get($field) + $amount);
	}

	function save()
	{
		return $this->get($this->primary_fields[0]) ? $this->update() : $this->insert();
	}

	function delete()
	{
		$this->fireEvent('BEFORE_DELETE');

		$db = & $this->getDB();

		$db->delete($this->table, $this->getIdCondition());

		$this->fireEvent('AFTER_DELETE');
	}

	function fireEvent($event, &$context_data = [])
	{
		if (!is_array($event))
			$this->EventHandler($event, $context_data);
		else
			foreach ($event as $e)
				$this->EventHandler($e, $context_data);
	}

	function __get($name)
	{
		return $this->get($name);
	}

	function __set($name, $value)
	{
		return $this->set($name, $value);
	}

	function invert($fieldname)
	{
		if (!$this->loaded)
			$this->load($fieldname);

		if (!$this->loaded)
			return false;

		$this->set($fieldname, (bool) $this->get($fieldname) ? 0 : 1);
		$this->update(Array($fieldname));

		return true;
	}

	function invertActive()
	{
		return $this->invert('active');
	}

	function getFirstError()
	{
		reset($this->errors);
		return current($this->errors);
	}

	function validate()
	{
		foreach ((array) $this->validators as $fieldname => $validator) {
			if (!(is_string($validator) || is_array($validator)))
				continue;

			$params = Array();

			if (is_array($validator))
				list($validator, $params) = $validator;
			
			if(strpos($validator,'func_')===0){
				$this->{substr($validator, 5)}($fieldname, $params);
			}else{
				if ($err = GW_Validator::getErrors($validator, $this->get($fieldname), $params))
					$this->setError($err[0], $fieldname);
			}
		}
		return $this->errors ? false : true;
	}

	function getDefaultOrderBy()
	{
		return $this->default_order ? $this->default_order : $this->primary_fields[0] . ' DESC';
	}

	function toArray()
	{
		$list = Array();

		foreach ($this->content_base as $field => $item)
			$list[$field] = $this->get($field);

		return $list;
	}

	static function listToArray($list)
	{
		$new_list = Array();
		foreach ($list as $item)
			$new_list[] = $item->toArray();

		return $new_list;
	}

	/**
	 * specify condition if items used for custom grouping
	 */
	function move($where, $conditions = '')
	{
		$db = $this->getDB();
		$id_field = $this->primary_fields[0];
		$id = (int) $this->get($id_field);

		$q = "SELECT `$id_field` FROM `$this->table`" . ($conditions ? " WHERE " . $conditions : '') . ' ORDER BY priority';
		

		$rows = $db->fetch_one_column($q, $id_field);

		if (($index = array_search($id, $rows)) === false)
			return true;

		//dump(Array('where'=>$where,'item_id'=>$id, 'index'=>$index, 'rows'=>$rows));

		if ($where == 'up') {
			if ($index == 0)
				return true;

			$tmp = $rows[$index - 1];
			$rows[$index - 1] = $rows[$index];
			$rows[$index] = $tmp;
		}elseif ($where == 'down') {
			if ($index == count($rows) - 1)
				return true;

			$tmp = $rows[$index + 1];
			$rows[$index + 1] = $rows[$index];
			$rows[$index] = $tmp;
		}

		//dump(Array('rows'=>$rows));

		$list = Array();
		foreach ($rows as $i => $row)
			$list[] = Array($id_field => $row, 'priority' => $i);

		$db->_multi_insert($this->table, $list, true);
	}

	public $order_limit_fields = Array();

	function getLimitOrdCondition()
	{
		$ordering_conditions = "";
		$params = Array();

		foreach ($this->order_limit_fields as $field) {
			$ordering_conditions.=($ordering_conditions ? ' AND ' : '') . " `$field`=?";
			$params[] = $this->$field;
		}

		if ($params)
			$ordering_conditions = GW_DB::prepare_query(array_merge(Array($ordering_conditions), $params));

		return $ordering_conditions ? $ordering_conditions : false;
	}

	function fixOrder()
	{
		$this->move("", $this->getLimitOrdCondition());
	}

	function savePositions($shifts, $conditions = '')
	{
		$db = $this->getDB();
		$id_field = $this->primary_fields[0];
		$q = "SELECT `$id_field` FROM `$this->table`" . ($conditions ? " WHERE " . $conditions : '') . ' ORDER BY priority';
		$rows = $db->fetch_one_column($q, $id_field);

		$list = Array();
		foreach ($rows as $i => $id)
			$list[$id] = Array($id_field => $id, 'priority' => $i);

		foreach ($shifts as $id => $shift)
			$list[$id]['priority']+=$shift;

		$db->_multi_insert($this->table, $list, true);
	}

	function encodeSerialize($fieldname, $value, $revert)
	{
		if ($revert) {
			if ($value)
				return unserialize($value);
		}else {
			if (is_array($value))
				return serialize($value);
		}
	}

	function encodeComma($fieldname, $value, $revert)
	{
		if ($revert) {
			if ($value)
				return explode(',', trim($value, ','));
		}else {
			if (is_array($value))
				return ',' . implode(',', $value) . ',';
		}
	}

	function encodeJSON($fieldname, $value, $revert, $object = false)
	{
		if ($revert) {
			if ($value)
				return json_decode($value, !$object);
		}else {
			if (is_array($value) || is_object($value))
				return json_encode($value);

			elseif (is_string($value)) // assume it is valid json
				return $value;
		}
	}

	function encodeJSONo($fieldname, $value, $revert)
	{
		return $this->encodeJSON($fieldname, $value, $revert, true);
	}

	function eventHandler($event, &$context_data = [])
	{
		switch ($event) {
			case 'BEFORE_UPDATE':
				if ($this->auto_fields)
					$this->set('update_time', date('Y-m-d H:i:s'));
				break;
			case 'BEFORE_INSERT':
				if ($this->auto_fields)
					$this->set('insert_time', date('Y-m-d H:i:s'));
				break;

			case 'AFTER_LOAD':
				$this->decodeFields();
				$this->resetChangedFields();
				break;
			case 'AFTER_CONSTRUCT':
				$this->resetChangedFields();
				break;
		}
	}

	function calculateField($name)
	{
		die('overide this');
	}

	function calculateFieldCache($key)
	{
		$cache = & $this->cache['calcf'];

		if (isset($cache[$key]))
			return $cache[$key];

		$cache[$key] = $this->calculateField($key);

		return $cache[$key];
	}

	public static function __callStatic($name, $arguments)
	{
		if (stripos($name, 'getBy') === 0) {
			//Example call GW_Articles::getById(15);
			//will work same as $o = new GW_Articles; $o->find("id=15")

			$field = substr($name, 5);

			$cn = get_called_class();
			$item_0 = new $cn;

			return $item_0->find(Array("`$field`=?", $arguments[0]));
		} elseif (stripos($name, 'static') !== false) {
			//Example call GW_Articles::findStatic("id=15");
			//will work same as $o = new GW_Articles; $o->find("id=15")

			$func = str_ireplace('static', '', $name);

			$cn = get_called_class();
			$item_0 = new $cn;

			return call_user_func_array(Array($item_0, $func), $arguments);
		} else {
			trigger_error("Unhandled static call", E_USER_ERROR);
		}
	}

	function __isset($name)
	{
		return isset($this->content_base[$name]) || isset($this->calculate_fields[$name]);
	}

	function setError($msg, $field = false, $error_code = GW_GENERIC_ERROR)
	{
		$this->errors[$field] = $msg;
		$this->error_codes[$error_code] = ($field ? $field . '::' : '') . $msg;
	}
	
	function prepareSave()
	{
		$this->fireEvent('PREPARE_SAVE');
	}
	
	function multiInsert($list, $replace=true)
	{
		$this->getDB()->multi_insert($this->table, $list, $replace);
	}
	
	function deleteMultiple($cond)
	{
		$this->getDB()->delete($this->table, $cond);
	}
	
	function attachAssocRecs($list, $fieldname, $obj_classname, $options=[])
	{
		$ids = [];
		foreach($list as $itm){
			if($itm->$fieldname)
			$ids[$itm->$fieldname]=$itm->$fieldname;
		}
		
		$o = new $obj_classname;
			
		if(!$ids)
			return false;
		
		$cond = GW_DB::inCondition('id', $ids);
				
		if(isset($options['simple_options']))
		{
			$key=$options['simple_options'];
			return $o->getAssoc(['id', $key], $cond);
		}else{
			return $o->findAll($cond, ['key_field'=>'id']);
		}	
	}

	function getByIds($ids, $opts=[])
	{
		if(!$ids)
			return [];
		
		$opts['key_field']='id';
			
		return $this->findAll(GW_DB::inCondition('id', $ids), $opts);
	}
	
}

