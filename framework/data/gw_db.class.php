<?php

/**
 * TODO: dont halt execution if it is not debug mode
 * 
 */
class GW_DB
{

	/**
	 *
	 * @var mysqli
	 */
	public $link = false;
	public $uphd = Array();
	public $conf = Array
	    (
	    'logfile' => '',
	    'errshow' => true,
	    'errtags' => true,
	    'lang' => 'lt',
	);
	public $result = false;
	public $last_query;
	public $last_query_time;
	public $query_times;
	public $debug = false;
	public $profiling = false;
	public $speed = [0,0];//1st
	static $datetime_format = 'Y-m-d H:i:s';
	public $error;
	public $error_query;
	
	//if true - collect queries, no real execution
	public $sql_collect=false;
	public $sql_collect_data=[];

	static function parse_uphd($uphd)
	{
		if(is_array($uphd))
			return $uphd;
		
		list($user, $uphd) = explode(':', $uphd, 2);
		list($pass, $uphd) = explode('@', $uphd, 2);
		list($host, $database) = explode('/', $uphd, 2);
		
		$tmp=explode(':',$host,2);
		if(count($tmp)>1)
		{
			$host = $tmp[0];
			$port = $tmp[1];
		}else{
			$port = 3306;
		}
		

		return Array($user, $pass, $host, $database, $port);
	}

	function connect($updh, $newlink = false)
	{
		list($user, $pass, $host, $database, $port) = $updh;
				
		$this->link = new mysqli($host, $user, $pass, $database, $port) or $this->trigger_error();
		if ($database)
			$this->link->select_db($database) or $this->trigger_error();

		//comment next line if mysql v < 4.1
		$this->link->query('SET names "utf8mb4"');

		if (isset($this->conf['INIT_SQLS'])) {
			$list = explode(',', $this->conf['INIT_SQLS']);

			foreach ($list as $sql)
				$this->link->query($sql);
		}

		//$this->test();
	}

	function __construct($conf = Array())
	{		
		$this->conf['logfile'] = GW::s('DIR/LOGS') . 'MySQL.log';
		$conf = array_merge(GW::$settings['DB'], $conf);
		$this->conf = array_merge($this->conf, (array) $conf);
		
		$this->uphd = self::parse_uphd($this->conf['UPHD']);

		$this->connect($this->uphd);
	}

	function trigger_error($cmd = '', $msg = Null, $soft_error = false)
	{
		$this->conf['errshow'] = 1;
		$this->debug = 1;

		if (empty($msg))
			$msg = $this->error($cmd);

		$this->error = $msg;
		$this->error_query = $cmd;

		if (!$soft_error){
			if(GW::$context->app->user->id == 9 && !isset($_GET['syscall']))
			{
				d::dumpas("ERROR: $msg \nCMD: $cmd");
			}else{
				throw new Exception("ERROR: $msg \nCMD: $cmd", E_USER_ERROR);
			}
		}
	}

	function getError()
	{
		return $this->link->error;
	}

	function error($query = '', $nodie = false)
	{
		if (!$this->conf['errshow'])
			return;

		$errorMsg = 'MySQL_ERROR: ' . $this->getError() . ' (' . $this->link->errno . ")";

		if ($this->conf['logfile']) {
			$this->logint($errorMsg);
			$this->logint("MySQL_QUERY: $query");
			$this->logint("BACKTRACE: \n--------------------------------\n" . GW_Debug_Helper::backtrace_soft(1) . "\n--------------------------------\n");
		}

		return $errorMsg;
	}

	function logint($msg, $delim = '')
	{
		if (!$this->conf['logfile'])
			return;
		file_put_contents($this->conf['logfile'], '[' . date('y-m-d H:i:s') . ']' . (($delim) ? '[' . $delim . ']' : '') . ' ' . $msg . "\r\n", FILE_APPEND);
	}

