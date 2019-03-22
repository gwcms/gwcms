<?php

include "init_basic.php";



class GW_CMS_Sync
{
	public $params;
	private $sourceDir;
	private $destDir;
	private $projDir;
	private $sync_direction;
	
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
			return true;
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
	}
	
	function cmdExp()
	{
		$this->setDirection(false);
				
		$this->actSync();
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

	
}




	



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