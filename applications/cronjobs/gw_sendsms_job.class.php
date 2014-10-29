<?php

class GW_SendSms_Job extends GW_Generic_Job
{
	

	
	function process()
	{
		GW_Application::innerProcessStatic("sms/mass?act=do:updateBulk&just_action=1");
		
		$this->log("OK");
	}
}