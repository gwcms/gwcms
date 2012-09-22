<?php

//dump($_REQUEST);
//dump($_REQUEST['act']);
//dump($_POST);
//dump($_GET);
//exit;
GW::$auth = new GW_Auth(new GW_User());
if (isset($_REQUEST['pre_act'])){
	
	if ($_REQUEST['pre_act'] == "do_login")
	{
		//dump("DU PRØVER Å LOGGE INN");
		if (isset($_REQUEST['ulogin'])){
			//TODO Perform CHECK against SQL INJECTIONS
			//dump("Brukenavn: " . $_POST['ulogin'][0] . " Passord: " . $_POST['ulogin'][1]);
			//dump($customer_auth);
			$x = GW::$auth->loginPass($_REQUEST['ulogin'][0], $_REQUEST['ulogin'][1]);
			if (GW::$auth->error){
				GW::$request->setErrors(GW::$auth->error);
				GW::$request->jump("bruker");
			}
		}
		else{
			//Display error message.
		}
	}
	
	elseif($_REQUEST['pre_act'] == "do_logout")
	{
		GW::$auth->logout();
		GW::$request->jump();
		//GW::$request->setMessage("/USERS/");
		//dump("Logged out successfully!");
	}
	elseif($_REQUEST['pre_act'] == "do_jump")
	{
		GW::$request->jump();
		
		//GW::$request->setMessage("/USERS/");
		//dump("Logged out successfully!");
	}
	else{
		GW::$request->setErrors(Array("Unknow Command"));
	}
}

GW::$user = GW::$auth->isLogged();
if(GW::$user)
	GW::$user->onRequest();