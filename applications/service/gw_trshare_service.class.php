<?php

//call me example: https://domain.tld/service/trshare/gettr?trkey=REGISTER_EMAIL_NOTE

class GW_TrShare_service extends GW_Common_Service
{


	
	function init()
	{
		parent::init();
		
	}
	
	function checkAuth()
	{
		//($this->checkBasicHTTPAuth())
			return true;
	}
	
	
	function actGetTr()
	{
		
		
		list($mod,$key)=GW_Translation::singleton()->fullkeyToModAndKey($_GET['trkey']);
		$tr = GW_Translation::singleton()->find(['module=? AND `key`=?', $mod, $key]);
		$tr = $tr ? $tr->toArray() : [];
		unset($tr['id']);
		
		return ['result'=>  $tr];
	}
	

	

}
