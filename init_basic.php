<?php

$debug=Array('mem_use'=>Array(memory_get_usage(true)));

error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT);

if(file_exists(__DIR__ . '/vendor/autoload.php'))
	require_once __DIR__ . '/vendor/autoload.php';

include "framework/functions.php";
include "framework/gw_autoload.class.php";
include "framework/gw.class.php";

include "config/main.php";

GW_Autoload::init();

register_shutdown_function(['GW_Debug_Helper','errorReport']);

$proc_timer = new GW_Timer;

GW::init();
