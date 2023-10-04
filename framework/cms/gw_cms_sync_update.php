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
	
	if(isset($_GET['act']) && $_GET['act']=='doSync'){
		$path = GW::s('DIR/ROOT')."applications/cli/sudogate.php";
		$sudouser = 'wdm';
		
	
		file_put_contents(GW::s('DIR/SYS_REPOSITORY').'sync_opts', json_encode($_GET));
		
		$res=shell_exec($cmd="sudo -S -u $sudouser /usr/bin/php $path sync 2>&1");
		d::ldump("$cmd\n:$res");
		
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
		$list = $sync->checkOne($_GET['proj']);
		$sync->fileDiffLink($list['exp'],['dir'=>0]);
		$sync->fileDiffLink($list['imp'],['dir'=>1]);
		d::ldump($list);
		
		$eCp = count($list['exp']['copy']);
		$eRm = count($list['exp']['remove']);
		$iCp = count($list['imp']['copy']);
		$iRm = count($list['imp']['remove']);
		
		$core = GW::s('SYNC_MODULE') ? GW::s('SYNC_MODULE') : 'gwcms';
		
		if($iCp || $iRm)
			echo "<br><br><b>{$_GET['proj']}</b> &raquo;&raquo;&raquo; <b>$core</b> 
				<a href='?proj={$_GET['proj']}&dir=1&act=doSync&type=copy'>copy($iCp)</a> 
				<a href='?proj={$_GET['proj']}&dir=1&act=doSync&type=remove'>remove($iRm)</a>";
	
		
		if($eCp || $eRm)
		echo "<br><br><b>$core</b> &raquo;&raquo;&raquo; <b>{$_GET['proj']}</b> 
			<a href='?proj={$_GET['proj']}&dir=0&act=doSync&type=copy'>copy($eCp)</a> 
				<a href='?proj={$_GET['proj']}&dir=0&act=doSync&type=remove'>remove($eRm)</a>";
		
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

