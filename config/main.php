<?php

define('GW_MSG_SUCC',0);
define('GW_MSG_WARN',1);
define('GW_MSG_ERR',2);
define('GW_MSG_INFO',3);


$dir =& GW::s('DIR');
$dir['ROOT']=dirname(__DIR__).'/';
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

			


		
//used to send mail through
//GW::$static_conf['REMOTE_SERVICES']['MAIL1'] = 'http://uostas.net/services/mail.php?key=fh5ad2fg1ht4a6s5dg1hy4a5d4fg';	
GW::s('DEFAULT_APPLICATION','SITE');		
GW::s('LANGS', Array('lt','en'));

define('GW_USER_SYSTEM_ID', 1);
define('GW_GENERIC_ERROR', 100);

//padaryt vienodus jei reikia kad administravimo vartotojai butu priloginti svetaineje
define("AUTH_SESSION_KEY", "cms_auth");
define("PUBLIC_AUTH_SESSION_KEY", "site_auth");

GW::s('GW_CMS_VERSION', '3.0');

include $dir['ROOT'].'config/project.php';
include $dir['ROOT'].'config/environment.php';


$env_title = [GW_ENV_DEV=>'[D] ', GW_ENV_TEST=>'[T] ', GW_ENV_PROD=>''];
GW::s('SITE/TITLE_MARK', $env_title[GW::s('PROJECT_ENVIRONMENT')]);

//whereis php
GW::s('PHP_CLI_LOCATION', '/usr/local/bin/php');

/*
echo "<pre>";
print_r(GW::$settings);
exit;

*/
