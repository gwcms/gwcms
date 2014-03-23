<?php

class GW_DB
{
	var $link=false;
	var $uphd=Array();
	var $conf=Array
	(
		'logfile' => '',
		'errshow' => true,
		'errtags' => true,
		'lang'=>'lt',
	);

	var $result=false;
	var $last_query;
	var $last_query_time;
	var $query_times;
	var $debug=false;
	static $datetime_format='Y-m-d H:i:s';


	function parse_uphd($uphd)
	{
		list($user,$uphd)=explode(':',$uphd,2);
		list($pass,$uphd)=explode('@',$uphd,2);
		list($host,$database)=explode('/',$uphd,2);

		return Array($user,$pass,$host,$database);
	}


	function connect($updh,$newlink=false)
	{
		list($user,$pass,$host,$database)=$updh;

		$this->link=@mysql_connect($host, $user, $pass, $newlink) or $this->trigger_error();
		if($database) mysql_select_db($database) or $this->trigger_error();

		//comment next line if mysql v < 4.1
		$this->query('SET names "UTF8"');		
	}


	function __construct($conf=Array())
	{
		$this->conf['logfile']=GW::$dir['LOGS'].'MySQL.log';
		$this->uphd = self::parse_uphd(GW::$static_conf['DB']['UPHD']);

		$conf=array_merge(GW::$static_conf['DB'], $conf);
		$this->conf=array_merge($this->conf, (array)$conf);

		$this->connect($this->uphd);
	}


	function trigger_error($cmd='',$msg=Null, $type=E_USER_ERROR)
	{
		if($this->debug)
			dump(GW_Debug_Helper::backtrace_soft());

		if(empty($msg))
			$msg = $this->error($cmd);

		trigger_error($msg,E_USER_ERROR);
	}
	
	function getError()
	{
		return mysql_error();
	}

	function error($query='',$nodie=false)
	{	
		if(!$this->conf['errshow'])return;

		$errorMsg='MySQL_ERROR: '.$this->getError().' ('.mysql_errno().")";

		if($this->conf['logfile'])
		{
			$this->logint($errorMsg);
			$this->logint("MySQL_QUERY: $query");
			$this->logint("BACKTRACE: \n--------------------------------\n".GW_Debug_Helper::backtrace_soft(1)."\n--------------------------------\n");
		}

		return $errorMsg;
	}

	function logint($msg,$delim='')
	{
		if(!$this->conf['logfile'])return;
		file_put_contents($this->conf['logfile'],'['.date('y-m-d H:i:s').']'.(($delim)?'['.$delim.']':'').' '.$msg."\r\n",FILE_APPEND);
	}

	function query($cmd, $nodie=false)
	{
		$tmp = new GW_Timer();
		$this->result=mysql_query($cmd, $this->link);
		$this->last_query_time=$tmp->stop(6);
		$this->last_query=$cmd;
		
		if($this->debug)
			$this->query_times[]=Array($cmd, (float)$this->last_query_time);
		
		$this->result || $nodie || $this->trigger_error($cmd);
		
		return $this->result;
	}

	function fetch_row($cmd='',$assoc=1,$nodie=false)
	{
		if(!$cmd)return;
	
		$this->query(self::prepare_query($cmd), $nodie);

		return $assoc?mysql_fetch_assoc($this->result):mysql_fetch_row($this->result);
	}

	function fetch_rows($cmd='',$assoc=1,$nodie=false)
	{
		if(!$cmd)return;

		$this->query(self::prepare_query($cmd), $nodie);
		$result=Array();
		
		if(!is_resource($this->result))
			return false;

		if($assoc)
			while($row=mysql_fetch_assoc($this->result))$result[]=$row;
		else
			while($row=mysql_fetch_row($this->result))$result[]=$row;

		return $result;
	}

	function fetch_rows_key($cmd,$key,$nodie=false)
	{
		if(!$cmd || !$key)die('unspecified sql or key');

		$cmd=self::prepare_query($cmd);

		$this->query($cmd,$nodie);

		$result=Array();

		while($row=mysql_fetch_assoc($this->result))
			$result[$row[$key]]=$row;

		return $result;
	}
	
	
	/**
	 * return associative array
	 * associative array key - $fields[0]
	 * associative array values - $fields[1]
	 */
	
	function fetch_assoc($cmd, $nodie=false)
	{
		$cmd=self::prepare_query($cmd);

		$this->query($cmd,$nodie);

		$result=Array();

		while($row=mysql_fetch_array($this->result))
			$result[$row[0]]=$row[1];

		return $result;		
	}

	function fetch_one_column($cmd, $nodie=false)
	{
		if(!$cmd)die('unspecified sql');

		$cmd=self::prepare_query($cmd);

		$this->query($cmd,$nodie);

		$result=Array();

		while($row=mysql_fetch_array($this->result))
			$result[]=$row[0];

		return $result;			
	}

	function fetch_result($cmd='',$index=0,$nodie=false)
	{
		$cmd=self::prepare_query($cmd);

		if($cmd)$this->query($cmd,$nodie);
		return @mysql_result($this->result,$index);
	}


	function insert($table,$entry,$nodie=false)
	{
		$names='';$values='';
		foreach ($entry as $elemRak => $vert) {$names[]='`'.$elemRak.'`';$values[]="'".addslashes($vert)."'";}
		$query="INSERT INTO $table (". implode($names,',').") VALUES (".implode($values,',').")";
		$this->query($query,$nodie);
		return mysql_affected_rows($this->link);
	}

