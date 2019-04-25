<?php

include "init_basic.php";



class GW_CMS_Sync
{
	public $params;
	public $sourceDir;
	public $destDir;
	public $projDir;
	public $sync_direction;
	
	function parseParams($argv)
	{
		$params = array();
		foreach ($argv as $arg)
			if (preg_match('/--(.*?)=(.*)/', $arg, $reg))
				$params[$reg[1]] = $reg[2];
			elseif (preg_match('/-([a-z0-9_-]*)/i', $arg, $reg))
				$params[$reg[1]] = true;

		$this->params = $params;
	}
	
	
	function enterWorkDir()
	{
		return "cd ".$this->sourceDir." && ";
	}	
	
	function execute($cmd)
	{
		$this->out("$cmd ...");
		passthru($this->enterWorkDir().$cmd);
	}
	
	function exec($cmd)
	{
		$this->out("$cmd ..");
		return shell_exec($this->enterWorkDir().$cmd);
	}	

	function getLastCommitWhenVersionsWereSynced($bydate=false){

		/*
		$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';

		$format='--pretty=format:"%H %s %ai"';

		if($bydate){
			$out = explode("\n", trim($this->exec($enter_repos."git log $format --since=$bydate")));

			list($last_commit, $commitmsg) = explode(' ', $out[count($out)-1], 2);

		}else{
			$out = explode("\n", trim($this->exec($enter_repos."git log $format --grep='gwcms uptodate'")));

			if ($out[0]) {
				list($last_commit, $commitmsg) = explode(' ', $out[0], 2);
			} else {
				return false;
			}		
		}

		$lastcommit_date = $this->exec("git show -s --format=%ci $last_commit");

		return [
			'lastcommit' => $last_commit,
			'lastcommitmsg' => $commitmsg,
			'lastcommit_date' => $lastcommit_date,
			'allcommits' => $out
		];
		 */	
	}
	
	function getLastSyncTime()
	{
		$f=$this->projDir."config/project_core_sync.json";
		$dat = json_decode(file_get_contents($f));
		return $dat->{$this->sync_direction};	
	}
	
	function storeSyncTime()
	{
		$f=$this->projDir."config/project_core_sync.json";
		$dat = json_decode(file_get_contents($f));
		$dat->{$this->sync_direction} = date('Y-m-d H:i:s');
		
		file_put_contents($f, json_encode($dat));
	}

	/**
	 * will return last comit since specified time
	 */
	function getOneCommitBefore($date)
	{
		
		$commits = explode("\n", trim($this->exec('git log -n 1 --reverse  --until="'.$date.'" --pretty=format:"%H %s %ai"')));

		if(!isset($commits[0]))
			return false;

		list($commitid,$whatever) = explode(' ', $commits[0],2);

		return $commitid;
	}


	
	function getNewCommitsFromDate($lastcommit_date)
	{
		$format='--pretty=format:"%H %s %ai"';	

		$newcommits = explode("\n", trim($this->exec('git log  --since="' . $lastcommit_date . '" '.$format)));

		$updates_from_commit_com = $this->getOneCommitBefore($lastcommit_date);
		
		if(!$updates_from_commit_com){
			$first_commit = explode("\n", trim($this->exec('git rev-list --max-parents=0 HEAD')))[0];
			$updates_from_commit_com = $first_commit;
		}
			

		return [
			'newcommits'=>$newcommits,
			'updates_from_commit'=>$updates_from_commit_com,
		];
	}

	function filterProjectSpecific(&$filesarr, $direction=true)
	{
		$t= new GW_Timer;	

		$dir = $direction ? $this->sourceDir : $this->destDir;

		include $dir.'config/project_specific_files.php';


		foreach($filesarr as $idx => $file)
		{
			foreach($paths as $pattern)
				if(fnmatch($pattern, $file)){
					unset($filesarr[$idx]);
					echo "--(projspec) $file ($pattern)\n";
				}
		}	
		echo "filterProjectSpecific: ".$t->stop(5)." secs\n";
	}

	function filterMatchingFiles(&$filesarr)
	{
		$t= new GW_Timer;
		
		foreach($filesarr as $idx => $file)
		{
			if(GW_File_Helper::isFilesEqual($this->sourceDir.$file, $this->destDir.$file))
			{
				unset($filesarr[$idx]);
				echo "--(nochange) $file\n";
			}
		}
		echo "filterMatchingFiles: ".$t->stop(5)." secs\n";
	}


