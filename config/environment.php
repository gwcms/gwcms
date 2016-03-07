<?php

define('GW_ENV_DEV',1);
define('GW_ENV_TEST',2);
define('GW_ENV_PROD',3);




if(__DIR__=='/var/www/prodpath'){
	GW::s('PROJECT_ENVIRONMENT', GW_ENV_PROD);

	
	//GW::s("APP_BACKGROUND_REQ_TYPE", 'localhost_base'); // can be force_http or localhost_base (past one requires valid SITE_LOCAL_URL)
	//GW::s("SITE_LOCAL_URL",'http://localhost/projectpathunder_localhost/');
	//GW::s("SITE_URL",'https://project.com/');
	
}elseif(__DIR__=='/var/www/testpath'){
	GW::s('PROJECT_ENVIRONMENT', GW_ENV_TEST);
	//GW::s('DB/UPHD', 'user:pass@host/dbname');
}else{
	
	GW::s('PROJECT_ENVIRONMENT', GW_ENV_DEV);
	//GW::s('DB/UPHD', 'user:pass@host/dbname');
}
