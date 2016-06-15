<?php

function dump_flush($var)
{
	$args = func_get_args();
	call_user_func_array('dump', $args);
	ob_flush();
	flush();
}

function backtrace()
{
	echo "<pre>";
	echo debug_print_backtrace();
	echo "</pre>";
}
