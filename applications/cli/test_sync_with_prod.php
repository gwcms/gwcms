<?php

//run this script from test environment
//for proper work you must have permissions to $userhost also verify known_hosts approval

chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php';


$timer = new GW_Timer;

initEnviroment(GW_ENV_PROD);

function mypassthru($cmd)
{
	echo $cmd."\n";
	passthru($cmd);
}
function out($str){
	echo $str."\n";
}

$userhost = GW::s("SSH_USERHOST");
$sourcedir = GW::s("DEPLOY_DIR");

list($dbuser, $dbpass, $host, $database, $port) = GW_DB::parse_uphd(GW::s('DB/UPHD'));

$remotefile="/tmp/$database.gz";
$localfile=$remotefile;
$unziped="/tmp/$database";
$extra = "";


$destinationdir = GW::s('DIR/ROOT');
$t = new GW_Timer;
mypassthru("rsync --exclude='*.log' --exclude='repository/.sys/templates_c/*' -azP $userhost:$sourcedir $destinationdir");
out("----------RSYNC speed: {$t->stop()} secs----------");

$t = new GW_Timer;
mypassthru("ssh $userhost 'cd /tmp && mysqldump --force --opt --add-drop-database $extra --user=$dbuser -p{$dbpass} $database  | gzip > $remotefile'");
out("----------Export-speed: {$t->stop()} secs----------");

$t = new GW_Timer;
mypassthru($cmd="sftp $userhost:$remotefile $localfile");
out("----------Download-speed: {$t->stop()} secs----------");



initEnviroment(GW_ENV_TEST);
//prod
list($dbuser, $dbpass, $host, $database, $port) = GW_DB::parse_uphd(GW::s('DB/UPHD'));


$t = new GW_Timer;
mypassthru("zcat $localfile | mysql -u $dbuser -p{$dbpass} $database");
out("----------Import-speed: {$t->stop()} secs----------");

out("----------Sum-speed: {$timer->stop()} secs----------");
