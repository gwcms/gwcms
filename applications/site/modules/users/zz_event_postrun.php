<?php

//suras kokie puslapiai yra priskirti login, logout, register, kur nukreipti po to kai prisilogina

$list = GW_Page::singleton()->getByModulePath('users/%');

$translate = ['SITE/PATH_USERZONE', 'SITE/PATH_REGISTER', 'SITE/PATH_LOGOUT', 'SITE/PATH_LOGIN'];


foreach($translate as $path){
	GW::s($path, isset($list[GW::s($path)]) ? $list[GW::s($path)] : 'not/found');
}




//GW::s();
