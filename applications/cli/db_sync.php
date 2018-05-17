<?php



chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php';

$db_local = new GW_DB();
$db_local->mi_odk_unset_insert=false;

initEnviroment(GW_ENV_PROD);

echo("Atverti kanala:\nssh ".GW::s("SSH_USERHOST")." -L 3308:127.0.0.1:3306 -fN &\n\n");
$sql_dir = GW::s('DIR/ROOT').'/sql/';
$sync_file = "{$sql_dir}last_sync";
$lastsync = file_get_contents($sync_file);




$uphd = GW_DB::parse_uphd(GW::s('DB/UPHD'));

//localhost not ok, uses socks if localhost
$uphd[2] = '127.0.0.1';$uphd[4] = '3308';
$proddb = $uphd[3];

$db = new GW_DB(['UPHD'=>$uphd]);



$rows = $db->fetch_rows($sql="SELECT table_schema,table_name,update_time FROM information_schema.tables WHERE table_schema='$proddb' AND update_time > '$lastsync'");

$ignore_tables=['gw_tasks'=>1, 'gw_user_ip_log'=>1];

print_r([$sql=>$rows]);


//mariadb upgrade 10.2 needed (innodb update_time bug)
//https://websiteforstudents.com/install-upgrade-to-mariadb-10-1-10-2-10-3-latest-on-ubuntu-16-04-lts-server/




foreach($rows as $row)
{
	//$db
	$table = $row['table_name'];
	
	if(isset($ignore_tables[$table])){
		echo "$table ignored\n";
		continue;		
	}
		
	
	
	$columns = $db->fetch_assoc("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='$proddb' AND table_name='$table'");
	
	$cond = [];
	
	
	if(isset($columns['insert_time']) && isset($columns['update_time'])){
		$cond[]="(insert_time >= '$lastsync' OR update_time >= '$lastsync') ";	
	}else{
		/*
		if(isset($columns['insert_time']))
			$cond[]="insert_time>='$lastsync'";

		if(isset($columns['time']))
			$cond[]="time>='$lastsync'";

		if(isset($columns['update_time']))
			$cond[]="update_time>='$lastsync'";
		 * 
		 */		
	}
	
	
	if(!$cond || !isset($columns['id'])){
		echo "$table sync not supported\n";
		continue;
	}
	
	$changes = $db->fetch_rows($sql="SELECT * FROM `$table` WHERE ".implode(' AND ', $cond).' ORDER BY id');
	
	$db_local->multi_insert($table, $changes, true);
	
	print_r([$sql=>$changes]);
		
}

file_put_contents($sync_file, date('Y-m-d H:i:s'));


//jeigu insert time > last_sync tai insert
//jeigu update_time > last_sync tai update





