<?php
//testas123

$debug=Array('mem_use'=>Array(memory_get_usage(true)));



include "framework/functions.php";
include "framework/gw_autoload.class.php";
include "framework/gw.class.php";

include "config/main.php";

GW_Autoload::init();

$proc_timer = new GW_Timer;

GW::init();
GW::request();





GW_Debug_Helper::show_debug_info();


echo "<!--process time ".$proc_timer->stop(5)."-->";
