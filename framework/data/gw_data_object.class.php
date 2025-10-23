<?php

class GW_Data_Object
{

	use Singleton;

	public $table;
	public $db_die_on_error = true;
	public $primary_fields = array('id');
	public $content_base = [];
	public $decoded_content_base = [];
	public $errors = [];
	public $error_codes = [];
	public $loaded = false;
	public $auto_fields = true;
	public $auto_validation = false; // calls $this->validate before save
	public $default_order = false;
	public $ignore_fields = [];
	private $_ignore_fields = ['temp_id'=>1];
	
	public $encode_fields = [];
	
	public $calculate_fields = [];
	static $_instance;
	public $cache=[];
	public $changed_fields = [];
	public $changed=false; //indicates if changed related composite objects
	public $extensions=[];
	protected $observers = [];
	public $constructcomplete=false;
	public $is_db_based = true;
	public $inherit_props = [];
	public $extra_cols = [];
	public $base_args=[];	

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

		if(!$this->table)
			$this->table = strtolower(get_class($this));
		
		$this->initExtensions();
		
		if ($load)
			$this->load();
		
		$this->ignore_fields += $this->_ignore_fields;
				
		$this->fireEvent('AFTER_CONSTRUCT');
		$this->constructcomplete = true;
		$this->calculate_fields['classname'] = 1;
	}
	
	function initExtensions()
	{
		foreach($this->extensions as $extension => $x)
		{
			$class="GW_Extension_$extension";
			$this->extensions[$extension] = new $class($this, $extension);
		}
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

	function &getDecoded($key)
	{			
		if(!isset($this->decoded_content_base[$key])){
			$func = "encode" . $this->encode_fields[$key];
			$this->decoded_content_base[$key] = isset($this->content_base[$key]) ? $this->$func($key, $this->content_base[$key], true) : [];
		}
		
		return $this->decoded_content_base[$key];
	}
	
	function getStoreVal($key)
	{
		if (isset($this->encode_fields[$key])) {
			$func = "encode" . $this->encode_fields[$key];
			$store =& $this->getDecoded($key);
			return $this->$func($key, $store, false);			
		}

		return $this->content_base[$key] ?? null;
	}

	/**
	 * Atkreipti demensi i tai kad atliekamas laukelio pazymejimas i pakeista net tuo
	 * atveju kai paduodama (string)6 o buvo (int)6 (cast sensitive palyginimas)
	 */
	function set($key, $val)
	{
		
		if($this->loaded && $this->changetrack_max ?? false)
			$this->fireEvent('BEFORE_CHANGES');
		
		if(strpos($key, '/')!==false)
		{			
			$keys=explode('/', $key);
			$k1= array_shift($keys);
			
			if($this->loaded && isset($this->encode_fields[$k1]))
			{			
				$store =&  $this->getDecoded($k1);
				$this->changed_fields[$k1] = 1;
								
			}elseif(isset($this->calculate_fields[$k1]) && is_object($this->$k1)){
				$store = $this->$k1;
			} else {
				$store =& $this->content_base[$k1];
				$this->changed_fields[$k1] = 1;
			}
			
			$this->__objAccessWrite($store, $keys, $val);			
			
			return true;
		}
		
		//construct complete keciu i loaded 2018-10-24 / blogai veikia serializuojant paprasta masyva, po formos issaugojimo
		//common modulis pirma construct patadaro tada set values, load padaro sekanciu zingsniu
		
		if($this->loaded && isset($this->encode_fields[$key]))
		{
			$data =&  $this->getDecoded($key);
			$data = $val;
			$this->changed_fields[$key] = 1;
		}
		
		if (!isset($this->content_base[$key]) || $this->content_base[$key] !== $val) {
			//d::ldump('item:'.$this->id.' CHANGE '.$this->content_base[$key].' -> '.$val);

			if(!$this->constructcomplete || !isset($this->content_base[$key]) || $this->content_base[$key] != $val){
				$this->content_base[$key] = $val;


			
				if($this->constructcomplete && !isset($this->ignore_fields[$key]))
					$this->changed_fields[$key] = 1;
			}
			
		}
	}

	function resetChangedFields()
	{
		$this->changed_fields = [];
	}

	function __objAccessRead($o, $keys)
	{		
		$tmp = $keys;	
		
		//allow keyval extension to read key containing "/" characters
		if($o instanceof GW_Extension_KeyVal){
			return $o->get(implode('/', $keys));
		}
				
		
		$key = array_shift($keys);
		
		
		//d::ldump( ['isset key'=>$key, 'class'=>get_class($o), 'result'=>isset($o->{$key})] );
		if(!isset($o->{$key}) )
			return null;
				
		if(is_object($o->{$key}) && count($keys)>0)
			return $this->__objAccessRead ($o->{$key}, $keys);
		
		return $o->{$key};
	}
	
	function __objAccessWrite(&$o, $keys, $val)
	{
		//allow keyval extension to write key containing "/" characters
		if($o instanceof GW_Extension_KeyVal){
			$o->set(implode('/', $keys), $val);
			return true;
		}
		
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
		{
			$keys=explode('/', $key);
			$k1= array_shift($keys);
			
			if($this->loaded && isset($this->encode_fields[$k1]))
			{			
				$store =&  $this->getDecoded($k1);
			} elseif(isset($this->calculate_fields[$k1])){
				$store = $this->get($k1);
				//$store->{$keys[0]};
				
			}else {
				$store =& $this->content_base[$k1];
			}
					
			return $this->__objAccessRead($store, $keys);
		}		
		
		
		if (isset($this->calculate_fields[$key])) {
			$func = $this->calculate_fields[$key];
			$func = $func == 1 ? 'calculateFieldCache' : $func;
			return $this->$func($key);
		}
		
		if(isset($this->encode_fields[$key]))
		{			
			return $this->getDecoded($key);
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
				
		$this->inheritProps($o);
		
		return $o;
	}
	
	function inheritProps($o)
	{
		foreach($this->inherit_props as $prop)
			$o->$prop = $this->$prop;
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

	private $lastRequestInfoPrepared;
	
	function lastRequestInfo()
	{
		if($this->lastRequestInfoPrepared)
		{
			$tmp = $this->lastRequestInfoPrepared;
			$this->lastRequestInfoPrepared = false;
			return $tmp;
		}
		
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
				$tables[] = "`$table` AS " . chr(97 + 1 + $index);
		}
		
		if (isset($params['from_add'])) {
			foreach ($params['from_add'] as $index => $table)
				$tables[] = "`$table` AS `$index`";
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
	
	function __addJoins($joins, &$sql){
		foreach ($joins as $join)
				$sql.=" " . $join[0] . " JOIN " . $join[1] . " ON " . $join[2];
	}

	function buildSql($options)
	{
		if (isset($options['sql']))
			return $options['sql']; // nothing to build, already have sql

		$conditions = isset($options['conditions']) ? $options['conditions'] : '';
		$select = isset($options['select']) ? $options['select'] : 'a.*';
		
		if(isset($this->base_args['select_add']))
			$select.=', '.$this->base_args['select_add'];

		$offset = isset($options['offset']) ? $options['offset'] : 0;
		$order = isset($options['order']) ? $options['order'] : $this->getDefaultOrderBy();
		$data = array();

		$options['conditions'] = $conditions;
		
		if(isset($options['count'])){
			$sql = "SELECT count(*) FROM " . $this->findAllTable($options);
		}else{	
			$countertag =  isset($options['count_cached']) ? "" : "SQL_CALC_FOUND_ROWS" ;
			
			$sql = "SELECT $countertag {$select} FROM " . $this->findAllTable($options);
		}

		//ussage example $options=['joins'=>[['RIGHT','table_name','condition AND condition']]]
		if (isset($options['joins']))
			self::__addJoins($options['joins'], $sql);
		
		if (isset($this->base_args['joins_add']))
			self::__addJoins($this->base_args['joins_add'], $sql);
		
		if ($conditions)
			$sql.= ' WHERE ' . GW_DB::prepare_query($conditions);

		if (isset($options['group_by']) && $options['group_by'])
			$sql.= ' GROUP BY ' . $options['group_by'];

		if ($order && !isset($options['count']))
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

		
		$this->fireEvent('BEFORE_LIST', $options);
		
		$sql = $this->buildSql($options);
		
		if(isset($options['resultcache'])){
						
		
			
			list($entries, $query_info) = GW_Temp_Data::singleton()->rwCallback([
			    'name'=>"CACHED_".$this->findAllTable($options).'_'.md5($sql. serialize($options)),
			    'renew'=>isset($_GET['gw_renew_cache']),
			    'format'=>'serialize',
			    'expires'=>$options['resultcache']], function() use ($options) {
				if(isset($_GET['renew_debug']))
					d::ldump('renew cache');
				unset($options['resultcache']);
				return [$this->findAll(null, $options), $this->lastRequestInfo()];;
			});
			$this->lastRequestInfoPrepared = $query_info;
			
			
			//d::dumpas($entries);
			
			//d::ldump($entries);
			return $entries;
		}
		


		$db = & $this->getDB();

		$nodie = isset($options['soft_error']) && $options['soft_error'] ? true : false;
		
		if (
		    isset($options['assoc_fields']) && $options['assoc_fields'] &&
		    isset($options['return_simple']) && $options['return_simple']
		) {
			$entries = $db->fetch_assoc_exactnum($sql, count($options['assoc_fields']), $nodie);
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
		
		if(isset($options['grouped'])){
			$fields = $options['grouped'];
			//d::dumpas($opts['grouped']);
			
			$entries1 = [];
			
			if(is_array($fields) && count($fields)==2){
				foreach($entries as $idx => $itm)
					$entries1[$itm->get($fields[0])][$itm->get($fields[1])][$idx] = $itm;
			}elseif(is_array($fields) && count($fields)==3){
				$entries1[$itm->get($fields[0])][$itm->get($fields[1])][$itm->get($fields[2])][$idx] = $itm;
			}else{
				$entries1[$itm->get($fields)][$idx] = $itm;
			}
			
			$entries = $entries1;
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
		
		if(!isset($options['select']))
			$options['select'] = implode(', ', array_map(['GW_DB','escapeField'], $fields));
		
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
		
		if(is_numeric($conditions))
			$conditions="a.id = $conditions";

		$r = $this->findAll($conditions, $options);
		
		// jei key butu ne 0,1,2 $r[0] nesuveiks
		return count($r) ? reset($r) : false;
	}
	
	function loadVals($fields = "a.*")
	{
		return $this->find($this->getIdCondition(), Array('select' => $fields, 'return_simple' => 1));
	}

	function load($fields = 'a.*')
	{
		$vals = $this->loadVals($fields);

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
	
	
	function countExt($condition, $params=[])
	{
		$params['conditions']=$condition;
		$params['count']=1;
		$sql = $this->buildSql($params);
		return $this->getDB()->fetch_result($sql);
	}
	
	function maxVal($field, $condition=null)
	{
		$sql = $this->buildSql(['select'=>"max(`$field`)", 'conditions'=>$condition]);
		return $this->getDB()->fetch_result($sql);
		
	}

	function countGrouped($groupby, $condition)
	{
		$counts_sql = "SELECT `$groupby`, count(*) AS cnt FROM `{$this->table}` WHERE $condition GROUP BY `$groupby`";

		return $this->getDb()->fetch_assoc($counts_sql);
	}

	/*
	 * type - 
	 *	all(default) 
	 *	text - text columns, char,varchar, text, TINYTEXT,MEDIUMTEXT longtext,
	 */
	function getColumns($type='all')
	{
		$db = & $this->getDB();
		
		
		
		if($type=='all'){
			$cols = $db->getColumns($this->table);
		}elseif($type=='text'){
			$cols = $db->getColumns($this->table, GW_DB::inConditionStr('DATA_TYPE', ['char','varchar','text','tinytext','mediumtext','longtext']));
		}
		
		if($this->extra_cols)
			$cols= array_merge($cols,$this->extra_cols);
		
		

		return array_flip($cols);
	}
	
	
	
	function getColumnOptions($column)
	{
		return $this->getDB()->getColumnOptions($this->table, $column);
	}

	function getIdCondition($addalias=true)
	{
		$idfield = $this->primary_fields[0];
		$addalias = $addalias ? 'a.' : '';
		return $this->getDB()->prepare_query(Array($addalias.'`' . $idfield . '`=?', $this->get($idfield)));
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
			
			if(! $field_names || $field_names == ['update_time'])
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
		$rez = $db->update($this->table.' AS a', $this->getIdCondition(), $entry);
		
		$this->fireEvent(['AFTER_UPDATE', 'AFTER_SAVE'], $context);
		
		return $rez;
	}

	function updateChanged()
	{
		$field_names = array_keys($this->changed_fields);
		$x=null;

			
		if($field_names)
			$x = $this->update($field_names, ['onlychanged'=>1]);
		
		
		return $x;
	}

	function showChanged()
	{
		$rez = [];
		foreach ($this->changed_fields as $fieldname => $x)
			$rez[$fieldname] = $this->get($fieldname);

		return $rez;
	}
	
	function isChanged()
	{
		return count($this->changed_fields) > 0;
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

		//$ar = $db->delete("`$this->table` AS a", $this->getIdCondition());
		//ubuntu16.04 mariadb does not support alias in delete
		$ar = $db->delete("`$this->table`", $this->getIdCondition(false));

		$this->fireEvent('AFTER_DELETE');
		
		return $ar;
	}

	function fireEvent($event, &$context_data = [])
	{
		if (!is_array($event))
			$this->fireSingeEvent($event, $context_data);
		else
			foreach ($event as $e)
				$this->fireSingeEvent($e, $context_data);
		
	}
		
	function registerObserver($observer)
	{
		$this->observers[] = $observer;
	}
	
	function fireSingeEvent($event, &$context_data = [])
	{
		$this->EventHandler($event, $context_data);
				
		foreach($this->observers as $observer)
			if($observer[0] == 'extension')
				$this->extensions[$observer[1]]->eventHandler($event, $context_data);
			
			
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
					$this->setError($err, $fieldname);
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
		
		
		if(isset($this->extensions['keyval'])){
			$keyvaldump = $this->extensions['keyval']->obj->getAll();
			
			foreach($keyvaldump as $field => $value)
				$list["keyval/$field"] = $value;
		}

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
	function move($where, $conditions = '', $times = 1)
	{
		$db = $this->getDB();
		$id_field = $this->primary_fields[0];
		$id = (int) $this->get($id_field);
		
		$q = "SELECT `$id_field` FROM `$this->table`" . ($conditions ? " WHERE " . GW_DB::prepare_query($conditions) : '') . ' ORDER BY priority';
		

		$rows = $db->fetch_one_column($q, $id_field);
		$oldrows = $rows;



		//dump(Array('where'=>$where,'item_id'=>$id, 'index'=>$index, 'rows'=>$rows));
		for($loop=0;$loop < $times; $loop++){
			
			if (($index = array_search($id, $rows)) === false)
				return true;		
		
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
		}

		//dump(Array('rows'=>$rows));

		$list = Array();
		foreach ($rows as $i => $row)
			$list[] = Array($id_field => $row, 'priority' => $i);

		$db->_multi_insert($this->table, $list, true);
		
		return [$oldrows,$rows];
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
	
	
	//on drag & drop new position given, so push up all
	function updatePositions($oldpos, $newpos)
	{
		$cond = $this->getLimitOrdCondition();
		$oldpos = (int)$oldpos;
		$newpos = (int)$newpos;
		
		if($oldpos == $newpos)
			return true;
		
		$dir = $newpos > $oldpos ? 'down':'up';
		
		$rangelow = min($oldpos, $newpos);
		$rangelhi  = max($oldpos, $newpos);
		
		
		$pos_update = $this->move($dir, $cond, $rangelhi-$rangelow);
		return [$dir, $cond, $rangelhi-$rangelow, json_encode($pos_update)];
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
	
	function savePositionsExact($rows, $conditions)
	{
		$db = $this->getDB();
		return $db->updateMultiple($this->table, $rows, $conditions);
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
				if(is_string($value)){
					return explode(',', trim($value, ','));
				}else{
					return $value;
				}
		}else {
			if (is_array($value))
				return ',' . implode(',', $value) . ',';
		}
	}

	function encodeJSON($fieldname, $value, $revert, $object = false)
	{
		if ($revert) {
			if ($value){
				if(is_array($value) || is_object($value)){
					return $value;
				}
					
				return json_decode($value, !$object);
			}
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
	
	
	/*
	 * Must be unsigned if all 8 positions needed
	 * ALTER TABLE `myThingsTable` CHANGE `flags` `flags` TINYINT(4) UNSIGNED NOT NULL;
	 * example config:
	 * public $flags_conf=['flags'=>[0=>'isAlive',1=>'isHomoSapiens',2=>'isMetal',3=>'isWood',4=>'isLiquid',5=>'isGas',6=>'is..',7=>'is...']];
	 * to Encode larger than tiny int, need to write another function like encodeFlags16 or encodeFlags32
	 */
	function encodeFlags($fieldname, $value, $revert)
	{
		//d::ldump([$fieldname,$this->content_base[$fieldname],$value, $revert ? 'decode':'encode']);
		$len=8;
		
		if($revert){
			if(!is_array($value)){
				$val = [];
				$c = sprintf('%0'.$len.'d', decbin($value));
				
				for($i=0;$i<8;$i++){
	
					if($c[$len-$i-1]==='1' && isset($this->flags_conf[$fieldname][$i]))
						$val[$this->flags_conf[$fieldname][$i]]=1;
				}
				
				return (object)$val;
			}
			
			return $value;
		}else{
			$val='00000000';
						
			foreach($value as $key => $x){
				if($x==1){
					$ind = array_search($key, $this->flags_conf[$fieldname]);
					//d::ldump("search $key, index: $ind, x:$x");
					if($ind!==false)
						$val[$len-$ind-1] = '1';
				}
			}
			//print_r(['before_encode'=>$value, 'prepared'=>$val, "encoded"=>bindec($val)]);			
			return bindec($val);
		}
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
					if(!$this->get('insert_time'))
						$this->set('insert_time', date('Y-m-d H:i:s'));
				break;

			case 'AFTER_LOAD':
				//$this->decodeFields();
				$this->resetChangedFields();
				break;
			case 'AFTER_CONSTRUCT':
				$this->resetChangedFields();
				break;
		}
	}

	function calculateField($name)
	{
		switch($name){
			case "classname":
				return get_class($this);
			break;
		}
	}

	function calculateFieldCache($key)
	{
		$cache =&  $this->cache['calcf'];
		
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
		if(is_array($msg) && GW_Array_Helper::isIndexesNumeric($msg))
			$msg = $msg[0];
				
		$this->errors[$field] = $msg;
		$this->error_codes[$error_code] = ($field ? $field . '::' : '') . $msg;
	}
	
	function prepareSave()
	{
		$this->fireEvent('PREPARE_SAVE');
	}
	
	function multiInsert($list, $replace=true)
	{
		return $this->getDB()->multi_insert($this->table, $list, $replace);
	}
	
	function updateMultiple($conditions, $update_vals, $limit=false)
	{
		return $this->getDB()->update($this->table, $conditions, $update_vals, $limit);
	}
	
	function deleteMultiple($cond)
	{
		return $this->getDB()->delete($this->table, $cond);
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
		
			
		$list = $this->findAll(GW_DB::inCondition('id', $ids), $opts);
		
		
		
		if($opts['preserve_order'] ?? false){
			$list0 = $list;
			$list = [];
			foreach($ids as $id)
				if(isset($list0[$id]))
					$list[$id] = $list0[$id];
				
				
				
			//d::dumpas([$ids,$list]);	
		}
		
		
		
		return $list;
		
	}
	
	function isChangedField($field)
	{
		return isset($this->changed_fields[$field]);
	}
	
	public $_original=[];
	
	function copyOriginal()
	{
		$this->_original = clone $this;
	}
	function getOriginal($name){
		
		if($this->_original && is_object($this->_original))
			return $this->_original->get($name);
		
	}
	
	function secondsPassedAfterCreate($testseconds=false)
	{
		if($this->insert_time=='0000-00-00 00:00:00' || !$this->insert_time)
			return false;
		
		$secs = time()-strtotime($this->insert_time);
		
		if($testseconds){
			return $secs > $testseconds;
		}else{
			return $secs;
		}
		
	}
	
	function createIfNotExists($vals, $execinsert=false)
	{			
		if($obj = $this->find(GW_DB::buidConditions($vals)))
		{
			return $obj;
		}else{
			$obj = $this->createNewObject();
			$obj->setValues($vals);
			
			if($execinsert)
				$obj->insert();
		}
		return $obj;
	}
	
	
	
	
	function findJoinsForFields($fields)
	{
		//composite_map configuration example:
		//'partic2' => ['gw_composite_linked', ['object'=>'GW_Customer','relation_field'=>'participant2']]
		
		/* $fields array example:
    [0] => team_name
    [1] => partic1.name
    [2] => partic2.name
		 * 		 */
		//join example: 
		//['left','gw_nl_subscribers AS part1','a.subscriber_id=part1.id']
		
		$joins = [];
		foreach($fields as $field){
			if(strpos($field,'.')!==false){
				
				list($objname, $fld) = explode('.', $field);
				$objname2  = $this->composite_map[$objname][1]['object'];
				$relationf = $this->composite_map[$objname][1]['relation_field'];
				$obj = $objname2::singleton();
				$joins[$objname] = ['left', "`{$obj->table}` AS `$objname`", "a.$relationf = `$objname`.id"];
			}
		}
		
		return array_values($joins);
	}
	
	function getFieldTypes()
	{
		return $this->getDB()->getColTypes($this->table);
	}
	
	//default recovery // if it is needed to add related data please expand or override this function
	function getRecoveryData()
	{
		$data = [
			'item_data'=>$this->toArray(),
			'item_class'=>get_class($this)
		];
		
		if(isset($this->composite_map)){
			foreach($this->composite_map as $key => $cfg){
				if($cfg[0] == 'gw_related_objecs'){
					
					$subrecovery = [];
					
					
					
					foreach($this->$key as $sub){
						$subrecovery[] = $sub->getRecoveryData();
					}
					
					$data['subitems'][$key] = $subrecovery;
				}
			}
		}
		
		if(isset($this->extensions['keyval'])){
			$data['keyval'] = $this->extensions['keyval']->obj->getAll();
		}
		
		return $data;
	}

	function extensionget($id){
		return $this->extensions[$id];
	}
	
	
	
	function encodeDate($field, $value, $revert)
	{
		if ($revert) {
			if($value == '0000-00-00 00:00:00' || $value == '0000-00-00'){
				return false;
			}else{
				return $value;
			}
		}else {
			return $value;
		}
	}	
	
	
}



