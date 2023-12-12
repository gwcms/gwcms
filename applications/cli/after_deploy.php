#!/usr/bin/php
<?php

chdir(__DIR__.'/../../');
include_once __DIR__.'/../../init_basic.php';


GW::db();//init db
unset($_SERVER['HTTP_HOST']); // jei sita nuimu tada Navigator::getBase ima is settingsu site_url

if(GW::s('SYNC_TRANSLATIONS_AFTER_DEPLOY'))
	Navigator::backgroundRequest('admin/lt/datasources/translations/synchronizefromxml?commit=1');

Navigator::backgroundRequest('admin/lt/system/tools?act=doimportSqlUpdates');
Navigator::backgroundRequest('admin/lt/system/modules?act=doSyncFromXmls&force=1');


echo shell_exec("cd '".GW::s('DIR/ROOT')."' && rm -f repository/.sys/templates_c/*");
//