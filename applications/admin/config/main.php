<?php

GW::s('PATH_LOGIN','users/login');
GW::s('GW_USERZONE_PATH',"usr/");

GW::s('GW_LOGIN_NAME_EXPIRATION','+1 month'); //strtotime format
GW::s('GW_AUTOLOGIN_EXPIRATION', '+2 week'); //strtotime format

$rdir =& GW::s('DIR');
$dir =& $rdir['ADMIN'];
$dir['ROOT']=dirname(__DIR__).'/';
$dir['TEMPLATES']=$dir['ROOT'].'templates/';
$dir['MODULES']=$dir['ROOT'].'modules/';
$dir['LIB']=$dir['ROOT'].'lib/';
$dir['LANG']=$dir['ROOT'].'lang/';
$dir['TEMPLATES'] = $dir['ROOT'].'templates/';




$rdir['AUTOLOAD'][] = $dir['LIB'];
$rdir['AUTOLOAD_RECURSIVE'] = $dir['MODULES'];

GW::s('ADMIN/LANGS', Array('lt'));
GW::s('ADMIN/PATH_LOGIN', 'users/login');








