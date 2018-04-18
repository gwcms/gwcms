<?php

include "init_basic.php";



function execute($cmd)
{
	echo "$cmd ...\n";
	passthru($cmd);
}
function my_shell_exec($cmd)
{
	echo "$cmd ...\n";
	return shell_exec($cmd);
}


my_shell_exec("cd '".__DIR__."'");



function getLastCommitWhenVersionsWereSynced($repos_local=true, $bydate=false){

	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
	
	$format='--pretty=format:"%H %s %ai"';
	
	if($bydate){
		$out = explode("\n", trim(my_shell_exec($enter_repos."git log $format --since=$bydate")));
		
		list($last_commit, $commitmsg) = explode(' ', $out[count($out)-1], 2);
		
	}else{
		$out = explode("\n", trim(my_shell_exec($enter_repos."git log $format --grep='gwcms uptodate'")));
		
		if ($out[0]) {
			list($last_commit, $commitmsg) = explode(' ', $out[0], 2);
		} else {
			return false;
		}		
	}
	
	


	$lastcommit_date = my_shell_exec($enter_repos."git show -s --format=%ci $last_commit");

	return [
		'lastcommit' => $last_commit,
		'lastcommitmsg' => $commitmsg,
		'lastcommit_date' => $lastcommit_date,
		'allcommits' => $out
	];	
}

/**
 * will return last comit since specified time
 */
function getOneCommitBefore($repos_local, $date)
{
	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
	
	$commits = explode("\n", trim(my_shell_exec($enter_repos.'git log -n 1 --reverse  --until="'.$date.'" --pretty=format:"%H %s %ai"')));
	
	if(!isset($commits[0]))
		return false;
	
	list($commitid,$whatever) = explode(' ', $commits[0],2);
		
	return $commitid;
}


function getNewCommitsFromDate($repos_local=true, $lastcommit_date)
{
	
	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
		
	$format='--pretty=format:"%H %s %ai"';	
	
	$newcommits = explode("\n", trim(my_shell_exec($enter_repos.'git log  --since="' . $lastcommit_date . '" '.$format)));
	
	
	//$updates_from_commit = explode(' ', $newcommits[count($newcommits) - 1], 2);
	//$updates_from_commit_com = $updates_from_commit[0];
	
	
	$updates_from_commit_com = getOneCommitBefore($repos_local, $lastcommit_date);
	
	return [
		'newcommits'=>$newcommits,
		'updates_from_commit'=>$updates_from_commit_com,
		
		];
}

function filterProjectSpecific(&$filesarr, $repos_local=true)
{
	$t= new GW_Timer;	
	
	$dir = $repos_local ? GW::s('DIR/ROOT') : dirname(__DIR__).'/gwcms/';
	
	include $dir.'config/project_specific_files.php';
	
	
	foreach($filesarr as $idx => $file)
	{
		foreach($paths as $pattern)
			if(fnmatch($pattern, $file)){
				unset($filesarr[$idx]);
				echo "--(projspec) $file ($pattern)\n";
			}
	}	
	echo "filterProjectSpecific: ".$t->stop()." secs\n";
}

function filterMatchingFiles(&$filesarr, $sourceDir, $destDir)
{
	$t= new GW_Timer;
	
	foreach($filesarr as $idx => $file)
	{
		if(GW_File_Helper::isFilesEqual($sourceDir.$file, $destDir.$file))
		{
			unset($filesarr[$idx]);
			echo "--(nochange) $file\n";
		}
	}
	echo "filterMatchingFiles: ".$t->stop()." secs\n";
}


function getChangedFiles($repos_local=true, $commit_id)
{
	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
	$dir = $repos_local ? __DIR__ : dirname(__DIR__).'/gwcms';
	$destdir = !$repos_local ? __DIR__ : dirname(__DIR__).'/gwcms';
	
	$files = explode("\n", trim(my_shell_exec($enter_repos."git diff --stat $commit_id..HEAD --name-only")));	
	
	$files = $files==[''] ? [] : $files;
	
	
	$removes=[];
	foreach($files as $idx => $file)
	{
		if(!file_exists($dir.'/'.$file))
		{
			$removes[]=$file;
			unset($files[$idx]);
		}
	}
	
	filterProjectSpecific($files, true);
	filterProjectSpecific($files, false);
	
	filterMatchingFiles($files, $dir.'/', $destdir.'/');

	return ['remove'=>$removes, 'copy'=>$files];
}


