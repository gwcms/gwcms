<?php

ob_start();
session_start();

include __DIR__.'/init_basic.php';
include GW::$dir['LIB'].'site_utility.php';
include GW::$dir['MODULES'].'users/gw_adm_user.class.php';
