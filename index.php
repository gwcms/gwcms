<?php


include __DIR__.'/init_basic.php';
GW::request();





GW_Debug_Helper::show_debug_info();



echo "<!--err:".error_reporting()."-->";
if(!GW::s('NO_PROCESS_TIME'))
	echo "<!--process time ".$proc_timer->stop(5)."-->";

//aaa
