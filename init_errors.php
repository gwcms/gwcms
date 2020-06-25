<?php
	
GW::$error_log = new GW_Logger(GW::$dir['LOGS'].'php_error_cached.log');


define('MASTER_REQUEST', !isset($_SERVER['REMOTE_ADDR']) ? /*cli*/1 : /*apache*/preg_match('/^127|^192\.168/',  $_SERVER['REMOTE_ADDR']) );

if(GW::s('PROJECT_ENVIRONMENT')==GW_ENV_PROD && !MASTER_REQUEST){
	define('_DEBUGING_', 0);
	//ini_set("display_errors", 0);

}else{
	define('_DEBUGING_', 1);
	//ini_set("display_errors", 1);

}


function error500()
{
	if($GLOBALS['error_msg_sent']) return;

	ob_clean();	
	@header("HTTP/1.1 500 Internal Server Error");
	@header("Status: 500 Internal Server Error");
	@header("Retry-After: 120");
	@header("Connection: Close");
	
	if (is_file($filename=GW::$dir['SYS_REPOSITORY'].'error_500.html')) 
		readfile($filename);

	$GLOBALS['error_msg_sent']++;
}


function sErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
{
	/*
		2   E_WARNING  	Non-fatal run-time errors. Execution of the script is not halted
		8 	E_NOTICE 	Run-time notices. The script found something that might be an error, but could also happen when running a script normally
		256 	E_USER_ERROR 	Fatal user-generated error. This is like an E_ERROR set by the programmer using the PHP function trigger_error()
		512 	E_USER_WARNING 	Non-fatal user-generated warning. This is like an E_WARNING set by the programmer using the PHP function trigger_error()
		1024 	E_USER_NOTICE 	User-generated notice. This is like an E_NOTICE set by the programmer using the PHP function trigger_error()
		4096 	E_RECOVERABLE_ERROR 	Catchable fatal error. This is like an E_ERROR but can be caught by a user defined handle (see also set_error_handler())
		8191 	E_ALL
	*/
	
	// if error has been supressed with an @
	if (error_reporting() == 0 || $errno == E_USER_NOTICE || $errno == E_DEPRECATED ) 
		return;


	$msg="#$errno: $errstr. File: $errfile:$errline remote_addr: $_SERVER[REMOTE_ADDR]";
	
	
	include_once GW::$dir['LIB'].'helpers/gw_debug_helper.class.php';
	
	
	
	GW::$error_log->msg($msg);
	
	if (_DEBUGING_){
                d::dump($msg);
	}else{
		mail
		(
			GW::s('MASTER_MAIL'), 
			GW::s('PROJECT_NAME').' error report', 
			$msg, 
			'From: error-cms@gw.lt'
		);
		error500();
	}
	
	//neuzlauzti kai wariningas
	if($errno == E_USER_WARNING || $errno == E_WARNING)
		return true;

		
	exit;
}

set_error_handler('sErrorHandler', E_ALL & ~E_NOTICE);