	function save($table,$entry,$nodie=false)
	{
		$names='';$values='';
		foreach ($entry as $elemRak => $vert) {$names[]='`'.$elemRak.'`';$values[]="'".addslashes($vert)."'";}
		$query="INSERT INTO $table (". implode($names,',').") VALUES (".implode($values,',').") ON DUPLICATE KEY UPDATE ";
				
		foreach ($entry as $elemRak => $vert)
			if(!is_numeric($elemRak))$query.='`'.$elemRak."`=\"".addslashes($vert)."\", ";

		$query=substr($query,0,-2);
		$this->query($query,$nodie);
		return mysql_affected_rows($this->link);
	}

	//required that all entries have set full keys
	function _multi_insert($table,$entries,$replace=false,$nodie=false)
	{
		$keys =Array();
		$keys1=Array();
		
		reset($entries);//to get first element from array
		foreach(current($entries) as $key=> $entry)
		{
			$keys[]=$key;
			$keys1[] = '`'.$key.'`';
		}
		
		$query="INSERT INTO $table (". implode($keys1,',').") VALUES ";

		foreach($entries as $entry)
		{
			$values=Array();
			
			foreach($keys as $key) 
				$values[]="'".addslashes($entry[$key])."'";
			
			$query.= "(".implode($values,',')."),\n";
		}
		
		$query = substr($query,0,-2);
		
		if($replace)
		{
			$query .= ' ON DUPLICATE KEY UPDATE ';
			foreach($keys1 as $key)
				$query .= $key.'=VALUES('.$key.'), ';
			
			$query = substr($query,0,-2);	
		}
		
		$this->query($query,$nodie);

		return mysql_affected_rows($this->link);
	}

	function multi_insert($table,$entries,$replace=false,$nodie=false)
	{
		$peace=100;
		$afRows=0;
		$loops=ceil(count($entries)/$peace);

		for($i=0;$i<$loops;$i++){
			$slice = array_slice($entries,$i*$peace,$peace);
			$afRows+=$this->_multi_insert($table,$slice,$replace,$nodie);
		}
		return $afRows;
	}


	function update($table,$filter,$entry,$nodie=false)
	{
		$filter=self::prepare_query($filter);

		$query="UPDATE $table SET ";
		if(!is_array($entry))$this->fatalError('update: 3d argument must be assoc array');

		//implementation of passing back fetched array (mysql_fetch_array)
		//do not pass numeric field name (if(!is_numeric($elemRak)))

		foreach ($entry as $elemRak => $vert)
			if(!is_numeric($elemRak))
				$query.='`'.$elemRak."`=\"".addslashes($vert)."\", ";
				
		$query=substr($query,0,-2)." WHERE $filter;";

		if(isset($GLOBALS['show_update_sql']))
			dump($query);
		$this->query($query,$nodie);
		
		return mysql_affected_rows($this->link);
	}

	function delete($table,$filter,$nodie=false)
	{
		$filter=self::prepare_query($filter);
		
		if(!$table)
			$this->fatalError('Invalid table name');
			
		if(!$filter)
			$this->fatalError('filter is required, use query("DELETE FROM tablename")');
			
		$this->query("DELETE FROM $table WHERE $filter",$nodie);
		
		return mysql_affected_rows($this->link);
	}
	

	function count($tbl,$filter='',$nodie=false)
	{
		$filter=self::prepare_query($filter);

		if(!$tbl)
			$this->fatalError('Invalid table name');
			
		if($filter)
			$filter='WHERE '.$filter;
			
		$Q="SELECT COUNT(*) FROM `$tbl` $filter";
		
		return $this->fetch_result($Q,0,$nodie);
	}

	function increase($table,$where,$field,$x=1,$nodie=false)
	{
		$query="UPDATE $table SET $field = $field + $x WHERE $where";
		
		$this->result=$this->query($query,$nodie);
		
		return $this->affected();
	}

	function affected()
	{
		return mysql_affected_rows($this->link);
	}

	function fatalError($msg)
	{
		$this->trigger_error('',$msg);
	}

	function insert_id()
	{
		return mysql_insert_id($this->link);
	}

	function num_rows()
	{
		return mysql_num_rows($this->result);
	}
	
	function getSQLResultsCount($sql = null)
	{
		if (is_null($sql))
			$sql = $this->last_query;
			
		$sql = preg_replace("/LIMIT .+$/Uim", "", $sql);
		$sql = preg_replace("/ORDER BY .+$/Uim", "", $sql);
		$sql = preg_replace("/^SELECT[ \t].*?[ \t]FROM[ \t]/Uims", "SELECT 1 FROM ", $sql,1);
		
		$this->query($sql);
		
		return $this->num_rows();
	}

	function close()
	{
		mysql_close($this->link);
	}

	function ping()
	{
		return mysql_ping($this->link);
	}

	function check_connection()
	{
		if(!$this->ping())
		{
			$this->close();
			$this->connect();
			return 1;
		}
	}

	static function prepare_query($params)
	{
		if(!$params || !is_array($params)) return $params;

		$query = array_shift($params);

		$ho = new db_query_prep_helper($params);

		return preg_replace_callback('/(\?)/',Array(&$ho,'replace'), $query);
	}
	
	static function escape($mixed)
	{
		return addslashes($mixed);
	}
	
	static function timeString($time=false)
	{
		return date(self::$datetime_format, $time ? $time : time());
	}
	
	function getLongQueries($time=1, $clean=1)
	{
		$list = Array();
		
		foreach((array)$this->query_times as $query)
			if($query[1] > $time)
				$list[]=$query;

		if($clean)
			$this->query_times = Array();
		
		return $list;
	}	
}


class db_query_prep_helper
{
	var $data;

	function __construct(&$data)
	{
		$this->data =& $data;
	}

	function replace($arg)
	{
		$curr = array_shift($this->data);
		return is_numeric($curr) ? $curr : '"'.addslashes($curr).'"';
	}
}

?>
