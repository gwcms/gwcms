<?
//vykdyti su php-cli

include dirname(__DIR__).'/admin/init.php';



$source=dirname(__DIR__).'/';
$dest_dir="/home/www/karolisvil/padangu-arsenalas.lt/public";
$dest_userdomain="root@gw.lt";
$dest_ssh_port="63842";//default 22

include __DIR__.'/excludes_for_general_cms.php';
include __DIR__.'/general_rsync.php';
