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
		
		//$data = GW_Android_Push_Notif::push($this->app->user);
		
		//echo json_encode($data, JSON_PRETTY_PRINT);
		

		exit;
			
	}
		
}

?>