	function query($cmd, $nodie = false)
	{
		$this->error = false;

		$start = microtime(true);
		
		
		if($this->sql_collect){
			$this->sql_collect_data[] = $cmd;
			return true;
		}
		
		

		try {
			$this->result = $this->link->query($cmd);
		} catch (Exception $e) {
			
			$this->trigger_error($cmd, null, $nodie);
			$this->result=false;
			return false;
		}		
		
		
		$this->last_query_time = microtime(true)-$start;
		$this->last_query = $cmd;

		//in case of serious problems
		//$this->logint($cmd.' - '.$_SERVER['REQUEST_URI']);
		
		if ($this->debug){
			$this->query_times[] = Array($cmd, (float) $this->last_query_time, '<a class="dbbacktrace" href="#">BT</a>');
			
			if(isset($_GET['db_backtrace']) && $_GET['db_backtrace'] == count($this->query_times)-1 ){
				d::dumpas("Backtrace for $this->last_query");
			}
		}
		
		if($this->profiling){
			$this->speed[0]++;
			$this->speed[1] += $this->last_query_time;
		}
		
		if(isset($this->backtracing)){
			ob_start();
			debug_print_backtrace();
			$trace = ob_get_clean();
			
			$this->query_times[] = $trace;
		}

		$this->result || $this->trigger_error($cmd, null, $nodie);
		

		return $this->result;
	}

	function fetch_row($cmd = '', $assoc = 1, $nodie = false)
	{
		if (!$cmd)
			return;

		$this->query(self::prepare_query($cmd), $nodie);


		if (!is_object($this->result))
			return false; //avoid error if cmd is DELETE FROM ..

		return $assoc ? $this->result->fetch_assoc() : $this->result->fetch_row();
	}

	function fetch_rows($cmd = '', $assoc = 1, $nodie = false)
	{
		if (!$cmd)
			return;

		$this->query(self::prepare_query($cmd), $nodie);

		//if (!is_resource($this->result))
		//	return Null;

		if (!is_object($this->result))
			return false; //avoid error if cmd is DELETE FROM ..		


		$result = Array();

		if ($assoc == 1)
			while ($row = $this->result->fetch_assoc())
				$result[] = $row;
		elseif ($assoc == 2)
			while ($row = $this->result->fetch_object())
				$result[] = $row;
		else
			while ($row = $this->result->fetch_row())
				$result[] = $row;

		return $result;
	}

	function fetch_rows_key($cmd, $key, $nodie = false)
	{
		if (!$cmd || !$key)
			die('unspecified sql or key');

		$cmd = self::prepare_query($cmd);

		$this->query($cmd, $nodie);

		if (!$this->result)
			return Null;

		$result = Array();
		
		if(is_array($key))
		{
			$lastidx = array_pop($key);
			
			while ($row = $this->result->fetch_assoc()){
								
				$idx = [];
				foreach($key as $fld)
					$idx[] = $row[$fld];

				GW_Array_Helper::getPointer2XlevelAssocArr($result, $idx, $row[$lastidx]);
			}
		}else{
			while ($row = $this->result->fetch_assoc())
				$result[$row[$key]] = $row;			
		}
		




		return $result;
	}

	/**
	 * return associative array
	 * associative array key - $fields[0]
	 * associative array values - $fields[1]
	 */
	function fetch_assoc($cmd, $nodie = false)
	{
		$cmd = self::prepare_query($cmd);

		$this->query($cmd, $nodie);

		$result = [];

		

		switch($tmp=mysqli_num_fields($this->result)){
			case 1:
				while ($row = $this->result->fetch_array())
					$result[$row[0]] = 1;
			case 2:
				while ($row = $this->result->fetch_array())
					$result[$row[0]] = $row[1];
			break;

			case 3:
				while ($row = $this->result->fetch_array())
					$result[$row[0]][$row[1]] = $row[2];
			break;
			case 4:
				while ($row = $this->result->fetch_array())
					$result[$row[0]][$row[1]][$row[2]] = $row[3];
			break;			
			default:
				$this->trigger_error($cmd, "FETCH ASSOC $tmp - NUM FIELDS NOT SUPPORTED");
			break;
			
		}

		return $result;
	}
	
