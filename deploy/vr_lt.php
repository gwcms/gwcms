<?
//vykdyti su php-cli

include dirname(__DIR__).'/admin/init.php';



$source=dirname(__DIR__).'/';
$dest_dir="/home/www/vr.lt";
$dest_userdomain="root@uostas.net";
$dest_ssh_port="63842";//default 22

include __DIR__.'/excludes_for_general_cms.php';
include __DIR__.'/general_rsync.php';
