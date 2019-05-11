<?php


chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php'; 



switch($argv[1]){
	case 'pulldb':
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV){
			$level = $argv[2];
			$cfg = new GW_Config_FS('system__tools');
			$tables=json_decode($cfg->get("{$level}_sync_ignore_tables"),true);
			
			$cmdargs="";
			if($tables)
				$cmdargs="--exclude=".implode(',', $tables);
			
			$cmd =  "php ".__DIR__."/db_sync_whole.php $cmdargs 2>&1";
			echo $cmd."\n";			
			echo shell_exec($cmd);
		}else{
			echo "Only on dev";
		}
	break;
	
	case 'sync':
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV){
			echo shell_exec("php ".GW::s('DIR/ROOT')."/update.php -web 2>&1");
		}else{
			echo "Only on dev";
		}
	break;	
}
