<?php


chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php';

$timer = new GW_Timer;

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
function out($str){
	echo $str."\n";
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

$t = new GW_Timer;
mypassthru("ssh $userhost 'cd /tmp && mysqldump --force --opt --add-drop-database $extra --user=$dbuser -p{$dbpass} $database  | gzip > $remotefile'");
out("----------Export-speed: {$t->stop()} secs----------");

$t = new GW_Timer;
mypassthru($cmd="sftp $userhost:$remotefile $localfile");
out("----------Download-speed: {$t->stop()} secs----------");


initEnviroment(GW_ENV_DEV);
//prod
list($dbuser, $dbpass, $host, $database, $port) = GW_DB::parse_uphd(GW::s('DB/UPHD'));


$t = new GW_Timer;
mypassthru("zcat $localfile | mysql -u $dbuser -p{$dbpass} $database");
out("----------Import-speed: {$t->stop()} secs----------");



	
out("----------Sum-speed: {$timer->stop()} secs----------");