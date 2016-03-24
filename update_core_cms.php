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
	
	if($bydate){
		$out = explode("\n", trim(my_shell_exec($enter_repos.'git log --oneline --since='.$bydate)));
		
		list($last_commit, $commitmsg) = explode(' ', $out[count($out)-1], 2);
		
	}else{
		$out = explode("\n", trim(my_shell_exec($enter_repos.'git log --oneline --grep="gwcms uptodate"')));
		
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
		
		
	$newcommits = explode("\n", trim(my_shell_exec($enter_repos.'git log  --since="' . $lastcommit_date . '" --format=oneline')));
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
	return explode("\n", trim(my_shell_exec($enter_repos."git diff --stat $commit_id..HEAD --name-only")));	
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



//update FROM GWCMS
/*
$last_sync = getLastCommitWhenVersionsWereSynced(false, '2015-12-16');

if(!$last_sync)
	die('no last commit with gwcms uptodate');

$newcommits = getNewCommitsFromDate(false, $last_sync['lastcommit_date']);//just for info
$changed_files = [];
//$changed_files = getChangedFiles(false, $last_sync['lastcommit']);
print_r(['$last_sync'=>$last_sync, '$newcommits'=>$newcommits, 'changed_files'=>$changed_files]);
*/


//SEND UPDATES TO GWCMS
$last_sync = getLastCommitWhenVersionsWereSynced();
$newcommits = getNewCommitsFromDate(true, $last_sync['lastcommit_date']);//just for info
$changed_files = getChangedFiles(true, $last_sync['lastcommit']);
print_r(['$last_sync'=>$last_sync, '$newcommits'=>$newcommits, 'changed_files'=>$changed_files]);





echo "p - pull\n";
echo "1 - export files to /tmp dir\n";
echo "2 - apply diff\n";

echo "l - git status\n";
echo "c - commit -m 'gwcms uptodate' && git push //// please check if updates didnt messed up things too badly\n";

echo "ld,2010-01-01 00:00 - gwcms list new commits from date\n";
echo "ctrl+c - quit\n";

while (false !== ($line = fgets(STDIN))) {
	echo "Your choice: " . $line;

	$line = trim($line);
	$line = explode(",", $line, 2);
	$args = isset($line[1]) ? explode(',', $line[1]) : [];
		
	
	switch ($line[0]) {
		case '1':
			//to gwvms
			exportExtract2Tmp(true, $last_sync['lastcommit']);
			
			//from gwcms
			//exportExtract2Tmp(false, $last_sync['lastcommit']);
		break;

	
		case 'p':
			execute("git pull");
		break;
		case 'l':
			execute("git status");
		break;
		case 'ld':
			execute('cd ../gwcms && git diff --stat @{'.$args[0].'}..HEAD');
		break;
	
	}
	
	echo "Done, enter next command\n";
}