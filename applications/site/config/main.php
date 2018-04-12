<?php

GW::s('PATH_LOGIN','sys/login');
GW::s('PATH_LOGOUT','usr/user/logout');

$rdir =& GW::s('DIR');
$dir =& $rdir['SITE'];
$dir['ROOT']=dirname(__DIR__).'/';
$dir['TEMPLATES']=$dir['ROOT'].'templates/';
$dir['MODULES']=$dir['ROOT'].'modules/';
$dir['LIB']=$dir['ROOT'].'lib/';
$dir['LANG']=$dir['ROOT'].'lang/';
$dir['TEMPLATES'] = $dir['ROOT'].'templates/';

$rdir['ADMIN']['ROOT']=$rdir['APPLICATIONS'].'admin/';
$rdir['ADMIN']['MODULES']=$rdir['ADMIN']['ROOT'].'modules/';

$rdir['AUTOLOAD'][] = $dir['LIB'];
$rdir['AUTOLOAD_RECURSIVE'] = $rdir['ADMIN']['MODULES'];


GW::s('SITE/USERZONE_PATH','usr/');
GW::s('SITE/USER_PASS_CHANGE_PAGE', "sys/user/passchange?id1=%s&id2=%s");
GW::s('SITE/PATH_LOGOUT','usr/user/logout');

GW::s('SITE/USER_CLASS', 'GW_Customer');

GW::s('SITE/TITLE','Žinutės');
