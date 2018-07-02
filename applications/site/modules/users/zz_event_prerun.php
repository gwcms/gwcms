<?php

//suras kokie puslapiai yra priskirti login, logout, register, kur nukreipti po to kai prisilogina

$list = GW_Page::singleton()->getByModulePath('%');

$translate = ['SITE/PATH_REGISTER', 'SITE/PATH_LOGOUT', 'SITE/PATH_LOGIN', 'SITE/PATH_PASSCHANGE', 'SITE/PATH_PROFILE'];



foreach ($translate as $path) {
	GW::s($path, isset($list[GW::s($path)]) ? $list[GW::s($path)] : 'not/found');
}




foreach ($list as $key => $val) {
	GW::s('SITE/PATH_TRANS/' . $key . '/_', $val);
}



if ($this->user) {
	$cnt = GW_Chat_Message::singleton()->count(['to_id=? AND last=1 AND seen=0', $this->user->id]);

	if ($cnt)
		$this->updates_by_path[GW::s('SITE/PATH_TRANS/users/chat/_')] = $cnt;


	$cnt = $this->user->getProfileVisitsCount();

	if ($cnt)
		$this->updates_by_path[GW::s('SITE/PATH_TRANS/users/userslist/profilevisitors/_')] = $cnt;
	
	$cnt = DG_PrivatePhoto_Request::singleton()->count(['to_id=? AND seen=0',$this->user->id]);
	
	if ($cnt)
		$this->updates_by_path[GW::s('SITE/PATH_TRANS/users/userpics/_')] = $cnt;	
	
	
	$new_approves= DG_PrivatePhoto_Request::singleton()->count(['from_id=? AND approved=1 AND approve_seen=0',$this->user->id]);
	
	if ($new_approves)
		$this->updates_by_path[GW::s('SITE/PATH_TRANS/users/userslist/_')] = $new_approves;	
	
	GW::s('DG/NEW_APPROVES', $new_approves);
}

GW::s('SITE/PATH_USER_PROF', $this->buildURI(GW::s('SITE/PATH_TRANS/users/userslist/_') . '/profile', ['id' => '']));

$this->user_cfg = new GW_Config('customers/');
$this->user_cfg->preload('');






if ($this->user_cfg->login_with_fb) {
	

	if(!isset($_SESSION['FBRLH_state_link'])){
		

		$fb = new Facebook\Facebook([
			'app_id' => $this->user_cfg->fb_app_id,
			'app_secret' => $this->user_cfg->fb_app_secret,
			'default_graph_version' => 'v2.5',
		]);

		$helper = $fb->getRedirectLoginHelper();

		//,'user_birthday','user_about_me'
		$permissions = ['email']; // Optional permissions
		$loginUrl = $helper->getLoginUrl($this->buildURI('direct/users/fblogin/login', [], ['absolute' => 1]), $permissions);
		
		$_SESSION['FBRLH_state_link']=$loginUrl;
	}
	
	$this->smarty->assign('login_with_fb_url', $_SESSION['FBRLH_state_link']);
}

//https://www.facebook.com/v2.4/dialog/oauth?client_id=172271779814711&state=9a8a5e796ff3b598acd7321d445e3f6a&response_type=code&sdk=php-sdk-5.1.2&redirect_uri=http%3A%2F%2Fdatinggirls.com%2Fen%2Fdirect%2Fusers%2Ffblogin%2Flogin&scope=email
//https://www.facebook.com/v2.4/dialog/oauth?client_id=885513141537294&state=a54356a82effc0a3e9acc4915f83856b&response_type=code&sdk=php-sdk-5.0.0&redirect_uri=http%3A%2F%2Fipmc.lt%2Fartistdb%2Flt%2Fdirect%2Fusers%2Ffblogin%2Flogin&scope=email"