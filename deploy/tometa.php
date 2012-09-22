<?
//vykdyti su php-cli

include dirname(__DIR__).'/admin/init.php';

function gwpassthru($cmd)
{
	passthru($cmd);
	dump($cmd);
}

function rsync($params)
{
	$port = $params['ssh_port']?:22;
	
	$cmd = "rsync -e 'ssh -p $port' -Ovrtgoz ";

	foreach($params['excludes'] as $exclude)
		$cmd.="--exclude='".$exclude."' ";
	
	$cmd.=$params['source']." ".$params['remote_dir'];
	
	gwpassthru($cmd);
}

$source=dirname(__DIR__).'/';

$dest_dir="/home/www/main/proj/tometa/";
$userdomain="root@uostas.net";
$ssh_port="63842";//default 22

$excludes = Array('.svn','modules/gallery','modules/dropindesign','modules/articles', 'modules/customers');
$excludes[]='project_name.php'; //or /admin/config/project_name.php
$excludes[]='repository/*'; //or /admin/repository/*
$excludes[]='public/*'; 
$excludes[]='/.htaccess';


$ssh_run="ssh $userdomain -p{$ssh_port}";

rsync(array('remote_dir'=>"$userdomain:$dest_dir",'ssh_port'=>$ssh_port, 'source'=>$source, 'excludes'=>$excludes));


//$excludes = Array('.svn');

gwpassthru("$ssh_run chmod -R a-x+rwX $dest_dir/admin/repository");
gwpassthru("$ssh_run touch $dest_dir/admin/config/GW_ENV_TEST");
