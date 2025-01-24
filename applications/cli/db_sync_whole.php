<?php


chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php';

$timer = new GW_Timer;



function parseParams()
{
	$params = array();
	foreach ($GLOBALS['argv'] as $arg)
		if (preg_match('/--(.*?)=(.*)/', $arg, $reg))
			$params[$reg[1]] = $reg[2];
		elseif (preg_match('/-([a-z0-9_-]*)/i', $arg, $reg))
			$params[$reg[1]] = true;

	return $params;
}

function mypassthru($cmd, $hiddenstrings=[])
{
	echo str_replace($hiddenstrings, '*UNDISCLOSED*', $cmd)."\n";
	passthru($cmd);
}
function out($str){
	echo $str."\n";
}


$params = parseParams();



if($params['recoverdb'] ?? false){
	

	list($dbuser, $dbpass, $host, $database, $port) = GW_DB::parse_uphd(GW::s('DB/UPHD'));
	
	initEnviroment(GW_ENV_PROD);
	list($proddbuser, $proddbpass, $prodhost, $proddatabase, $prodport) = GW_DB::parse_uphd(GW::s('DB/UPHD'));
	
	$backupfolder = $params['recoverdb'];
	$localfile = "/mnt/back1/sysbackup/natosltserver/backups/$backupfolder/$proddatabase.gz.enc";
	
	
	out("Recovery file: $localfile");
	
}else{
	
	if($params['env'] ?? false == 'test'){
		initEnviroment(GW_ENV_TEST);
	}else{
		initEnviroment(GW_ENV_PROD);
	}


	list($dbuser, $dbpass, $host, $database, $port) = GW_DB::parse_uphd(GW::s('DB/UPHD'));




	//backup cmd:
	$userhost = GW::s("SSH_USERHOST");
	$pcmd="";$pcmd2="";
	$tmp = explode(':', $userhost);
	if(count($tmp)>1){
		$userhost = $tmp[0];
		$pcmd="-p".$tmp[1];
		$pcmd2="-oPort=".$tmp[1];
	}

	$remotefile="/tmp/$database.gz";
	
	$localfile=$remotefile;
	$unziped="/tmp/$database";

	$extra = "";

	if(isset($params['exclude']))
	{
		foreach(explode(',',$params['exclude']) as $tbl)
		$extra.=" --ignore-table=$database.$tbl ";
	}

	$t = new GW_Timer;
	mypassthru("ssh $userhost $pcmd 'cd /tmp && mysqldump --force --opt --add-drop-database $extra --user=$dbuser -p{$dbpass} $database  | gzip > $remotefile'");
	out("----------Export-speed: {$t->stop()} secs----------");

	$t = new GW_Timer;
	mypassthru($cmd="sftp $pcmd2 $userhost:$remotefile $localfile");
	out("----------Download-speed: {$t->stop()} secs----------");

	//security
	mypassthru("ssh $userhost $pcmd 'unlink $remotefile'");
	
	initEnviroment(GW_ENV_DEV);
	//prod
	list($dbuser, $dbpass, $host, $database, $port) = GW_DB::parse_uphd(GW::s('DB/UPHD'));

}

$t = new GW_Timer;

if(strpos($localfile, '.enc')!==false){
	$encstr=file_get_contents('https://serv133.voro.lt/extrasec/backupencstr.php');
	mypassthru("openssl enc -aes-256-cbc -d -in '$localfile' -k '$encstr' | zcat | mysql -u $dbuser -p{$dbpass} $database", [$encstr,$dbpass]);
}else{
	mypassthru("zcat $localfile | mysql -u $dbuser -p{$dbpass} $database");
}


out("----------Import-speed: {$t->stop()} secs----------");



	
out("----------Sum-speed: {$timer->stop()} secs----------");