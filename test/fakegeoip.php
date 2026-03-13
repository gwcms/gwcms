<?php

if(!function_exists('geoip_country_code_by_name')){
	function GW::countryByIp($ip)
	{
		return file_get_contents('http://127.0.0.1:8000/geoip.php?ip='.$ip);

		//return shell_exec('/usr/bin/php7.4 -r "echo GW::countryByIp(\''.$ip.'\');"');
	}
}
