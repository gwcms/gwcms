<?php



chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php';


$timer = new GW_Timer;

GW::s('DB_BACKUPS','/mnt/back1/sysbackup/natosltserver/backups/*/artistdb.gz');
//atstatyti is 

function getListBackups()
{
	
		return glob(GW::s('DB_BACKUPS'));
}


function mypassthru($cmd)
{
	echo $cmd."\n";
	passthru($cmd);
}
function out($str){
	echo $str."\n";
}





out("----------Sum-speed: {$timer->stop()} secs----------");