	/**
	 * return associative array
	 * associative array key - $fields[0]
	 * associative array values - $fields[1]
	 */
	function fetch_assoc_exactnum($cmd, $num, $nodie = false)
	{
		$cmd = self::prepare_query($cmd);

		$this->query($cmd, $nodie);

		$result = [];

		

		switch($num){
			case 1:
				while ($row = $this->result->fetch_array())
					$result[$row[0]] = 1;
			case 2:
				while ($row = $this->result->fetch_array())
					$result[$row[0]] = $row[1];
			break;

			case 3:
				while ($row = $this->result->fetch_array())
					$result[$row[0]][$row[1]] = $row[2];
			break;
			case 4:
				while ($row = $this->result->fetch_array())
					$result[$row[0]][$row[1]][$row[2]] = $row[3];
			break;			
			default:
				$this->trigger_error($cmd, "FETCH ASSOC $num - NUM FIELDS NOT SUPPORTED");
			break;
			
		}

		return $result;
	}	

	function fetch_one_column($cmd, $nodie = false)
	{
		if (!$cmd)
			die('unspecified sql');

		$cmd = self::prepare_query($cmd);

		$this->query($cmd, $nodie);

		$result = Array();
		
		if($this->result)
			while ($row = $this->result->fetch_array())
				$result[] = $row[0];

		return $result;
	}

	function fetch_result($cmd = '', $nodie = false)
	{
		$cmd = self::prepare_query($cmd);

		if ($cmd)
			$this->query($cmd, $nodie);

		if(!$this->result)
			return null;
		
		$row = $this->result->fetch_array();

		return isset($row[0]) ? $row[0] : Null;
	}

	static function sql_prepare_value($val)
	{
		//( || is_object($val) ? json_encode($val) :
		return "'" . addslashes(is_array($val) ? json_encode($val) : $val). "'";
	}
	
	function insert($table, $entry, $nodie = false, $replaceinto = false)
	{
		$names = [];
		$values = [];
		foreach ($entry as $elemRak => $vert) {
			$names[] = '`' . $elemRak . '`';
			$values[] = self::sql_prepare_value($vert) ;
		}

		$query = ($replaceinto ? "REPLACE" : "INSERT") . " INTO $table (" . implode(',', $names) . ") VALUES (" . implode(',', $values) . ")";
		$this->query($query, $nodie);
		return $this->link->affected_rows;
	}

	function save($table, $entry, $nodie = false)
	{		
		$names = [];
		$values = [];
		foreach ($entry as $elemRak => $vert) {
			$names[] = '`' . $elemRak . '`';
			
			//if(is_object($vert))
			//	d::dumpas($vert);
			
			$values[] = self::sql_prepare_value($vert) ;
		}
		$query = "INSERT INTO $table (" . implode(',', $names) . ") VALUES (" . implode(',', $values) . ") ON DUPLICATE KEY UPDATE ";

		foreach ($entry as $elemRak => $vert)
			if (!is_numeric($elemRak))
				$query.='`' . $elemRak . "`=" .  self::sql_prepare_value($vert) . ", ";

		$query = substr($query, 0, -2);
		$this->query($query, $nodie);
		return $this->link->affected_rows;
	}

	public $mi_odk_unset_insert=true;
	//required that all entries have set full keys
	function _multi_insert($table, $entries, $replace = false, $nodie = false)
	{
		$keys = Array();
		$keys1 = Array();

		reset($entries); //to get first element from array
		foreach (current($entries) as $key => $entry) {
			$keys[] = $key;
			$keys1[$key] = '`' . $key . '`';
		}
		
		$query = "INSERT INTO $table (" . implode(',', $keys1) . ") VALUES ";

		foreach ($entries as $entry) {
			$values = Array();

			foreach ($keys as $key)
				$values[] = "'" . addslashes($entry[$key]??'') . "'";

			$query.= "(" . implode(',', $values) . "),\n";
		}

		$query = substr($query, 0, -2);

		if ($replace) {
			$query .= ' ON DUPLICATE KEY UPDATE ';
			
			if($this->mi_odk_unset_insert)
				unset($keys1['insert_time']);
			
			foreach ($keys1 as $key)
				$query .= $key . '=VALUES(' . $key . '), ';

			$query = substr($query, 0, -2);
		} 
		
		

		$this->query($query, $nodie);

		return $this->link->affected_rows;
	}

