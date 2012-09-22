<?php

GW::$auth = new GW_Auth(new GW_ADM_User());
GW::$user = GW::$auth->isLogged();

if(!$do_not_register_request && GW::$user)
	GW::$user->onRequest();
	