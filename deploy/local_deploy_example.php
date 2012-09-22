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

	$cmd = "rsync ".
	(isset($params['remote_ssh_port']) ? "-e 'ssh -p $port'" : '').
	" -Ovrtgoz ";

	foreach($params['excludes'] as $exclude)
		$cmd.="--exclude='".$exclude."' ";
	
	$cmd.=$params['source']." ".$params['destination'];
	
	gwpassthru($cmd);
}

$source=dirname(__DIR__).'/';

$destination = '/var/www/vizualireklama/';

$dest_domain = "wdm@localhost";
$dest_ssh_port="22";//default 22
$dest_dir = "/var/www/vizualireklama/";

//$destination="$dest_domain:$dest_dir";



$excludes = Array('.svn','modules/gallery','modules/dropindesign','modules/articles', 'modules/customers');
$excludes[]='project_name.php'; //or /admin/config/project_name.php
$excludes[]='repository/*'; //or /admin/repository/*
$excludes[]='public/*'; 
$excludes[]='/.htaccess';


$ssh_run="ssh $dest_domain -p{$dest_ssh_port}";

$params = array(
	'destination'=>$destination,
//	'remote_ssh_port'=>$dest_ssh_port, 
	'source'=>$source, 
	'excludes'=>$excludes
);


rsync($params);


//$excludes = Array('.svn');

//gwpassthru("$ssh_run chmod -R a-x+rwX $dest_dir/admin/repository");
//gwpassthru("$ssh_run touch $dest_dir/admin/config/GW_ENV_TEST");
