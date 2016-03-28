<?php

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

$dir = __DIR__;
my_shell_exec("cd '$dir'");

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



function getNewCommitsFromDate($repos_local=true, $lastcommit_date)
{
	
	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
		
	$format='--pretty=format:"%H %s %ai"';	
	
	$newcommits = explode("\n", trim(my_shell_exec($enter_repos.'git log  --since="' . $lastcommit_date . '" '.$format)));
	$updates_from_commit = explode(' ', $newcommits[count($newcommits) - 1], 2);
	$updates_from_commit_com = $updates_from_commit[0];
	return [
		'newcommits'=>$newcommits,
		'updates_from_commit'=>$updates_from_commit,
		'commit_id'=>$updates_from_commit_com
		];
}

function getChangedFiles($repos_local=true, $commit_id)
{
	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
	$files = explode("\n", trim(my_shell_exec($enter_repos."git diff --stat $commit_id..HEAD --name-only")));	
	
	return $files==[''] ? [] : $files;
}

function exportExtract2Tmp($repos_local=true, $commit_id)
{
	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
	execute($enter_repos."git diff --stat $commit_id..HEAD --name-only | tar czf /tmp/exportchanges.tar.gz -T -");
	
	$outdir = "/tmp/extractupdates_".date('Ymd_His');
	mkdir($outdir);
	
	execute('cd '.$outdir.' && for a in `ls -1 /tmp/exportchanges.tar.gz`; do tar -zxvf $a; done');	
	//tar -xvzf archyvo_pavadinimas.tar.gz extractins i ta pati kataloga
	
	echo "Files were extracted to $outdir\n";
	return $outdir;
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
	
	switch ($line[0]) {
		case '1':
			/*
			if($UPDATES2CORE){//to gwvms
				exportExtract2Tmp(true, $last_sync['lastcommit']);
			}else{//from gwcms
				exportExtract2Tmp(false, $last_sync['lastcommit']);	
			}
			*/
		break;

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
			//to check updates before get updates
			$last_sync = getLastCommitWhenVersionsWereSynced(true);
			$new_commits = getNewCommitsFromDate(false, $last_sync['lastcommit_date']);
			
			$changed_files = getChangedFiles(false, $new_commits['commit_id']);
			
			print_r(['last_sync'=>$last_sync,
				'new_commits'=>$new_commits,
				'changed_files'=>$changed_files, 
				]);
		break;
	
		case '2':
		case 'exportupdatesfromcore':
			//intended to get updates from core gwcms
			$last_sync = getLastCommitWhenVersionsWereSynced(true);
			$new_commits = getNewCommitsFromDate(false, $last_sync['lastcommit_date']);
			
			exportExtract2Tmp(false, $new_commits['commit_id']);
		break;
		
		case '3':
		case 'showupdates2core':
			$last_sync = getLastCommitWhenVersionsWereSynced();
			$newcommits = getNewCommitsFromDate(true, $last_sync['lastcommit_date']);//just for info
			$changed_files = getChangedFiles(true, $last_sync['lastcommit']);
			print_r(['$last_sync'=>$last_sync, '$newcommits'=>$newcommits, 'changed_files'=>$changed_files]);	
			
			if(!$changed_files)
				echo "NO CHANGED FILES\n";
		break;
	
		case '4':
		case 'exportupdates2core':
			$last_sync = getLastCommitWhenVersionsWereSynced();
			$newcommits = getNewCommitsFromDate(true, $last_sync['lastcommit_date']);//just for info			
			$dir = exportExtract2Tmp(true, $newcommits['commit_id']);
			
			shell_exec("krusader --left=$dir --right=../gwcms");
		break;
	
		case 'h':
			echo "c|p;log - show core|projectlog - c;log;2016-01-01 - show core log since 2016-01-01\n";
			echo "c|p;show;commit_id - show info about commit\n";
			
			echo "c|p;newc;2016-01-01 - new commits since date\n";
			echo "showupdates(1) - get info when was last 'gwcms uptodate' named commit\n";
			echo "exportupdatesfromcore(2) - get info when was last 'gwcms uptodate' named commit\n";
			echo "showupdates2core(3) - get info when was last 'gwcms uptodate' named commit\n";
			echo "exportupdates2core(4) - export files when was last 'gwcms uptodate' named commit\n";
		break;
	}	
	
}






// per komandine eilute priimti komanda

if(isset($argv[1]))
{
	processCommand($argv[1]);
	echo "\n";
}else{
	processCommand('h');
}


echo "Enter command\n";
while (false !== ($line = fgets(STDIN))) {
	processCommand($line);
	echo "Done, enter next command\n";
}