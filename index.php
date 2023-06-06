<?php



include __DIR__.'/init_basic.php';

GW_Bot_Detect::process();

GW::request();





GW_Debug_Helper::show_debug_info();


if(isset($GLOBALS['netbeansinitrequest']))
	GW_Debug_Helper::openInNetBeans();
	
	
if(GW::s('BOT_SEND_TO_MIRROR'))
	GW_Bot_Detect::stats();
/*
echo "<!--err:".error_reporting()."-->";
if(!GW::s('NO_PROCESS_TIME'))
	echo "<!--process time ".GW::$globals['proc_timer']->stop(5)."-->";
*/
//aaa
