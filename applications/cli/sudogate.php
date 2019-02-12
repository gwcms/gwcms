<?php


chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php'; 



switch($argv[1]){
	case 'pulldb':
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV){
			echo shell_exec("php ".__DIR__."/db_sync_whole.php --exclude=gw_config 2>&1");
		}else{
			echo "Only on dev";
		}
	break;
}
