<?php


$ssh_run="ssh $dest_userdomain -p{$dest_ssh_port}";


$rsync_params=array(
		'destination'=>"$dest_userdomain:$dest_dir",
		'remote_ssh_port'=>$dest_ssh_port, 
		'source'=>$source, 
		'excludes'=>$excludes
	);

//add params via command line
//example php /var/www/gw_cms/deploy/sms.php -dry_run --test=123
$rsync_params += GW_App_Base::parseParams();


if($rsync_params['run'])
{
	unset($rsync_params['run']);
	Rsync_Helper::exec($rsync_params);
	exit;
}


dump('Dry run:');

Rsync_Helper::exec($rsync_params + Array('dry_run'=>1));

dump('To run add -run param');

	
