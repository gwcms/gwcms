<?php

$debug=Array('mem_use'=>Array(memory_get_usage(true)));


if(file_exists(__DIR__ . '/vendor/autoload.php'))
	require_once __DIR__ . '/vendor/autoload.php';

include "framework/functions.php";
include "framework/gw_autoload.class.php";
include "framework/gw.class.php";

include "config/main.php";

GW_Autoload::init();

//EXPERIMENTAL php8.1 labai daug erroru meto is po smarcio reiktu protingesnio varianto
if(phpversion()<'8.0'){
	register_shutdown_function(['GW_Debug_Helper','errorReport']);
}


//siuncia klaida developeriui el pastu arba atvaizduoje ekrane jei pats dev prisijunges
//
set_error_handler(['GW_Debug_Helper','warningHandler'], E_USER_WARNING | E_NOTICE);

//isveda klaida i ekrana
//
set_error_handler(['GW_Debug_Helper','errrorHandler'], E_WARNING);



$GLOBALS['proc_timer'] = new GW_Timer;

GW::init();
