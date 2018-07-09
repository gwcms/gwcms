<?php


chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php';

initEnviroment(GW_ENV_PROD);

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

$params = parseParams();


list($dbuser, $dbpass, $host, $database, $port) = GW_DB::parse_uphd(GW::s('DB/UPHD'));

function mypassthru($cmd)
{
	echo $cmd."\n";
	passthru($cmd);
}



//backup cmd:
$userhost = GW::s("SSH_USERHOST");


$remotefile="/tmp/$database.gz";
$localfile=$remotefile;
$unziped="/tmp/$database";

$extra = "";

if(isset($params['exclude']))
{
	foreach(explode(',',$params['exclude']) as $tbl)
	$extra.=" --ignore-table=$database.$tbl ";
}

mypassthru("ssh $userhost 'cd /tmp && mysqldump --force --opt --add-drop-database $extra --user=$dbuser -p{$dbpass} $database  | gzip > $remotefile'");
mypassthru($cmd="sftp $userhost:$remotefile $localfile");


initEnviroment(GW_ENV_DEV);
//prod
list($dbuser, $dbpass, $host, $database, $port) = GW_DB::parse_uphd(GW::s('DB/UPHD'));

mypassthru("zcat $localfile | mysql -u $dbuser -p{$dbpass} $database");



	
