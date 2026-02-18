<?php

define('GW_MSG_SUCC',0);
define('GW_MSG_WARN',1);
define('GW_MSG_ERR',2);
define('GW_MSG_INFO',3);


$dir =& GW::s('DIR');
$dir['ROOT']=str_replace('\\','/',dirname(__DIR__)).'/';
$dir['APPLICATIONS']=$dir['ROOT'].'applications/';

$dir['LIB']=$dir['ROOT'].'framework/';
$dir['VENDOR']=$dir['ROOT'].'vendor/';
$dir['PEAR']=$dir['LIB'].'pear/';
$dir['REPOSITORY']=$dir['ROOT'].'repository/';




$dir['SYS_REPOSITORY']=$dir['REPOSITORY'].'.sys/';
	$dir['TEMPLATES_C']=$dir['SYS_REPOSITORY'].'templates_c/';
	$dir['LOGS']=$dir['SYS_REPOSITORY'].'logs/';
	$dir['SYS_FILES']=$dir['SYS_REPOSITORY'].'files/';
	$dir['SYS_IMAGES']=$dir['SYS_REPOSITORY'].'images/';
	$dir['SYS_IMAGES_CACHE']=$dir['SYS_IMAGES'].'cache/';	
	$dir['LANG_CACHE']=$dir['SYS_REPOSITORY'].'cache/lang/';
	$dir['TEMP']=$dir['SYS_REPOSITORY'].'temp/';


$dir['AUTOLOAD'][] = $dir['LIB'];

			
$adir =& $dir["ADMIN"];
$adir["ROOT"] = $dir['APPLICATIONS'].'admin/';
$adir['MODULES']=$adir['ROOT'].'modules/';
$adir['LANG']=$adir['ROOT'].'lang/';


			
GW::s('DEFAULT_APPLICATION','SITE');		
GW::s('LANGS', Array('lt','en'));
GW::s('i18nExt', []);

define('GW_USER_SYSTEM_ID', 1);
define('GW_GENERIC_ERROR', 100);

//padaryt vienodus jei reikia kad administravimo vartotojai butu priloginti svetaineje
GW::s('ADMIN/AUTH_SESSION_KEY',"cms_auth");
GW::s('SITE/AUTH_SESSION_KEY',"site_auth");

GW::s('GW_CMS_VERSION', '4.6');
GW::s('SMARTY_ERROR_LEVEL', E_ALL & ~E_NOTICE & ~E_DEPRECATED);
GW::s('SMARTY_ERROR_LEVEL8', E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);



date_default_timezone_set('Europe/Vilnius');

//reachable via admin panel System / Nustatymai or url admin/lt/system/cfg
GW::s('BOT_DETECT_IP_QUOTA_FOREIGN', 300);


include $dir['ROOT'].'config/environment.php';
include $dir['ROOT'].'config/project.php';



$env_title = [GW_ENV_DEV=>'[D] ', GW_ENV_TEST=>'[T] ', GW_ENV_PROD=>'', GW_ENV_DEMO1=>'[DEMO]'];
GW::s('SITE/TITLE_MARK', $env_title[GW::s('PROJECT_ENVIRONMENT')]);

//whereis php
//GW::s('PHP_CLI_LOCATION', '/usr/local/bin/php');
GW::s('PHP_CLI_LOCATION', '/usr/bin/php');

//https://stackoverflow.com/questions/27560361/how-to-test-php-bitwise-function-input-parameters
define('GW_PERM_READ',1);
define('GW_PERM_WRITE',2);
define('GW_PERM_OPTIONS',4);
define('GW_PERM_REMOVE',8);

//84.15.236.87 t20  /  88.223.24.240 //u1
GW::s('OFFICE_IP_ADDR', ['82.135.242.67','88.223.24.240', '192.168.1.254', '127.0.0.1']);

GW::s('IMAGE_THUMB_FORMAT', 'webp'); //prev version: auto


if(isset($_SERVER['REMOTE_ADDR'])){
	
	if( in_array($_SERVER['REMOTE_ADDR'], GW::s('OFFICE_IP_ADDR'))){
		GW::s('DEVELOPER_PRESENT',1);
	}	
}

/*bendrinio naudojimo*/
GW::s("STATIC_EXTERNAL_ASSETS", "//serv133.voro.lt/html/");

//beter lower on site and bigger for admin panel - large import / recalc actions
GW::s('ADMIN/APP_MEM_LIM', "400M");

//define('',4); /* then 8, 16, 32, etc... */

/*
echo "<pre>";
print_r(GW::$settings);
exit;

*/

