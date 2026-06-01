<?php


if(isset($_SERVER['HTTP_HOST'])){
	
	
	if(isset($_GET['filediff'])){
		$sync = new GW_CMS_Sync();
		$sync->params['proj'] = $_GET['proj'];
		$sync->setDirection($_GET['dir']);
			
		$file1 = $sync->destDir.$_GET['filediff'];
		$file2 = $sync->sourceDir.$_GET['filediff'];
		$f1_proj = basename($sync->destDir);	
		$f2_proj = basename($sync->sourceDir);	
		
		$file1 = file_get_contents($file1);
		$file2 = file_get_contents($file2);
		
		
		//kai is gwcms i projekta siunciami updeitai istestuota ir ok spalvina
		if($_GET['dir']=='1'){
			$tmp = $file1; $file1=$file2; $file2=$tmp;
			$tmp = $f1_proj; $f1_proj=$f2_proj; $f2_proj=$tmp;
		}
		
		
		echo "<br/><br/>File diff: <b>{$_GET['filediff']}<b/><br>";
		
		//dir==1 korektiskai veikia zalia ten kur naujas kodas
		

		echo "<table style='width:100%'><tr><th>$f1_proj</th><th>$f2_proj</th></tr></table>";


		
		echo diff_helper::getTableStyle();
		

		echo diff_helper::toTable(diff_helper::compare($file1,$file2), "\t","");

		echo diff_helper::scripts();
		exit;
	}
	
		if(isset($_REQUEST['act']) && $_REQUEST['act']=='doSync'){
			$path = GW::s('DIR/ROOT')."applications/cli/sudogate.php";
			$sudouser = 'wdm';
			
		
			file_put_contents(GW::s('DIR/SYS_REPOSITORY').'sync_opts', json_encode($_REQUEST));
			
			$res=shell_exec($cmd="sudo -S -u $sudouser /usr/bin/php $path sync 2>&1");
			$resultid = date('Ymd_His') . '_' . substr(md5($cmd . $res . microtime(true)), 0, 8);
			file_put_contents(GW::s('DIR/SYS_REPOSITORY') . 'sync_result_' . $resultid . '.log', "$cmd\n$res");
			
			$jump = $_GET;
			unset($jump['act'], $jump['type'], $jump['dir'], $jump['confirm_remove'], $jump['remove_files']);
			$jump['sync_result'] = $resultid;
			
			header('Location: ?' . http_build_query($jump));
			
			exit;			
		}
	
	if(!isset($_GET['proj'])){

		foreach(glob(dirname(GW::s('DIR/ROOT')).'/*') as $path)
		{
			$proj= basename($path);
			echo "<li><a href='?proj=$proj'>$proj</a></li>";
		}
		exit;
		}else{
			
			$sync = new GW_CMS_Sync();
			
			if(isset($_GET['sync_result'])){
				$resultid = preg_replace('/[^a-zA-Z0-9_\\-]/', '', $_GET['sync_result']);
				$resultfile = GW::s('DIR/SYS_REPOSITORY') . 'sync_result_' . $resultid . '.log';
				
				if(is_file($resultfile)){
					echo "<h3>Last sync result</h3>";
					echo "<pre>" . htmlspecialchars(file_get_contents($resultfile), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</pre>";
				}
			}
			
			
			
			
		$list = $sync->checkOne($_GET['proj']);
		
		
		
		$sync->fileDiffLink($list['exp'],['dir'=>0]);
		
		$sync->fileDiffLink($list['imp'],['dir'=>1]);
		d::ldump($list, ['noescape'=>1]);
		
		$eCp = count($list['exp']['copy']);
		$eRm = count($list['exp']['remove']);
		$iCp = count($list['imp']['copy']);
		$iRm = count($list['imp']['remove']);
		
		$core = GW::s('SYNC_MODULE') ? GW::s('SYNC_MODULE') : 'gwcms';
		
		if($iCp || $iRm){
			echo "<br><br><b>{$_GET['proj']}</b> &raquo;&raquo;&raquo; <b>$core</b> 
				<a href='?proj={$_GET['proj']}&dir=1&act=doSync&type=copy'>copy($iCp)</a>";
			$sync->removeConfirmForm($_GET['proj'], 1, $list['imp']['remove']);
		}
	
		
		if($eCp || $eRm){
			echo "<br><br><b>$core</b> &raquo;&raquo;&raquo; <b>{$_GET['proj']}</b> 
				<a href='?proj={$_GET['proj']}&dir=0&act=doSync&type=copy'>copy($eCp)</a>";
			$sync->removeConfirmForm($_GET['proj'], 0, $list['exp']['remove']);
		}
		
		exit;
	}
}


/*

if(isset($_GET['diffshow'])){
	echo diff_helper::getTableStyle();
	echo diff_helper::toTable(diff::compare($file2,$file1), "\t","");	
}
*/


// per komandine eilute priimti komanda

if(!isset($argv[1]))
{
	$argv[1]='-h';
}

$sync = new GW_CMS_Sync();
$sync->process($argv);
