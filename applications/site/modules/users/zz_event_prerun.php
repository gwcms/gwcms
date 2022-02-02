<?php

GW::s('version2022',1);


if($this->user && $this->user->isRoot()){
	GW::s('version2022',1);
}

if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']??'') != 'xmlhttprequest'){
	if($this->user)
		$this->subProcessPath('users/users/noview',['act'=>'doCheckProfileMissingInfo']);
}

	
