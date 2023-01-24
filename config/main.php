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


		
//used to send mail through
//GW::$static_conf['REMOTE_SERVICES']['MAIL1'] = 'http://uostas.net/services/mail.php?key=fh5ad2fg1ht4a6s5dg1hy4a5d4fg';	
GW::s('DEFAULT_APPLICATION','SITE');		
GW::s('LANGS', Array('lt','en'));
GW::s('i18nExt', []);

define('GW_USER_SYSTEM_ID', 1);
define('GW_GENERIC_ERROR', 100);

//padaryt vienodus jei reikia kad administravimo vartotojai butu priloginti svetaineje
GW::s('ADMIN/AUTH_SESSION_KEY',"cms_auth");
GW::s('SITE/AUTH_SESSION_KEY',"site_auth");

GW::s('GW_CMS_VERSION', '3.7');
GW::s('GW_LANG_SEL_BY_GEOIP',0);
GW::s('SMARTY_ERROR_LEVEL', E_ALL & ~E_NOTICE & ~E_DEPRECATED);

include $dir['ROOT'].'config/environment.php';
include $dir['ROOT'].'config/project.php';



$env_title = [GW_ENV_DEV=>'[D] ', GW_ENV_TEST=>'[T] ', GW_ENV_PROD=>''];
GW::s('SITE/TITLE_MARK', $env_title[GW::s('PROJECT_ENVIRONMENT')]);

//whereis php
//GW::s('PHP_CLI_LOCATION', '/usr/local/bin/php');
GW::s('PHP_CLI_LOCATION', '/usr/bin/php');

//https://stackoverflow.com/questions/27560361/how-to-test-php-bitwise-function-input-parameters
define('GW_PERM_READ',1);
define('GW_PERM_WRITE',2);
define('GW_PERM_OPTIONS',4);
define('GW_PERM_REMOVE',8);

GW::s('OFFICE_IP_ADDR', '84.15.236.87');


//define('',4); /* then 8, 16, 32, etc... */

/*
echo "<pre>";
print_r(GW::$settings);
exit;

*/
