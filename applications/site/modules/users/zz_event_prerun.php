<?php

GW::s('version2022',1);

$list = GW_Page::singleton()->getByModulePath('%');

if($this->user && $this->user->isRoot()){
	GW::s('version2022',1);
}

if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']??'') != 'xmlhttprequest'){
	if($this->user)
		$this->subProcessPath('users/users/noview',['act'=>'doCheckProfileMissingInfo']);
}

//$this->user_updates['new_messages']  = $this->user->calcNewMessages();


/*
 * code sample
if ($this->user) {
	
	$this->notification_id_by_path=[
	    GW::s('SITE/PATH_TRANS/users/chat/_') => 'new_messages',
	    GW::s('SITE/PATH_TRANS/users/userslist/profilevisitors/_') => 'new_profile_visits',
	    GW::s('SITE/PATH_TRANS/users/userpics/_') => 'new_private_photo_request',
	    GW::s('SITE/PATH_TRANS/users/userslist/_') => 'new_private_photo_approves'
	];
	
	
	$this->user_updates['new_messages']  = $this->user->calcNewMessages();
	$this->user_updates['new_profile_visits']  = $this->user->calcNewProfileVisits();
	$this->user_updates['new_private_photo_request'] = $this->user->calcNewPrivatePhotoReq();	
	$this->user_updates['new_private_photo_approves'] = $this->user->calcNewApproves();	
	
}



GW::s('SITE/PATH_USER_PROF', $this->buildURI(GW::s('SITE/PATH_TRANS/users/userslist/_') . '/profile', ['id' => '']));
*/