	function multi_insert($table, $entries, $replace = false, $nodie = false)
	{
		$peace = 100;
		$afRows = 0;
		$loops = ceil(count($entries) / $peace);

		for ($i = 0; $i < $loops; $i++) {
			$slice = array_slice($entries, $i * $peace, $peace);
			$afRows+=$this->_multi_insert($table, $slice, $replace, $nodie);
		}
		return $afRows;
	}

	static function __update_set($entry)
	{
		$parts = [];

		foreach ($entry as $elemRak => $vert)
			if (!is_numeric($elemRak))
				$parts[] = '`' . $elemRak . "`=" . (self::sql_prepare_value($vert)) ;

		return implode(', ', $parts);
	}

	function update($table, $filter, $entry, $nodie = false, $limit=false)
	{
		$filter = self::prepare_query($filter);


		if (!is_array($entry))
			$this->fatalError('update: 3d argument must be assoc array');

		//implementation of passing back fetched array (mysqli_fetch_array)
		//do not pass numeric field name (if(!is_numeric($elemRak)))

		
		$limitSQL = $limit===false ? "" : " LIMIT $limit";

		$query = "UPDATE $table SET " . $this->__update_set($entry) . " WHERE $filter $limitSQL;";

		if (isset($GLOBALS['show_update_sql']))
			dump($query);
		$this->query($query, $nodie);

		return $this->link->affected_rows;
	}

	function delete($table, $filter, $nodie = false)
	{
		$filter = self::prepare_query($filter);

		if (!$table)
			$this->fatalError('Invalid table name');

		if (!$filter)
			$this->fatalError('filter is required, use query("DELETE FROM tablename")');

		$this->query("DELETE FROM $table WHERE $filter", $nodie);

		return $this->link->affected_rows;
	}

	function count($tbl, $filter = '', $nodie = false)
	{
		$filter = self::prepare_query($filter);

		if (!$tbl)
			$this->fatalError('Invalid table name');

		if ($filter)
			$filter = 'WHERE ' . $filter;

		$Q = "SELECT COUNT(*) FROM `$tbl` $filter";

		return $this->fetch_result($Q, 0, $nodie);
	}

	function increase($table, $where, $field, $x = 1, $nodie = false)
	{
		$query = "UPDATE $table SET $field = $field + $x WHERE $where";

		$this->result = $this->query($query, $nodie);

		return $this->affected();
	}		

	function affected()
	{
		return $this->link->affected_rows;
	}

	function fatalError($msg)
	{
		$this->trigger_error('', $msg);
	}

	function insert_id()
	{
		return $this->link->insert_id;
	}

	function num_rows()
	{
		return $this->result->num_rows;
	}

	function getSQLResultsCount($sql = null)
	{
		if (is_null($sql))
			$sql = $this->last_query;

		$sql = preg_replace("/LIMIT .+$/Uim", "", $sql);
		$sql = preg_replace("/ORDER BY .+$/Uim", "", $sql);
		$sql = preg_replace("/^SELECT[ \t].*?[ \t]FROM[ \t]/Uims", "SELECT 1 FROM ", $sql, 1);

		$this->query($sql);

		return $this->num_rows();
	}

	function close()
	{
		$this->link->close();
	}

	function ping()
	{
		return $this->link->ping();
	}

	function check_connection()
	{
		if (!$this->ping()) {
			$this->close();
			$this->connect();
			return 1;
		}
	}

	static function prepare_query($params)
	{
		if (!$params || !is_array($params))
			return $params;

		$query = array_shift($params);

		$ho = new db_query_prep_helper($params);

		return preg_replace_callback('/(\?)/', Array(&$ho, 'replace'), $query);
	}

