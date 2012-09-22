<?php


class GW_Debug_Helper
{
	function backtrace_soft($level_cut = 1)
	{
		$str='';
		$backtrace = debug_backtrace();
		$backtrace = array_slice($backtrace,$level_cut);
		$i=0;
		
		
		
		foreach($backtrace as $i => $trace)
		{
			$str.="#$i $trace[file]:$trace[line], ";
			$str.=$trace[object]?'$'.get_class($trace[object])."{$trace[type]}{$trace['function']}":"function $trace[function]";
	
			if($_REQUEST['showargs'] || 1)
				$str.=', ARGS: '.@json_encode($trace['args']);
	
			$str.="\n";
			$i++;
		}
		return $str;
	}
	
	function show_debug_info()
	{
		$test = GW::$db->query_times;


		if($_SESSION['debug'] && GW::$user->isRoot())
		{
			$info = $GLOBALS['debug'];
			$info['mem_use'][]=memory_get_usage(true);
			
			foreach($info['mem_use'] as $i => $val)
				$info['mem_use'][$i] = GW_Math_Helper::cfilesize($val); 
			
		
			
			$info['query_times']=GW::$db->query_times;
		
			$info['query_times_sum']=array_sum((array)$info['query_times']);
		
			if($info['query_times_sum'])
				$info['process_db_part']= round($info['query_times_sum']/$info['process_time'] * 100) .'%';
		
			dump($info);
		}	
	}	
	
}