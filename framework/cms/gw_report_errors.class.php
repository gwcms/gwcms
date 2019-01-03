<?php

class GW_Report_Errors
{

	static function msg($subject, $body)
	{
		mail('errors@gw.lt', $subject, $body);

		GW::getInstance('GW_Message')->msg(9, $subject, $body, 1);
	}
	
	static function adminNotify($msg)
	{
		GW_WebSocket_Helper::notifyUser('wdm', ['action'=>'notification', 'text'=> $msg ]);
	}
}
