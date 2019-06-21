<?php



$rdir =& GW::s('DIR');
$dir =& $rdir['SITE'];

$rdir['ADMIN']['ROOT']=$rdir['APPLICATIONS'].'admin/';
$rdir['ADMIN']['MODULES']=$rdir['ADMIN']['ROOT'].'modules/';

$rdir['AUTOLOAD'][] = $dir['LIB'];
$rdir['AUTOLOAD_RECURSIVE'] = $rdir['ADMIN']['MODULES'];