	static function buidConditions($conds, $operator = 'AND')
	{
		$conditions = [];
		foreach ($conds as $field => $val) {
			$conditions[0][] = self::escapeField($field)."=?";
			$conditions[] = $val;
		}
		if (isset($conditions[0]))
			$conditions[0] = implode(' ' . $operator . ' ', $conditions[0]);

		return $conditions;
	}

	static function inCondition($fieldname, $ids)
	{
		if (!$ids)
			return '1=0';

		foreach($ids as $i => $id)
			$ids[$i] = (int)$id;

		if (strpos($fieldname, '`') === false)
			$fieldname = '`' . $fieldname . '`';

		return $fieldname . ' IN (' . implode(',', $ids) . ')';
	}

	static function inConditionStr($fieldname, $ids)
	{
		if (!$ids)
			return '1=0';

		foreach ($ids as $i => $x)
			$ids[$i] = self::escape($x);


		if (strpos($fieldname, '`') === false)
			$fieldname = '`' . $fieldname . '`';

		return $fieldname . ' IN ("' . implode('","', $ids) . '")';
	}
	
	/**
	 * similar to buidConditions its just allows $condition1 or $condition2 to be empty, and conditions simple strings
	 */
	static function mergeConditions($condition1, $condition2, $operator="AND")
	{
		return ($condition1 ? "($condition1)":"").($condition1 && $condition2 ? " $operator ":"").($condition2 ? "($condition2)":"");
	}

	static function escape($mixed)
	{
		if(is_array($mixed)){
			foreach($mixed as $indx => $val)
				$mixed[$indx] = self::escape($val);
			
			return $mixed;
		}
		
		return addslashes($mixed);
	}
	
	static function escapeField($str, $default_tbl=false)
	{
		if(strpos($str,'.')!==false)
			return $str;
		
		
		if(strpos($str,'`')!==false)
			return $str;
		
		$str = "`".$str."`";
		
		if($default_tbl)
			$str = "$default_tbl.".$str;
		
		return $str;
	}

	static function timeString($time = false)
	{
		return date(self::$datetime_format, $time ? $time : time());
	}

	function getLongQueries($time = 1, $clean = 1)
	{
		$list = Array();

		foreach ((array) $this->query_times as $query)
			if ($query[1] > $time)
				$list[] = $query;

		if ($clean)
			$this->query_times = Array();

		return $list;
	}

