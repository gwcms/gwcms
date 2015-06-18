<?php



$dir =& GW::s('DIR');
$dir['ROOT']=dirname(__DIR__).'/';
$dir['APPLICATIONS']=$dir['ROOT'].'applications/';

$dir['LIB']=$dir['ROOT'].'framework/';
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
GW::s('SYS_VERSION', '2.1');


include $dir['ROOT'].'config/project.php';

/*
echo "<pre>";
print_r(GW::$settings);
exit;

*/
