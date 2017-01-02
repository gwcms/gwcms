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

function getChangedFiles($repos_local=true, $commit_id)
{
	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
	$dir = $repos_local ? __DIR__ : dirname(__DIR__).'/gwcms';
	
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
	

	
	return ['copy'=>$files, 'remove'=>$removes];
}

function exportExtract2Tmp($repos_local=true, $commit_id, $info=[])
{
	$enter_repos = $repos_local ? '' : 'cd ../gwcms && ';
	execute($enter_repos."git diff --stat $commit_id..HEAD --name-only | tar czf /tmp/exportchanges.tar.gz -T -");
	
	$outdir = "/tmp/extractupdates_".date('Ymd_His');
	mkdir($outdir);
	
	execute('cd '.$outdir.' && for a in `ls -1 /tmp/exportchanges.tar.gz`; do tar -zxvf $a; done');	
	
	$removefile = $outdir.'/removefile.sh';
	$info_file = $outdir.'/update_info_file.txt';
	
	$str = '';	
	$changed_files = getChangedFiles(false, $commit_id);
	foreach($changed_files['remove'] as $rmfile)
		$str.="rm $rmfile\n";
	
	if($changed_files['remove'])
		file_put_contents($removefile, $str);
	
	$info['changed']=$changed_files;
	
	file_put_contents($info_file, print_r($info, true));
	
	
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
	
	print_r($args);
	
	switch ($line[0]) {
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
			if(isset($args[0]))
				$datefrom = $args[0];
			else{
				$last_sync = getLastCommitWhenVersionsWereSynced(true);
				$datefrom = $last_sync['lastcommit_date'];
			}	
			echo "labadiena\n";
			//to check updates before get updates
			
			$new_commits = getNewCommitsFromDate(false, $datefrom);
			$changed_files = getChangedFiles(false, $new_commits['updates_from_commit']);
			
			print_r(['last_sync'=>isset($last_sync) ? $last_sync : false,
				'new_commits'=>$new_commits,
				'changed_files'=>$changed_files, 
				]);
		break;
	
		case '2':
		case 'importupdatesfromcore':
			if(isset($args[0]))
				$datefrom = $args[0];
			else{
				$last_sync = getLastCommitWhenVersionsWereSynced(true);
				$datefrom = $last_sync['lastcommit_date'];
			}
			//intended to get updates from core gwcms
			
			$new_commits = getNewCommitsFromDate(false, $datefrom);
			$dir = exportExtract2Tmp(false, $new_commits['updates_from_commit'], ['new_commits'=>$new_commits]);
			
			shell_exec("krusader --left=$dir --right='".__DIR__."'");
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
			if(isset($args[0]))
				$datefrom = $args[0];
			else{
				$last_sync = getLastCommitWhenVersionsWereSynced();
				$datefrom = $last_sync['lastcommit_date'];
			}		
			
			
			
			$newcommits = getNewCommitsFromDate(true, $datefrom);//just for info			
			
			$dir = exportExtract2Tmp(true, $newcommits['updates_from_commit']);
			
			shell_exec("krusader --left=$dir --right=../gwcms");
		break;
	
		case 'h':
			echo "c|p;log - show core|projectlog - c;log;2016-01-01 - show core log since 2016-01-01\n";
			echo "c|p;show;commit_id - show info about commit\n";
			
			echo "c|p;newc;2016-01-01 - new commits since date\n";
			echo "1 - showupdates[;2016-01-01] - get info when was last 'gwcms uptodate' named commit\n";
			echo "2 - importupdatesfromcore[;2016-01-01] - get info when was last 'gwcms uptodate' named commit - or enter date from\n";
			echo "3 - showupdates2core - get info when was last 'gwcms uptodate' named commit\n";
			echo "4 - exportupdates2core[;2016-01-01] - export files when was last 'gwcms uptodate' named commit\n";
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