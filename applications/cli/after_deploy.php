#!/usr/bin/php
<?php

chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php';


echo "Run lang sync ";
GW::db();//init db
unset($_SERVER['HTTP_HOST']); // jei sita nuimu tada Navigator::getBase ima is settingsu site_url
Navigator::backgroundRequest('admin/lt/datasources/translations/synchronizefromxml?commit=1');
Navigator::backgroundRequest('admin/lt/system/tools?act=doimportSqlUpdates');