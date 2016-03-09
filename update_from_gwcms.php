<?php

function execute($cmd)
{
	echo "$cmd ...\n";
	passthru($cmd);
}

$dir = __DIR__;
shell_exec("cd '$dir'");

$out = explode("\n", trim(shell_exec('git log --oneline --grep="gwcms uptodate"')));
;
if ($out[0]) {
	list($last_commit, $commitmsg) = explode(' ', $out[0], 2);
} else {
	die('no last commit with gwcms uptodate');
}

$lastcommit_date = shell_exec("git show -s --format=%ci $last_commit");

print_r([
	'lastcommit' => $last_commit,
	'lastcommitmsg' => $commitmsg,
	'lastcommit_date' => $lastcommit_date,
	'allcommits' => $out
]);


$newcommits = explode("\n", trim(shell_exec('cd ../gwcms && git log  --since="' . $lastcommit_date . '" --format=oneline')));
$updates_from_commit = explode(' ', $newcommits[count($newcommits) - 1], 2);
$updates_from_commit_com = $updates_from_commit[0];



//shell_exec("git archive -o /tmp/patch.zip e96d66ae0af2bbd39c08fdb9de0d8cb0589f8190 $(git diff --name-only e96d66ae0af2bbd39c08fdb9de0d8cb0589f8190..HEAD)");
//shell_exec('git diff-tree -r --no-commit-id --name-only e96d66ae0af2bbd39c08fdb9de0d8cb0589f8190 HEAD | xargs tar -rf test.tar');


$changed_files = explode("\n", trim(shell_exec("cd ../gwcms && git diff --stat $updates_from_commit_com..HEAD --name-only")));



print_r([
	'lastcommit' => $last_commit,
	'lastcommitmsg' => $commitmsg,
	'lastcommit_date' => $lastcommit_date,
	'allcommits' => $out,
	'newcommits' => $newcommits,
	'updates_from' => $updates_from_commit,
	'changed_files' => $changed_files,
]);




echo "p - pull\n";
echo "1 - extract exported files\n";
echo "2 - apply diff\n";

echo "l - git status\n";
echo "c - commit -m 'gwcms uptodate' && git push\n";

echo "ld,2010-01-01 00:00 - gwcms list new commits from date\n";
echo "ctrl+c - quit\n";

while (false !== ($line = fgets(STDIN))) {
	echo "Your choice: " . $line;

	$line = trim($line);
	$line = explode(",", $line, 2);
	$args = isset($line[1]) ? explode(',', $line[1]) : [];
		
	
	switch ($line[0]) {
		case '1':
			execute("cd ../gwcms && git diff --stat $updates_from_commit_com..HEAD --name-only | tar czf /tmp/updates_from_gwcms.tar.gz -T -");
			execute('cd '.$dir.' && for a in `ls -1 /tmp/updates_from_gwcms.tar.gz`; do tar -zxvf $a; done');
		break;
		case '2':
			execute("cd ../gwcms && git diff $updates_from_commit_com HEAD > /tmp/test.diff");
			execute('ls -l /tmp/test.diff');
			execute("cd $dir && patch  -p1 < /tmp/test.diff");
		break;
	
		case 'p':
			execute("git pull");
		break;
		case 'l':
			execute("git status");
		break;
		case 'c':
			execute("git add . && git commit -m 'gwcms uptodate' && git push");
		break;
	
		case 'ld':
			execute('cd ../gwcms && git diff --stat @{'.$args[0].'}..HEAD');
		break;
	
	}
	
	echo "Done, enter next command\n";
}


	