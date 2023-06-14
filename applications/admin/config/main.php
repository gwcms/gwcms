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

if(!GW::s('ADMIN/LANGS'))
	GW::s('ADMIN/LANGS', ['lt','en']);

GW::s('ADMIN/PATH_LOGIN', 'users/login');
GW::s('ADMIN/USER_CLASS', 'GW_User');




$am = GW::s('ADMIN/HOOKS/AFTER_MENU');
$am = is_array($am) ? $am : [];
$am[] = "emails/widgets/progress";


if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_TEST){
	$am[] = "system/systemwidgets/testenv";
}




GW::s('ADMIN/HOOKS/AFTER_MENU', $am);



//siuncia klaida developeriui el pastu arba atvaizduoje ekrane jei pats dev prisijunges
//
//TIK KAI DEVELOPERIS PRISIJUNGES
//d::dumpas(GW::$context->app->user);
//set_error_handler(['GW_Debug_Helper','errrorHandler'], E_WARNING);
//set_error_handler(['GW_Debug_Helper','warningHandler'], E_NOTICE);

//isveda klaida i ekrana
//






