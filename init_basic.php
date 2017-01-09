<?php

$debug=Array('mem_use'=>Array(memory_get_usage(true)));

error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT);


/*
register_shutdown_function('shutdown');

function shutdown()
{
  if(!is_null($e = error_get_last()))
  {
    print "<pre>E:\n\n". print_r($e,true);
  }
}
*/
$GLOBALS['version'] = is_file(__DIR__.'/version') ? file_get_contents(__DIR__.'/version') : -1;

if(file_exists(__DIR__ . '/vendor/autoload.php'))
	require_once __DIR__ . '/vendor/autoload.php';

include "framework/functions.php";
include "framework/gw_autoload.class.php";
include "framework/gw.class.php";

include "config/main.php";

GW_Autoload::init();

$proc_timer = new GW_Timer;

GW::init();
