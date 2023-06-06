<?php

class GW_Bot_Detect
{

	static function process(){

		$ua  = $_SERVER['HTTP_USER_AGENT'] ?? false;
		if(
			GW::s('BOT_SEND_TO_MIRROR') && 
			(
				stripos($ua, 'bot')!==false  ||
				stripos($ua, 'spider')!==false || 
				stripos($ua, 'scrap')!==false
			) && 
			(
				GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV || 
				GW::s('PROJECT_ENVIRONMENT') == GW_ENV_PROD
			)
		){

			initEnviroment(GW_ENV_TEST);
			GW_Proxy_Site::redirect(GW::s('SITE_URL'));
			exit;
		}
	}

	
	static function increase2($table, $where, $field, $x = 1, $field2, $y=1, $nodie = false)
	{
		$query = "UPDATE $table SET `$field` = `$field` + $x, `$field2` = `$field2` + $y WHERE $where";

		GW::db()->query($query, $nodie);

		return GW::db()->affected();
	}	
	
	
	static function stats(){
		
		
		$user_agent = mb_substr($_SERVER['HTTP_USER_AGENT'], 0, 100);
		$date= date('Y-m-d');
		$speed = GW::$globals['proc_timer']->stop(1);
		
		if(isset($_GET['bottest']))
			d::dumpas([$user_agent, $speed]);
		
		$aff = self::increase2("request_by_user_agent", GW_DB::prepare_query(['date=? AND user_agent=?',$date,$user_agent]),'cnt',1,'speed', $speed);;
		
		
		if(!$aff)
			GW::db()->insert("request_by_user_agent", ['date'=>$date,'user_agent'=>$user_agent, 'cnt'=>1]);
		
		if($speed>3){
			
			GW::db()->insert("request_slow", ['url'=>$_SERVER['REQUEST_URI'],'ip'=>$_SERVER['REMOTE_ADDR'],'user_agent'=>$user_agent]);
		}
	}
	
}