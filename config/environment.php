<?php

define('GW_ENV_DEV',1);
define('GW_ENV_TEST',2);
define('GW_ENV_PROD',3);
define('GW_ENV_DEMO1', 11);



GW::s("APP_BACKGROUND_REQ_TYPE", 'force_http'); // can be force_http or localhost_base (past one requires valid SITE_LOCAL_URL)
GW::s('PROJECT_FAVICO_ARGS', 'text=GW&text2=CMS&fs=50&font=EncodeSansNarrow-ExtraLight.ttf');


GW::s('DB/INIT_SQLS',"SET SESSION sql_mode = '';"); // automatycaly turned to strict in mysql 5.7 which causes default errors and others

$hostname = trim(file_get_contents("/etc/hostname"));



function initEnviroment($environmentid)
{
	switch($environmentid){
		case GW_ENV_DEV:
			include $GLOBALS['dir']['ROOT'].'config/db.php';
			GW::s('PROJECT_FAVICO_ARGS', GW::s('PROJECT_FAVICO_ARGS').'&color=ff6600');
			

			GW::$globals['version'] = trim(file_get_contents(GW::s('DIR/ROOT').'.git/FETCH_HEAD'));
		break;
	
		case GW_ENV_PROD:
			include $GLOBALS['dir']['ROOT'].'config/db_prod.php';
			
			GW::s('PROJECT_ENVIRONMENT', GW_ENV_PROD);
			

			GW::s("APP_BACKGROUND_REQ_TYPE", 'localhost_base'); // can be force_http or localhost_base (past one requires valid SITE_LOCAL_URL)
			GW::s("SITE_LOCAL_URL",'http://gwcms/');

			GW::s("SITE_URL",'https://cms.gw.lt/');
			
			//db_sync tool ant others
			GW::s("SSH_USERHOST",'root@gw.lt');
			
			//reset css,js caches
			GW::$globals['version'] = trim(file_get_contents(GW::s('DIR/ROOT').'version'));
			
			GW::s('PROJECT_FAVICO_ARGS', GW::s('PROJECT_FAVICO_ARGS').'&color=000099');
		break;
		
	}
	
	GW::s('DB/INIT_SQLS',"SET SESSION sql_mode = '';");// automatycaly turned to strict in mysql 5.7 which causes default errors and others
	
}

$env_host_map = ['wdmpc'=>GW_ENV_DEV, 'whatever'=>GW_ENV_TEST, 'odroidXU4'=>GW_ENV_PROD];
GW::s('PROJECT_ENVIRONMENT', $env_host_map[$hostname]?? GW_ENV_DEV);

echo "HN $hostname; env: ".GW::s('PROJECT_ENVIRONMENT').';';

initEnviroment(GW::s('PROJECT_ENVIRONMENT'));

GW::$globals['version_short'] = substr(GW::$globals['version'],0,8);