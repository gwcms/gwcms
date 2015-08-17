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

include "framework/functions.php";
include "framework/gw_autoload.class.php";
include "framework/gw.class.php";

include "config/main.php";

GW_Autoload::init();

$proc_timer = new GW_Timer;

GW::init();
