<?
//vykdyti su php-cli

include dirname(__DIR__).'/admin/init.php';



$source=dirname(__DIR__).'/';
$dest_dir="/home/www/sms/new";
$dest_userdomain="root@uostas.net";
$dest_ssh_port="63842";//default 22

include __DIR__.'/excludes_for_general_cms.php';
include __DIR__.'/general_rsync.php';



//$excludes = Array('.svn');

passthru("$ssh_run chmod -R a-x+rwX $dest_dir/admin/repository");
passthru("$ssh_run touch $dest_dir/admin/config/GW_ENV_TEST");
