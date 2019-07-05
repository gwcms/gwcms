<?php

class GW_Debug_Helper
{
	static function backtrace_soft($level_cut = 1)
	{
		$str = '';
		$backtrace = debug_backtrace();
		$backtrace = array_slice($backtrace, $level_cut);
		$i = 0;

		//echo "<pre>";
		//echo print_R($backtrace);;

		foreach ($backtrace as $i => $trace) {
			$str.="#$i " . (isset($trace['file']) ? $trace['file'] : '-') . ':' .
			    (isset($trace['line']) ? $trace['line'] : '-') . ", ";
			$str.=@$trace['object'] ? '$' . get_class(@$trace['object']) . $trace['type'] . "{$trace['function']}" : "function $trace[function]";

			if (isset($_REQUEST['showargs']) || 1)
				$str.=', ARGS: ' . @json_encode($trace['args']);

			$str.="\n";
			$i++;
		}
		return $str;
	}

	static function show_debug_info()
	{
		$test = GW::$context->db->query_times;

		if (isset(GW::$context->app->sess['debug']) && GW::$context->app->sess['debug'] && GW::$context->app->user && GW::$context->app->user->isRoot()) {
			$info = $GLOBALS['debug'];
			$info['mem_use'][] = memory_get_usage(true);

			foreach ($info['mem_use'] as $i => $val)
				$info['mem_use'][$i] = GW_Math_Helper::cfilesize($val);



			$info['query_times'] = GW::$context->db->query_times;

			$info['query_times_sum'] = array_sum((array) $info['query_times']);

			if ($info['query_times_sum'])
				$info['process_db_part'] = round($info['query_times_sum'] / $info['process_time'] * 100) . '%';
			
			$info['db_speed'] = GW::$context->db->speed;

			d::dumpas($info);
		}
	}
	
	static function getCodeCut($error, $lines)
	{
		$code = str_replace("\r", "", file_get_contents($error['file']));
		
		
		
		$code = explode("\n",$code);
		
		
			
		
		$line_start = max(0, $error['line']-floor($lines/2));
		$res = [];
				
		for($i=$line_start; $i< $line_start+$lines; $i++)
			if(isset($code[$i]))
				$res[]="/*line $i*/ ".$code[$i];
			
		$code = implode("\n",$res);
		
		$code = highlight_string("<?php ".$code."?>", true);
		$code = str_replace('<span style="color: #0000BB">&lt;?php&nbsp;</span>', '', $code);
		$code = str_replace('<span style="color: #0000BB">?&gt;</span>', '', $code);

		return $code;
	}
	
	static function warningHandler($errno, $errstr, $errfile, $errline, $errcontext)
	{
		$e=[
		    "type"=>$errno,
		    "message"=>$errstr,
		    "file"=>$errfile,
		    "line"=>$errline,
		    "context"=>$errcontext,
		    "warning"=>1
		];
		self::processError($e);
	}
	
	static function errorReport()
	{
		$e = error_get_last();
		
		if (!is_null($e) ) {
			switch ($e['type']) {
				case E_ERROR: 
				case E_USER_ERROR:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:	
				case E_RECOVERABLE_ERROR:
				case E_CORE_WARNING:
				case E_COMPILE_WARNING:
				case E_PARSE:
					$e['error']=1;
					self::processError($e);	
			}
		}								
	}
	
	static function processError($e)
	{
		$data = $e+[
			    'ip'=>$_SERVER["REMOTE_ADDR"],
			    'host_by_ip'=>gethostbyaddr($_SERVER["REMOTE_ADDR"]),
			    'request_uri'=>Navigator::__getAbsBase().$_SERVER['REQUEST_URI']
			];
		
		if(isset($_SERVER['HTTP_USER_AGENT']))
			$data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		
		$data["errorid"] = date('YmdHis');
		$data['type_name'] = array_search($e['type'], get_defined_constants());
		unset($data['type']);

		$data['code'] = self::getCodeCut($e,10);
		$data['message'] = str_replace("\n", "<br />", $data['message']);
		
		if($_POST)
			$data['post'] = print_r($_POST, true);
		
		if($GLOBALS['debug_data'] ?? false)
			 $data['debug_data']=$GLOBALS['debug_data'];

		if(isset(GW::$context->app->user->id))
			$data['user_id'] = GW::$context->app->user->id;

		if(isset(GW::$context->app->user->id))
			$data['user_email'] = GW::$context->app->user->email;	

		if(isset($_SERVER['HTTP_REFERER']))
			$data['referer'] = $_SERVER['HTTP_REFERER'];

		if(function_exists('geoip_country_code_by_name'))
				$data['ip_country'] = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);

		
		if(GW::s('REPORT_ERRORS')){

			$reci = GW::s('REPORT_ERRORS');
			$subj = $data['type_name']." under project: ".GW::s('PROJECT_NAME').' env: '.GW::s('PROJECT_ENVIRONMENT');
	
			
			$nosend=[];
			
			if(GW::s('PROJECT_ENVIRONMENT') != GW_ENV_PROD)
				$nosend[]="not production environment";
			
			//is background request - isset($_GET['sys_call'])
			if(!isset($_GET['sys_call']) && isset(GW::$context->app->user) && GW::$context->app->user->isRoot())
				$nosend[]="root user && frontend request";	
			
			//$nosend = false;
				
			
			if($nosend){
				$data['debug']="ITS ".implode(' AND ',$nosend)." SO mail will be not sent\nMail subj: $subj, Mail recip: $reci";
			}
			
			$body = GW_Data_to_Html_Table_Helper::doTableSingleRecord($data, ['valformat'=>[
				    'code'=>0, 
				    'message'=>0,
				]]);	
			
			if($nosend)
			{
				echo $body;			
			}else{
				$opts = ['to'=>$reci, 'subject'=>$subj, 'body'=>$body, 'noAdminCopy'=>1, 'noStoreDB'=>1];
				GW_Mail_Helper::sendMail($opts);
			}
			
			if(isset($e['error'])){
				$error_publ = "Ooops we have some problems... Sorry :( But problem is now in front of staff monitors and it will be destroyed ASAP. PLEASE come back later. Error id: ".GW::s('PROJECT_NAME'). " errorid:".$data['errorid']."\n";
				echo $error_publ;
			}
			
		}else{
			echo "error report turned off<br/>\n";
			echo $e['message'];
		}		
	}
	
	function stringVerbose($str)
	{
		$ret = [];
		
		for($i=0;$i<strlen($str);$i++)
		{
			$ret[] = $str[$i].':'.ord($str[$i]);
		}	
					
		return implode(', ',$ret);
	}
	
	function stringVerboseOutp($strArr)
	{
		foreach($strArr as $str)
		{
			echo self::stringVerbose($key);
			echo "<hr>";
		}
	}
	
	function analiseMaxLength($arr)
	{	
		foreach($arr as $key => $val){
			$max[$key] = max($max[$key] ?? -1, $val);
		}
	}
	
	
}
