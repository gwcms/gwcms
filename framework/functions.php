<?php

function dump()
{
	$args = func_get_args();
	call_user_func_array(Array('d', 'dump'), $args);
}

if(!function_exists('geoip_country_code_by_name')){
	function geoip_country_code_by_name($ip)
	{
		return shell_exec('/usr/bin/php7.4 -r "echo geoip_country_code_by_name(\''.$ip.'\');"');
	}
}
