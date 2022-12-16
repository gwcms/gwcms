<?php

$debug=Array('mem_use'=>Array(memory_get_usage(true)));


if(file_exists(__DIR__ . '/vendor/autoload.php'))
	require_once __DIR__ . '/vendor/autoload.php';

include "framework/functions.php";
include "framework/gw_autoload.class.php";
include "framework/gw.class.php";

include "config/main.php";

GW_Autoload::init();

register_shutdown_function(['GW_Debug_Helper','errorReport']);



if(phpversion()<'8.0')
	set_error_handler(['GW_Debug_Helper','warningHandler'], E_USER_WARNING);

$proc_timer = new GW_Timer;

GW::init();