	function getChangedFiles($commit_id)
	{
		$files = explode("\n", trim($this->exec("git diff --stat $commit_id..HEAD --name-only")));	

		$files = $files==[''] ? [] : $files;


		$removes=[];
		foreach($files as $idx => $file)
		{
			if(!file_exists($this->sourceDir.$file))
			{
				$removes[]=$file;
				unset($files[$idx]);
			}
		}

		$this->filterProjectSpecific($files, true);
		$this->filterProjectSpecific($files, false);
		
		$this->filterProjectSpecific($removes, true);
		$this->filterProjectSpecific($removes, false);		

		$this->filterMatchingFiles($files);
		
		$ret = ['copy'=>array_values($files), 'remove'=>[] ];
		
		
		$ret[ isset($this->params['nr'])?'skip_remove':'remove' ] = array_values($removes);

		return $ret;
	}


	function doSync($changed_files)
	{
		$t= new GW_Timer;

		$destdir = $this->destDir;
		$sourcedir = $this->sourceDir;
		
		
		//copy to temp dir
		if(!isset($this->params['s'])){
			$copy2temp=true;
			$destdir = "/tmp/extractupdates_".date('Ymd_His').'/';
			mkdir($destdir);
		}else{
			$copy2temp=false;
		}
		

		$removefile = $destdir.'removefile.sh';
		$info_file = $destdir.'update_info_file.txt';



		echo "Copying files ";
		foreach($changed_files['copy'] as $file){
			@mkdir(dirname($destdir.$file), 0777, true);

			copy($sourcedir.$file, $destdir.$file);
			echo $file."\n";
		}
		echo "\n";


		$rm_cmds = '';
		foreach($changed_files['remove'] as $rmfile){
			unlink($destdir.$rmfile);
			$rm_cmds.="rm $rmfile\n";
		}


		if($copy2temp){
			if($changed_files['remove'])
				file_put_contents($removefile, $rm_cmds);

			$info['changed']=$changed_files;

			file_put_contents($info_file, print_r($info, true));
		}else{
			if($changed_files['remove']){
				echo "To remove files you should execute these commands:\n";
				echo $rm_cmds;
			}
		}

		//tar -xvzf archyvo_pavadinimas.tar.gz extractins i ta pati kataloga

		echo count($changed_files['copy'] )." Files were extracted to $destdir\n";

		echo __FUNCTION__.": ".$t->stop(5)." secs\n";
		
		$this->storeSyncTime();

		return $destdir;
	}





	function cmdL()
	{
		$last_sync = getLastCommitWhenVersionsWereSynced(true);
		$this->out($last_sync);		
	}
	
	function cmdNewc()
	{
		$new_commits = getNewCommitsFromDate($repos_local, $args[0]);
		$this->out($new_commits);		
	}
	
	function cmdGitshow()
	{
		$this->execute('git show '.$this->params['date'].' --name-only');
	}

