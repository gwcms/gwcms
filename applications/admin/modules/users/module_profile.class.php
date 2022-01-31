<?php


class Module_Profile extends GW_Module
{	
	function init()
	{
		//profile modulis public, su salyga yra prisilogines vartotojas
		if(!$this->app->user)
			$this->app->jump(Navigator::getBase());
		
		
		$this->model = new GW_User();
		
		parent::init();
	}

	
	function viewDefault()
	{
		return ['item'=>$this->app->user];		
	}

	function doSave()
	{
		$this->viewDefault();
	}
	
	function viewLogout()
	{
		$this->app->auth->logout();
		$this->app->jump(GW::s('ADMIN/PATH_LOGIN'));
	}
	
	function doUpdateMyPass()
	{
		$vals=$_REQUEST['item'];
		
		$item =& $this->app->user;
		$item->setValues($vals);		
		
		$item->setValidators('change_pass_check_old');
		
		if(!$item->validate()){
			$this->setItemErrors($item);
			
			$this->processView('default');
			exit;	
		}else{
			$item->setValidators(false);
			if($item->update(Array('pass')))
				$this->setPlainMessage($this->lang['PASS_UPDATED']);
		}
		
		$this->jump();
	}
	
	function doUpdateMyProfile()
	{
		$vals=$_REQUEST['item'];
		
		$fields=Array('name','surname','email');
		
		$item =& $this->app->user;
		$item->setValues($vals);	
		$item->setValidators('update');
			
		
		if(!$item->validate()){
			$this->setItemErrors($item);
			
			$this->processView('default');
			exit;	
		}else{
			if($item->update($fields))
				$this->setPlainMessage($this->smarty->_tpl_vars['lang']['UPDATE_SUCCESS']);
		}
		
		$this->jump();		
	}
	
	function doSwitchUserReturn()
	{
		$this->app->auth->switchUserReturn();
		$this->jump();
	}

	function doStoreSubscription()
	{
		$subscription = GW_Android_Push_Notif::getRegistrationId($_GET["subscription"]);
		$new = $this->app->user->getExt()->insertIfNotExists('android_subscription', $subscription);

		echo "New: $new";
		echo $subscription;
		echo "\nOK";
		exit;
	}
	
	function doTestSubscription()
	{
		
		GW_Message::singleton()->message([
			'to'=>$this->app->user->id,
			'subject'=>"Testing push", 
			'message'=>"Hi, If you see this text - it workss!",
			'level'=>15,
			'group'=>false
		]);		
		
		if(isset($_GET['debug'])){
			$data = GW_Android_Push_Notif::push($this->app->user);
			d::ldump(json_encode($data, JSON_PRETTY_PRINT));
		}

		exit;
			
	}
	
	function doLinkWithFb()
	{
		$comebackurlAuthgw = $this->app->buildURI(false,['act'=>'doFinishLinkWithFb'],['absolute'=>1]);
		$req_id = GW_String_Helper::getRandString(25);
		$_SESSION['adm_auth_gw_lt_req_id']=$req_id;
			
		session_commit();
		session_write_close();
		$auth_gw_url = GW::s('GW_FB_SERVICE')."?request_id=".$req_id."&redirect2=". urlencode($comebackurlAuthgw);
		header('Location: '.$auth_gw_url);		
		exit;			
	}
	
	function doUnLinkWithFb()
	{
		$this->app->user->ext->adminfbid = '';
		$this->setMessage('Facebook account Unlinked');
		$this->jump();
	}
	
	function doFinishLinkWithFb()
	{	
		$req_id = $_SESSION['adm_auth_gw_lt_req_id'];
		$dat = file_get_contents(GW::s('GW_FB_SERVICE').'?get_response='.$req_id);
		$dat = json_decode($dat);
		
		$this->setMessage('Link success, now you can login using Facebook');
		$this->app->user->ext->adminfbid = $dat->id;
		$this->jump();
	}
	
	function doGetWsTempConfig()
	{
		$data = GW_WebSocket_Helper::getFrontConfig($this->app->user, true);
		echo json_encode($data);
		exit;
	}	
	
	
	function doSetI18nExtState()
	{
		$item =& $this->app->user;
		$lns = $item->i18next_lns;
		$ln = $_GET['ln'];
		if(!in_array($ln,GW::s('i18nExt')))
			die('no hacking');
		
		
		$lns[$ln]=(int)$_GET['state'];
		$item->set('ext/i18next_lns',json_encode($lns));
		$this->setMessage(GW::l('/g/ACTION').' '.GW::l('/m/VIEWS/doSetI18nExtState').' <b>'.GW::l("/g/LANG/$ln").'</b> '.($lns[$ln]?'ON':'OFF'), ['float'=>1]);
		
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}
}

?>
