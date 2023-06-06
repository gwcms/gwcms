<?php

class GW_Bot_Detect
{

	static function process(){

		if(
			stripos($_SERVER['HTTP_USER_AGENT'] ?? false, 'bot')!==false && 
			GW::s('BOT_SEND_TO_MIRROR') && 
			(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV || GW::s('PROJECT_ENVIRONMENT') == GW_ENV_PROD)
		){

			initEnviroment(GW_ENV_TEST);
			GW_Proxy_Site::redirect(GW::s('SITE_URL'));
			exit;
		}
	}

	
	static function stats(){
		
		
		$user_agent = mb_substr($_SERVER['HTTP_USER_AGENT'], 0, 100);
		$date= date('Y-m-d');
		$speed = GW::$globals['proc_timer']->stop(1);
		
		if(isset($_GET['bottest']))
			d::dumpas([$user_agent, $speed]);
		
		$aff = GW::db()->increase2("request_by_user_agent", GW_DB::prepare_query(['date=? AND user_agent=?',$date,$user_agent]),'cnt',1,'speed', $speed);;
		if(!$aff)
			GW::db()->insert("request_by_user_agent", ['date'=>$date,'user_agent'=>$user_agent, 'cnt'=>1]);
	}
	
}