	function cmdGitLog()
	{
		$cmd = 'git log --reverse --pretty=format:"%h %ai %s"';

		if($this->params['date'])
			$cmd.=" --since='{$this->params['date']}'";

		$this->execute($cmd);		
	}

	
	function cmdH()
	{
		$this->out(trim("
-imp // to import
-exp // to export
-p // preview changes (dont do actual sync)
-s // straight sync (otherwise changed files will be copied to temp dir)
-nr // no remove - dont remove files only copy
--proj='proj_repos' //select project (projects mus be in same dir 'ls /var/www/projects' : 'gwcms shopproject crmproject whateverproject')
--date='2018-01-01' //scan for changes from date
-l //get last sync time
-h //print help	
-newc // show new comits
-gitshow // git show
			"));
	}
	
	
	function actSync()
	{
		if(isset($this->params['date'])){
			$datefrom = $this->params['date'];
		}else{
			$datefrom = strtotime($this->getLastSyncTime());
		}
		//intended to get updates from core gwcms
		$this->out("Last sync ".date('Y-m-d H:i:s', $datefrom));
		
		$newcommits = $this->getNewCommitsFromDate($datefrom);
		
		
		//print_r($newcommits);
		//exit;
		
		$changed_files = $this->getChangedFiles($newcommits['updates_from_commit']);

		if(isset($this->params['p']))
		{
			print_r($changed_files);
			return $changed_files;
		}			

		$dir = $this->doSync($changed_files);
		
		if(!isset($this->params['s']))
			shell_exec("krusader --left=$dir --right='".$this->destDir."'");
	}
	
	
	function setDirection($import=true)
	{
		$this->sync_direction = $import ? 'import':'export';
		
		$external = dirname(__DIR__).'/'.$this->params['proj'].'/';;
		$core = __DIR__.'/';	
		
		$this->projDir = $external;
		$this->sourceDir = $import ? $external : $core;
		$this->destDir = $import ? $core : $external;
		
		$this->out("Source: $this->sourceDir");
		$this->out("Destination: $this->destDir");
	}
	
	function cmdImp()
	{
		$this->setDirection(true);
		
		$this->actSync();
		
		$op = shell_exec($cmd="cd '$this->projDir' && git add config/project_core_sync.json && git commit -m 'changes exported to gwcms' && git push");		
		echo "SYNC VERSION FILE $cmd\nOUT: $op\n\n";			
	}
	
	function cmdExp()
	{
		$this->setDirection(false);
				
		$this->actSync();
	}
	
	function checkOne($proj)
	{
		$this->params['p']=1;
		
		$this->params['proj'] = $proj;

		$this->setDirection(true);
		$imp = $this->actSync();
		$this->setDirection(false);
		$exp = $this->actSync();
			
		return ['exp'=>$exp, 'imp'=>$imp];
	}
	
	function cmdCheck()
	{
		$projects = explode(',', $this->params['proj']);
				
		$short = [];
		$long = [];
		
		foreach($projects as $project)
		{	
			list($exp, $imp) = $this->cmdCheckOne($project);
			
			$long[$project] = ['imp'=>$imp, 'exp'=>$exp];
			$short[$project] = ['imp'=>count($imp['copy'])+count($imp['remove']), 'exp'=>count($exp['copy'])+count($exp['remove'])];			
		}
		
		print_r($long);
		print_r($short);
	}
	
	function cmdWeb()
	{
		$optsfile=GW::s('DIR/SYS_REPOSITORY').'sync_opts';
		if(!file_exists($optsfile))
			die('optsfile does note exists');
		
		$res = file_get_contents($optsfile);
		$res = json_decode($res);
		
		
		if($res->act=='doSync'){
			$this->params['proj'] = $res->proj;
			$this->params['s'] = true;
			
			$this->setDirection($res->dir == '1');
			$this->actSync();			
		}			
			
		unlink($optsfile);
	}
	
	
	function process($argv)
	{
		$this->parseParams($argv);
						
		foreach($this->params as $key => $val){
			$act="cmd{$key}";
			if(method_exists($this, $act)){
				$this->out("act: $act");
				$this->$act();
			}
		}
	}
	
	function out($msg)
	{
		echo is_object($msg) || is_array($msg) ? print_r($msg,1) : $msg."\n";
	}
	
	function fileDiffLink(&$list,$addpathopt=[])
	{
		foreach($list['copy'] as &$path)
		{
			$url = Navigator::buildURI(false, ['filediff'=>$path]+$addpathopt+$_GET);
			$path = "<a href='$url'>$path</a>";
		}
	}
	

	
}

if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']=='gwcms'){
	
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
		
		echo "<br/><br/>File diff: <b>{$_GET['filediff']}<b/><br>";
		echo "<table style='width:100%'><tr><th>$f2_proj</th><th>$f1_proj</th></tr></table>";
		
		echo diff_helper::getTableStyle();
		echo diff_helper::toTable(diff_helper::compare($file2,$file1), "\t","");
		echo diff_helper::scripts();
		exit;
	}
	
	if(isset($_GET['act']) && $_GET['act']=='doSync'){
		$path = GW::s('DIR/ROOT')."applications/cli/sudogate.php";
		$sudouser = 'wdm';
		
	
		file_put_contents(GW::s('DIR/SYS_REPOSITORY').'sync_opts', json_encode($_GET));
		
		$res=shell_exec($cmd="sudo -S -u $sudouser /usr/bin/php $path sync 2>&1");
		
		
		
		d::ldump($res);
		exit;			
	}
	
	if(!isset($_GET['proj'])){

		foreach(glob(dirname(__DIR__).'/*') as $path)
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
		
		if($eCp || $eRm)
		echo "<br><a href='?proj={$_GET['proj']}&dir=0&act=doSync'>Export gwcms changes to <b>{$_GET['proj']}</b> copy($eCp) remove($eRm)</a>";
		
		if($iCp || $iRm)
		echo "<br><a href='?proj={$_GET['proj']}&dir=1&act=doSync'>Import <b>{$_GET['proj']}</b> changes to gwcms copy($iCp) remove($iRm)</a>";
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


/*

echo "Enter command\n";
while (false !== ($line = fgets(STDIN))) {
	processCommand($line);
	echo "Done, enter next command\n";
}
 * */