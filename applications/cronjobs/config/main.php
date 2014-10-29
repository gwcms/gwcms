<?php

$rdir =& GW::s('DIR');
$dir =& $rdir['CRONJOBS'];
$dir['ROOT']=dirname(__DIR__).'/';
$dir['LIB']=$dir['ROOT'].'lib/';


$rdir['AUTOLOAD'][] = $dir['ROOT'];
$rdir['AUTOLOAD'][] = $dir['LIB'];



