<?php

class GW_Debug_Helper {

		static function backtrace_soft($level_cut = 1) {
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

		static function show_debug_info() {
				$test = GW::$context->db->query_times;


				if (isset($_SESSION['debug']) && $this->app->user->isRoot()) {
						$info = $GLOBALS['debug'];
						$info['mem_use'][] = memory_get_usage(true);

						foreach ($info['mem_use'] as $i => $val)
								$info['mem_use'][$i] = GW_Math_Helper::cfilesize($val);



						$info['query_times'] = GW::$context->db->query_times;

						$info['query_times_sum'] = array_sum((array) $info['query_times']);

						if ($info['query_times_sum'])
								$info['process_db_part'] = round($info['query_times_sum'] / $info['process_time'] * 100) . '%';

						dump($info);
				}
		}

}
