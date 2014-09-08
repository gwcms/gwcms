<?php


include dirname(__DIR__).'/init.php';

if(!$_GET['extend'])
	$do_not_register_request=true;


include GW::$dir['ADMIN'].'init_auth.php';

if(GW::$user)
	die((string)GW::$user->remainingSessionTime());

die('-2');