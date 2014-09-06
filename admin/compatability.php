<?php

//NO MSIE 6
if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6')!==false)
	die('MS Internet Explorer 6 not supported. <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx">Get new version</a>');

	
//magic quotes should be off
if($gpc = ini_get("magic_quotes_gpc"))
	trigger_error('echo "php_flag magic_quotes_gpc off" >> .htaccess', E_USER_ERROR);