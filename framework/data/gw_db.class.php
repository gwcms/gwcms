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
	static $datetime_format = 'Y-m-d H:i:s';
	public $error;
	public $error_query;

	function parse_uphd($uphd)
	{
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
		$this->link->query('SET names "UTF8"');

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
		$this->uphd = is_array(GW::$settings['DB']['UPHD']) ? GW::$settings['DB']['UPHD'] : self::parse_uphd(GW::$settings['DB']['UPHD']);

		$conf = array_merge(GW::$settings['DB'], $conf);
		$this->conf = array_merge($this->conf, (array) $conf);

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

		if (!$soft_error)
			throw new Exception("ERROR: $msg \nCMD: $cmd", E_USER_ERROR);
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

		$tmp = new GW_Timer();
		$this->result = $this->link->query($cmd);
		$this->last_query_time = $tmp->stop(6);
		$this->last_query = $cmd;

		if ($this->debug)
			$this->query_times[] = Array($cmd, (float) $this->last_query_time);

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

	function fetch_one_column($cmd, $nodie = false)
	{
		if (!$cmd)
			die('unspecified sql');

		$cmd = self::prepare_query($cmd);

		$this->query($cmd, $nodie);

		$result = Array();

		while ($row = $this->result->fetch_array())
			$result[] = $row[0];

		return $result;
	}

	function fetch_result($cmd = '', $nodie = false)
	{
		$cmd = self::prepare_query($cmd);

		if ($cmd)
			$this->query($cmd, $nodie);

		$row = $this->result->fetch_array();

		return isset($row[0]) ? $row[0] : Null;
	}

	function insert($table, $entry, $nodie = false, $replaceinto = false)
	{
		$names = '';
		$values = '';
		foreach ($entry as $elemRak => $vert) {
			$names[] = '`' . $elemRak . '`';
			$values[] = "'" . addslashes($vert) . "'";
		}

		$query = ($replaceinto ? "REPLACE" : "INSERT") . " INTO $table (" . implode($names, ',') . ") VALUES (" . implode($values, ',') . ")";
		$this->query($query, $nodie);
		return $this->link->affected_rows;
	}

	function save($table, $entry, $nodie = false)
	{		
		$names = '';
		$values = '';
		foreach ($entry as $elemRak => $vert) {
			$names[] = '`' . $elemRak . '`';
			
			//if(is_object($vert))
			//	d::dumpas($vert);
			
			$values[] = "'" . addslashes($vert) . "'";
		}
		$query = "INSERT INTO $table (" . implode($names, ',') . ") VALUES (" . implode($values, ',') . ") ON DUPLICATE KEY UPDATE ";

		foreach ($entry as $elemRak => $vert)
			if (!is_numeric($elemRak))
				$query.='`' . $elemRak . "`=\"" . addslashes($vert) . "\", ";

		$query = substr($query, 0, -2);
		$this->query($query, $nodie);
		return $this->link->affected_rows;
	}

	//required that all entries have set full keys
	function _multi_insert($table, $entries, $replace = false, $nodie = false)
	{
		$keys = Array();
		$keys1 = Array();

		reset($entries); //to get first element from array
		foreach (current($entries) as $key => $entry) {
			$keys[] = $key;
			$keys1[] = '`' . $key . '`';
		}
		
		$query = "INSERT INTO $table (" . implode($keys1, ',') . ") VALUES ";

		foreach ($entries as $entry) {
			$values = Array();

			foreach ($keys as $key)
				$values[] = "'" . addslashes($entry[$key]) . "'";

			$query.= "(" . implode($values, ',') . "),\n";
		}

		$query = substr($query, 0, -2);

		if ($replace) {
			$query .= ' ON DUPLICATE KEY UPDATE ';
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

	function __update_set($entry)
	{
		$parts = [];

		foreach ($entry as $elemRak => $vert)
			if (!is_numeric($elemRak))
				$parts[] = '`' . $elemRak . "`=\"" . addslashes($vert) . "\"";

		return implode(', ', $parts);
	}

	function update($table, $filter, $entry, $nodie = false)
	{
		$filter = self::prepare_query($filter);


		if (!is_array($entry))
			$this->fatalError('update: 3d argument must be assoc array');

		//implementation of passing back fetched array (mysqli_fetch_array)
		//do not pass numeric field name (if(!is_numeric($elemRak)))



		$query = "UPDATE $table SET " . $this->__update_set($entry) . " WHERE $filter;";

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
			$conditions[0][] = "`$field`=?";
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
		
		$opts = explode("','", trim($matches[1],"'"));
				
		return $opts;
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
