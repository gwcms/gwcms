<?php

//suras kokie puslapiai yra priskirti login, logout, register, kur nukreipti po to kai prisilogina

$list = GW_Page::singleton()->getByModulePath('articles/articles');

foreach($list as $syspath => $publicpath){
	GW::s('PATH_TRANS/'.$syspath, $publicpath);
}


$this->smarty->assign('announcements_count', GW_Article::singleton()->count('active=1'));