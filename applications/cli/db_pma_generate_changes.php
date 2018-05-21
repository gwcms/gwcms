<?php



chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php';



$db = new GW_DB();

$sql_dir = GW::s('DIR/ROOT').'/sql/';
$sync_file = "{$sql_dir}last_generate_changes_time";
$time = file_get_contents($sync_file);


$updates_sql = '';
$tables=[];

$uphd = GW_DB::parse_uphd(GW::s('DB/UPHD'));
$dbname = $uphd[3];


foreach($db->fetch_rows("SELECT schema_sql, table_name FROM phpmyadmin.pma__tracking WHERE date_updated > '$time' AND db_name='$dbname'") as $row)
{
	preg_match_all('/\# log (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}).*/i', $row['schema_sql'], $matches);
	
	foreach($matches[1] as $idx => $timem)
	{
		if($timem >= $time)
			break;
	}
	
	$matchstart = $matches[0][$idx];
	
	list(,$updates) = explode($matchstart, $row['schema_sql'],2);
	
	$updates_sql .= $matchstart . $updates."\n\n";
	
	$tables[ $row['table_name'] ]=1;	
}

if(! $tables)
	die("no changes\n");

file_put_contents($sql_dir.date('Y-m-d-H-i-s').' '.implode('-', array_keys($tables)).'.sql', $updates_sql);

file_put_contents($sync_file, date('Y-m-d H:i:s'));


echo $updates_sql;
