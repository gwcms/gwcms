<?php

define('GW_ENV_DEV',1);
define('GW_ENV_TEST',2);
define('GW_ENV_PROD',3);


GW::s("APP_BACKGROUND_REQ_TYPE", 'force_http'); // can be force_http or localhost_base (past one requires valid SITE_LOCAL_URL)
GW::s('PROJECT_FAVICO_ARGS', 'text=GW&text2=CMS&fs=50&font=EncodeSansNarrow-ExtraLight.ttf');


GW::s('DB/INIT_SQLS',"SET SESSION sql_mode = '';"); // automatycaly turned to strict in mysql 5.7 which causes default errors and others

$GLOBALS['version'] = trim(file_get_contents(GW::s('ROOT_DIR').'.git/ORIG_HEAD'));
$GLOBALS['version_short'] = substr($GLOBALS['version'],0,8);

if(__DIR__=='/var/www/gwcms/config'){
	GW::s('PROJECT_ENVIRONMENT', GW_ENV_PROD);
	GW::s('PROJECT_FAVICO_ARGS', GW::s('PROJECT_FAVICO_ARGS').'&color=000099');
	
	$GLOBALS['version'] = is_file(__DIR__.'/version') ? file_get_contents(__DIR__.'/version') : -1;
	
	include $dir['ROOT'].'config/db.php';
	//GW::s("APP_BACKGROUND_REQ_TYPE", 'localhost_base'); // can be force_http or localhost_base (past one requires valid SITE_LOCAL_URL)
	//GW::s("SITE_LOCAL_URL",'http://localhost/projectpathunder_localhost/');
	//GW::s("SITE_URL",'https://project.com/');
	
}elseif(__DIR__=='/var/www/testpath'){
	GW::s('PROJECT_ENVIRONMENT', GW_ENV_TEST);
	//GW::s('DB/UPHD', 'user:pass@host/dbname');
}else{
	GW::s('PROJECT_FAVICO_ARGS', GW::s('PROJECT_FAVICO_ARGS').'&color=ff6600');
	
	GW::s('PROJECT_ENVIRONMENT', GW_ENV_DEV);
	//GW::s('DB/UPHD', 'user:pass@host/dbname');
	
}

