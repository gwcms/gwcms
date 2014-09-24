<?php


define('GW_ENV_DEV',1);
define('GW_ENV_TEST',2);
define('GW_ENV_PROD',3);


if(file_exists(__DIR__.'/GW_ENV_PROD')){
	GW::$static_conf['PROJECT_ENVIRONMENT']=GW_ENV_PROD;
}elseif(file_exists(__DIR__.'/GW_ENV_TEST')){
	GW::$static_conf['PROJECT_ENVIRONMENT']=GW_ENV_TEST;
}else{
	GW::$static_conf['PROJECT_ENVIRONMENT']=GW_ENV_DEV;
}

GW::$static_conf['GW_USERZONE_PATH']="usr/";
GW::$static_conf['GW_SITE_PATH_LOGIN']='sys/login';
GW::$static_conf['GW_SITE_PATH_LOGOUT']='usr/user/logout';

GW::$static_conf['GW_LOGIN_NAME_EXPIRATION']= '+1 month'; //strtotime format
GW::$static_conf['GW_AUTOLOGIN_EXPIRATION']= '+2 week'; //strtotime format


GW::$dir['ROOT']=dirname(dirname(__DIR__)).'/';		
	GW::$dir['ADMIN']=dirname(__DIR__).'/';
		GW::$dir['LIB']=GW::$dir['ADMIN'].'lib/';
		GW::$dir['PEAR']=GW::$dir['LIB'].'pear/';
		GW::$dir['REPOSITORY']=GW::$dir['ADMIN'].'repository/';
		GW::$dir['TEMPLATES']=GW::$dir['ADMIN'].'templates/';
		GW::$dir['MODULES']=GW::$dir['ADMIN'].'modules/';	
		GW::$dir['SYS_REPOSITORY']=GW::$dir['REPOSITORY'].'.sys/';
			GW::$dir['TEMPLATES_C']=GW::$dir['SYS_REPOSITORY'].'templates_c/';
			GW::$dir['LOGS']=GW::$dir['SYS_REPOSITORY'].'logs/';
			GW::$dir['SYS_FILES']=GW::$dir['SYS_REPOSITORY'].'files/';
			GW::$dir['SYS_IMAGES']=GW::$dir['SYS_REPOSITORY'].'images/';
			GW::$dir['SYS_IMAGES_CACHE']=GW::$dir['SYS_IMAGES'].'cache/';	
			GW::$dir['LANG_CACHE']=GW::$dir['SYS_REPOSITORY'].'cache/lang/';
			GW::$dir['TEMP']=GW::$dir['SYS_REPOSITORY'].'temp/';
			
	GW::$dir['PUB']=GW::$dir['ROOT'].'public/';
		GW::$dir['PUB_LIB']=GW::$dir['PUB'].'lib/';
		GW::$dir['PUB_TEMPLATES']=GW::$dir['PUB'].'templates/';
		GW::$dir['PUB_MODULES']=GW::$dir['PUB'].'modules/';
		GW::$dir['PUB_LANG']=GW::$dir['PUB'].'lang/';
		GW::$dir['PUB_TEMPLATES_C']=GW::$dir['SYS_REPOSITORY'].'templates_c/public/';	

		
//used to send mail through
GW::$static_conf['REMOTE_SERVICES']['MAIL1'] = 'http://uostas.net/services/mail.php?key=fh5ad2fg1ht4a6s5dg1hy4a5d4fg';	
		
include GW::$dir['ADMIN'].'config/project_name.php';
include GW::$dir['ADMIN'].'config/project_'.strtolower(GW::$static_conf['PROJECT_NAME']).'.php';

//load environment vars
$tmp1 =& GW::$static_conf['PROJECT_ENVIRONMENT'];
$tmp2 =& GW::$static_conf['ENVVARS'][$tmp1];

if($tmp1 > 1 && is_array($tmp2))
	GW::$static_conf=array_merge(GW::$static_conf, $tmp2);



GW::$static_conf['GW_SITE_TITLE']="Masinės žinutės";