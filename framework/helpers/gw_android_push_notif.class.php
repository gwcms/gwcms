<?php

class GW_Android_Push_Notif{
	
	static function pushIfNotOnline($user_id)
	{
		$online_if_last_request_newer_than = date('Y-m-d H:i:s', strtotime('-6 minute'));
		
		$extracond = ' AND '.GW_DB::prepare_query(['last_request_time < ?', $online_if_last_request_newer_than]);
			
		if($user = self::userFromUserid($user_id, $extracond))
			return self::push($user);
	}
	
	function userFromUserid($user, $extra_cond='')
	{
		if(is_numeric($user))
			$user = GW_User::singleton()->find(['id=? AND active=1 '.$extra_cond, $user]);
		
		return $user;
	}
	
	/**
	 * 
	 * @param type $user - user_id or user object
	 */
	static function push($user)
	{
		$user = self::userFromUserid($user);
			
		if(!$user)
			return false;
		
		//var_dump($user);
		
		$regids = $user->getExt()->get('android_subscription');
		
		foreach($regids as $idx => $regid)
			if(strpos($regid, 'mozilla.com')!==false){
				unset($regids[$idx]);
				self::pushFirefox($regid);
			}
					
		if(!$regids)
			return false;
		
		//panaikinti indexu tarpus kad eitu paeiliui
		$regids = array_merge($regids);		
		
			
		$api_key = GW_Config::singleton()->get('sys/google_api_access_key');

		$headers = ['Authorization: key=' . $api_key,'Content-Type: application/json'];

		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( ['registration_ids' => $regids] ) );
		$result = curl_exec($ch );
		curl_close( $ch );

		header('Content-type: text/plain');
		
		$data = json_decode($result, true);
		
		self::checkForInvalidRegIds($user, $data, $regids);
		
		GW::$lgr->msg("PUSH_MSGS msg to $user->id (".count($regids).") response: ".$result);
		
		return $data;
	}
	
	function pushFirefox($regid)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $regid );
		curl_setopt($ch, CURLOPT_PUT, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);
		
		//returns empty
	}
	
	static function checkForInvalidRegIds($user, $data, $regids)
	{
		if(isset($data['failure']) && $data['failure']>0 && isset($data['results']))
		{
			foreach($data['results'] as $idx => $info)
			{
				if(isset($info['error']) && $info['error']=='NotRegistered'){
					$user->getExt()->deleteKeyVal('android_subscription', $regids[$idx]);
					GW::$lgr->msg("PUSH_MSGS $user->id removing: ".$regids[$idx]);
				}
			}
		}			
	}
	
	static function getRegistrationId($str)
	{
		return str_replace("https://android.googleapis.com/gcm/send/", "", $str);
	}
}
