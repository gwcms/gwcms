<?php

GW::s('PATH_LOGIN','users/login');
GW::s('GW_USERZONE_PATH',"usr/");

GW::s('GW_LOGIN_NAME_EXPIRATION','+1 month'); //strtotime format
GW::s('GW_AUTOLOGIN_EXPIRATION', '+1 year'); //strtotime format

$rdir =& GW::s('DIR');
$dir =& $rdir['ADMIN'];
$dir['ROOT']=str_replace('\\','/',dirname(__DIR__)).'/';
$dir['TEMPLATES']=$dir['ROOT'].'templates/';
$dir['MODULES']=$dir['ROOT'].'modules/';
$dir['LIB']=$dir['ROOT'].'lib/';
$dir['LANG']=$dir['ROOT'].'lang/';
$dir['TEMPLATES'] = $dir['ROOT'].'templates/';




$rdir['AUTOLOAD'][] = $dir['LIB'];
$rdir['AUTOLOAD_RECURSIVE'] = $dir['MODULES'];

GW::s('ADMIN/LANGS', ['lt','en']);
GW::s('ADMIN/PATH_LOGIN', 'users/login');
GW::s('ADMIN/USER_CLASS', 'GW_User');




$am = GW::s('ADMIN/HOOKS/AFTER_MENU');
$am = is_array($am) ? $am : [];
$am[] = "emails/widgets/progress";

GW::s('ADMIN/HOOKS/AFTER_MENU', $am);



//https://stackoverflow.com/questions/27560361/how-to-test-php-bitwise-function-input-parameters
define('GW_PERM_READ',1);
define('GW_PERM_WRITE',2);
//define('',4); /* then 8, 16, 32, etc... */

