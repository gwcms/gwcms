<?php



include __DIR__.'/init_basic.php';


if(stripos($_SERVER['HTTP_USER_AGENT'] ?? false, 'bot')!==false && GW::s('BOT_SEND_TO_MIRROR') && GW::s('PROJECT_ENVIRONMENT') == GW_ENV_PROD){
	
	initEnviroment(GW_ENV_TEST);
	GW_Proxy_Site::redirect(GW::s('SITE_URL'));
	exit;
}

GW::request();





GW_Debug_Helper::show_debug_info();


if(isset($GLOBALS['netbeansinitrequest']))
	GW_Debug_Helper::openInNetBeans();
	
	
/*
echo "<!--err:".error_reporting()."-->";
if(!GW::s('NO_PROCESS_TIME'))
	echo "<!--process time ".GW::$globals['proc_timer']->stop(5)."-->";
*/
//aaa
