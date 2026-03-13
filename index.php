<?php

include __DIR__.'/init_basic.php';

GW_Bot_Detect::recaptcha();
GW_Bot_Detect::botRedirect();
GW_Bot_Detect::process();

/*
 * brutualus atjungimas jei eina atpazint is access.log kai srautas nera tipinis lankytojas 
if(isset($_GET['redirmirror'])){
	header('HTTP/1.1 504 Service Temporarily Unavailable');
	header('Status: 504 Service Temporarily Unavailable');
	header('Retry-After: 300');//300 seconds
	exit;
}
*/

GW::request();


GW_Debug_Helper::show_debug_info();


if(isset($GLOBALS['netbeansinitrequest']))
	GW_Debug_Helper::openInNetBeans();
	
	
//if(GW::s('BOT_SEND_TO_MIRROR_STATS'))
	GW_Bot_Detect::stats();



/*
echo "<!--err:".error_reporting()."-->";
if(!GW::s('NO_PROCESS_TIME'))
	echo "<!--process time ".GW::$globals['proc_timer']->stop(5)."-->";
*/
//aaa
