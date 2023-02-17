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
	
	case 'recoverdb':
		$backupfolder = $argv[2];
		$cmd =  "php ".__DIR__."/db_sync_whole.php --recoverdb=$backupfolder 2>&1";
		echo $cmd."\n";			
		echo shell_exec($cmd);		
	break;
	
	case 'sync':
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV){
			echo shell_exec("php ".GW::s('DIR/ROOT')."/update.php -web 2>&1");
		}else{
			echo "Only on dev";
		}
	break;	
	case 'writelang':
		$id = $argv[2];
				
		$t = new GW_Timer;
		
		$rdir =& GW::s('DIR');
		$dir =& $rdir['SITE'];

		$rdir['ADMIN']['ROOT']=$rdir['APPLICATIONS'].'admin/';
		$rdir['ADMIN']['MODULES']=$rdir['ADMIN']['ROOT'].'modules/';
		$rdir['ADMIN']['LANG']=$rdir['ADMIN']['ROOT'].'lang/';
		
		

		$rdir['AUTOLOAD'][] = @$dir['LIB'];
		$rdir['AUTOLOAD_RECURSIVE'] = $rdir['ADMIN']['MODULES'];	
		
		
		$user = $argv[3];
		echo "user is $user;\n";
		
		GW_Lang::$ln = 'en';
		GW_Lang::$app = "ADMIN";
		GW_Lang::$langf_dir = GW::s("DIR/APPLICATIONS") . 'ADMIN' . '/lang/';
		
		$lf = new GW_Lang_File($id);
		$lf->load();
		
		if(!$lf->newexists)
			die('temp not exists');
		
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV){
			$lf->writeToOriginal();	
			
		
		}else{
			$projdir = GW::s('PROJECT_NAME');
			$projrepos = GW::s('PROJECT_CODE_REPOS');
			$tmpdir = "/tmp/code_adj_{$projdir}/";
			
			if(!file_exists($tmpdir.'index.php')){
				mkdir($tmpdir);
				passthru("cd $tmpdir && git clone $projrepos .");
			}
			
			
			$dest = str_replace(GW::s('DIR/ROOT'),$tmpdir,$lf->filename);
			
			
			chdir($tmpdir);
			passthru('git pull');
			
			$lf->writeToOriginal($dest);
			
			passthru("git add *.xml");
			passthru("git commit -m 'translations from $user'");
			passthru("git push");
			echo "Speed is {$t->stop()} secs\n";
		}
	break;
}