	function test()
	{
		$this->query("CREATE TABLE IF NOT EXISTS `db_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NULL,
  `update_time` datetime NULL,
  `insert_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

		$this->insert('db_test', ['title' => ($test = "inserttest" . rand(0, 100000))]);
		$insert_id = $this->insert_id();

		$r = $this->fetch_rows($sql1 = "SELECT * FROM db_test WHERE title LIKE '$test'");
		$r1 = $this->fetch_result($sql = "SELECT title FROM db_test WHERE title LIKE '$test'");
		$r2 = $this->fetch_row($sql1);

		$tests['insert_id'] = $insert_id == $r[0]['id'];
		//$tests['insert_id_d'] = [$insert_id, $r[0]['id']];
		$tests['fetch_rows'] = $r[0]['title'] == $test;
		$tests['fetch_result'] = $r1 == $test;
		$tests['fetch_row'] = $r2['title'] == $test;

		//$this->query("DROP TABLE `db_test`");

		d::dumpas($tests);
	}
	
	function getColumnOptions($table, $column)
	{
		$row = $this->fetch_row("SHOW COLUMNS FROM `$table` LIKE '$column'");
		$type = $row['Type'];
		preg_match('/enum\((.*)\)$/', $type, $matches);
		
		$string = $matches[1];
		if ($string[0] == "'") $string = substr($string,1);
		if ($string[strlen($string)-1] == "'") $string = substr($string,0,strlen($string)-1);
		
		$opts = explode("','", $string);
				
		return $opts;
	}
	
	static $colOptsCache=[];
	
	function getColumnOptionsCached($table, $column)
	{
		if(!isset(self::$colOptsCache[$table][$column])){
			$arr = $this->getColumnOptions($table, $column);
			$arrmod = [];
			foreach($arr as $e)
				$arrmod[$e]=$e;
			
			self::$colOptsCache[$table][$column] = $arrmod;
		}else{
			return self::$colOptsCache[$table][$column];
		}
	}
	
	
	function writeColumnOptions($table, $column, $opts)
	{
		foreach($opts as $idx => $o)
			$opts[$idx] = self::escape($o);
		
		$cmd="ALTER TABLE `$table` MODIFY COLUMN `$column` enum('".implode("','", $opts)."') NOT NULL;";
		
		
		
		$this->query($cmd);
		
		unset(self::$colOptsCache[$table][$column]);
	}
	
	function testExistEnumOption($table, $column, $val){
		
		$this->getColumnOptionsCached($table, $column);
		
		//d::dumpas(self::$colOptsCache[$table][$column]);
		
		if(!isset(self::$colOptsCache[$table][$column][$val])){
			$opts = self::$colOptsCache[$table][$column];
			$opts[$val]=$val;
			$this->writeColumnOptions($table, $column, $opts);
		}
		
	}
	
	
	/**
	 * rows must be indexed array - and index is id
	 */
	function updateMultiple($table, $rows, $conditions='1=1', $id_field='id')
	{		
		$fields = [];
		foreach($rows as $idx => $record)
			foreach($record as $field => $value)
				$fields[$field]=1;
			
		$sql = "UPDATE `{$table}` SET ";
		
		foreach($fields as $field => $x)
		{
			$sql.=" ".self::escapeField($field)." = CASE ".self::escapeField($id_field)."\n";
			
			foreach($rows as $id => $record){
				if(isset($record[$field]))
					$sql.=" WHEN ".self::escape($id).' THEN '. self::escape($record[$field])."\n";
			}
			$sql.="END,";
		}
		
		$sql = substr($sql, 0, -1); //last comma
		
		$sql.= " WHERE ". self::prepare_query($conditions). ($conditions ? ' AND ':'').self::inCondition($id_field, array_keys($rows));
			
		$this->query($sql, true);

		return $this->link->affected_rows;
	}
	
	
	function getColumns($tablename, $extraconds="")
	{		
		$conds = self::prepare_query(self::buidConditions(['table_name'=>$tablename,'table_schema'=>$this->uphd[3]]));
		
		if($extraconds)
			$conds = GW_DB::mergeConditions ($conds, $extraconds);
		
		return $this->fetch_one_column("SELECT column_name FROM information_schema.columns WHERE ".$conds);
	}
	
	function getColTypes($table)
	{
		return $this->fetch_assoc(["SELECT COLUMN_NAME,DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE table_name = ?", $table]);
	}
	
	
	function tableExists($tbl)
	{
		return $this->fetch_result("DESCRIBE `$tbl`", true)===Null;
	}
	
	function execSqls($sqls)
	{
		$sqls = explode(';', $sqls);
		$results=[];
		
		foreach($sqls as $sql)
		{
			$sql = trim($sql);
			if(!$sql)continue;
			
			$t = new GW_Timer;
			$res = $this->query($sql, true, true);
			$result['affected'] = $db->affected();
			
			if (!is_object($this->result))
				$result['err'] = $this->getError();
			
			$result['took'] = $t->stop();

			$results[] = $result;
		}	
		
		
		return $results;
	}

	function truncate($table)
	{
		$this->query("TRUNCATE TABLE `$table`");
	}
	
	function aesCrypt($str, $key, $revert=false)
	{
		if($revert){
			return GW::db()->fetch_result(["SELECT AES_DECRYPT(FROM_BASE64(?),SHA2('$key',512))", $str]);
		}else{
			return $this->fetch_result(["SELECT TO_BASE64(AES_ENCRYPT(?,SHA2('$key',512)))", $str]);
		}
	}
}

class db_query_prep_helper
{

	var $data;

	function __construct(&$data)
	{
		$this->data = & $data;
	}

	function replace($arg)
	{
		$curr = array_shift($this->data);
		return is_numeric($curr) ? $curr : '"' . addslashes($curr) . '"';
	}
}