function exportExtract2Tmp($repos_local=true, $changed_files, $copy2temp=true)
{
	$t= new GW_Timer;
	
	$sourcedir = $repos_local ? __DIR__ : dirname(__DIR__).'/gwcms';
	
	if($copy2temp){
		$destdir = "/tmp/extractupdates_".date('Ymd_His');
		mkdir($destdir);
		
	}else{
		$destdir = !$repos_local ? __DIR__ : dirname(__DIR__).'/gwcms';		
	}
	
	$removefile = $destdir.'/removefile.sh';
	$info_file = $destdir.'/update_info_file.txt';
	
	
	
	echo "Copying files ";
	foreach($changed_files['copy'] as $file){
		@mkdir(dirname($destdir.'/'.$file), 0777, true);
		
		copy($sourcedir.'/'.$file, $destdir.'/'.$file);
		echo $file."\n";
		
	}
	echo "\n";
		
	
	$rm_cmds = '';
	foreach($changed_files['remove'] as $rmfile)
		$rm_cmds.="rm $rmfile\n";
	
	
	if($copy2temp){
		if($changed_files['remove'])
			file_put_contents($removefile, $rm_cmds);

		$info['changed']=$changed_files;

		file_put_contents($info_file, print_r($info, true));
	}else{
		echo "To remove files you should execute these commands:\n";
		echo $rm_cmds;
	}
	
	//tar -xvzf archyvo_pavadinimas.tar.gz extractins i ta pati kataloga
	
	echo count($changed_files['copy'] )." Files were extracted to $destdir\n";
	
	echo __FUNCTION__.": ".$t->stop(5)." secs\n";
	
	return $destdir;
}

function gitlog($repos_local=true, $datesince=''){

	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
	
	$cmd = $enter_repos.'git log --reverse --pretty=format:"%h %ai %s"';
	
	if($datesince)
		$cmd.=" --since='$datesince'";
	
	execute($cmd);
}

function gitshow($repos_local, $commit_id)
{
	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
	
	execute($cmd = $enter_repos.'git show '.$commit_id.' --name-only');
}





function processCommand($line, $repos_local=true){
	echo "Processing command: " . $line."\n";
	$line = trim($line);
	$line = explode(';',$line, 2);
	$args = isset($line[1]) ? explode(';', $line[1]) : [];
	
	print_r($args);
	
	$cmd = $line[0];
	
	switch ($cmd) {
		case 'c':
			processCommand(implode(';', $args), false);
		break;
		case 'p':
			processCommand(implode(';', $args), true);
		break;	
	
		case 'log':
			if(isset($args[0]))
				gitlog($repos_local,$args[0]);
			else
				gitlog($repos_local);
		break;
		case 'show':
			gitshow($repos_local, $args[0]);
		break;
		case 'newc':
			$new_commits = getNewCommitsFromDate($repos_local, $args[0]);
			print_r($new_commits);
		break;	
	
		case 'lastsync':
			// to check upates from core
			$last_sync = getLastCommitWhenVersionsWereSynced(true);
			print_r($last_sync);
		break;	
	

		
		case '1':
		case 'showupdates':		
		case '2':
		case 'importupdatesfromcore':
			if(isset($args[0]))
				$datefrom = $args[0];
			else{
				$last_sync = getLastCommitWhenVersionsWereSynced(true);
				$datefrom = $last_sync['lastcommit_date'];
			}
			//intended to get updates from core gwcms
			
			$newcommits = getNewCommitsFromDate(false, $datefrom);
			$changed_files = getChangedFiles(false, $newcommits['updates_from_commit']);
			
			if($cmd == '1' || $cmd=='showupdates')
			{
				print_r($changed_files);
				break;
			}			
			
			$dir = exportExtract2Tmp(false, $changed_files);
			
			shell_exec("krusader --left=$dir --right='".__DIR__."'");
		break;
		
		case '3':
		case 'showupdates2core':	
		case '4':
		case 'exportupdates2core':
			if(isset($args[0])){
				$datefrom = $args[0];
			}else{
				$last_sync = getLastCommitWhenVersionsWereSynced();
				$datefrom = $last_sync['lastcommit_date'];
			}	
			
			$straight_to_core = $args[1] ?? false;
			
			$newcommits = getNewCommitsFromDate(true, $datefrom);//just for info	
			$changed_files = getChangedFiles(true, $newcommits['updates_from_commit']);
							
			if($cmd == '3' || $cmd=='showupdates2core')
			{
				print_r($changed_files);
				break;
			}
			
			$dir = exportExtract2Tmp(true, $changed_files, !$straight_to_core);
			
			if(!$straight_to_core)
				shell_exec("krusader --left=$dir --right=../gwcms");
		break;
	
		case 'h':
			echo "c|p;log - show core|projectlog - c;log;2016-01-01 - show core log since 2016-01-01\n";
			echo "c|p;show;commit_id - show info about commit\n";
			
			echo "c|p;newc;2016-01-01 - new commits since date\n";
			echo "1 - showupdates[;2016-01-01] - get info when was last 'gwcms uptodate' named commit\n";
			echo "2 - importupdatesfromcore[;2016-01-01] - get info when was last 'gwcms uptodate' named commit - or enter date from\n";
			echo "3 - showupdates2core - get info when was last 'gwcms uptodate' named commit\n";
			echo "4 - exportupdates2core[;2016-01-01][;1] - export files when was last 'gwcms uptodate' named commit 3rd arg to send files straight to core\n";
		break;
	}	
	
}


	



// per komandine eilute priimti komanda

if(isset($argv[1]))
{
	processCommand($argv[1]);
	echo "\n";
	exit;
}else{
	processCommand('h');
}


echo "Enter command\n";
while (false !== ($line = fgets(STDIN))) {
	processCommand($line);
	echo "Done, enter next command\n";
}