
<?php


class GW_Auto_Translate_Helper
{

	static $bulk=[];
	
	static function collectTrans($item, $field, $from, $to){
		$uniq = get_class($item).'__'.$item->id;
		self::$bulk["{$from}_{$to}:$field"][$uniq] = $item;		
	}
	
	static function finaliseAutoTrans(){
		
		foreach(self::$bulk as $opts => $list){
			list($from_to, $field) = explode(':',$opts);
			list($from, $to) = explode('_',$from_to);
			
			foreach(array_chunk($list, 30) as $chunk)
				self::seriesTranslate($chunk, $field, $from, $to);
		}
	}
	
	static $it=0;
	
	
	static function serialize($data)
	{
		return json_encode($data,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}
	static function writeLog($str)
	{
		if(GW::$context->app->user && GW::$context->app->user->isRoot()){
			d::ldump($str);
		}else{
			file_put_contents(GW::s('DIR/LOGS').'trans_'.date('Ym').'.log', $str."\n", FILE_APPEND);
		}
	}
	
	static function seriesTranslate($list, $field, $from, $to, $opts=[])
	{	
		self::$it++;
		
		$log = $opts['log'] ?? true;
		$commit = $opts['commit'] ?? true;
		
		//return false;
		//$app = GW::$context->app;
		
		foreach($list as $item)
			$title_array[] = $item->get("{$field}_$from");
		
			
		$serviceurl = GW_Config::singleton()->get('system__translations/main_service_url');	
		
			
		if(isset($_GET['autotransdebug'])){
			d::ldump(['serv'=>$serviceurl,'from'=>$from,'to'=>$to, 'field'=>$field, 'rows'=>$title_array]);
			//return false;
		}
		
		//d::dumpas($app);

		
		
		//$serviceurl = "http://vilnele.gw.lt/services/translate/test.php";
		//$app->setMessage('Service url took from syste/translations config. url: '.$serviceurl);
		$opts = http_build_query(['from'=>$from,'to'=>$to]);
		$resp_raw = GW_Http_Agent::singleton()->postRequest($serviceurl.'?'.$opts, ['queries'=>json_encode($title_array)]);
				
		$resp = json_decode($resp_raw);
		$count =0;
		$confirm = [];
		
		
		if(!isset($resp->result))
		{
			if($log){
				if($log){
					self::writeLog("REQUEST_FAILED: ".self::serialize(["$field : $from >> $to",$title_array]));
					self::writeLog("RESPONSE: \n".$resp_raw);
				}else{
					d::ldump("Trans request with ".count($title_array)." items FAILED");
				}				
			}
			
			return false;
		}
		
		foreach($list as $idx => $item)
		{
			if(isset($resp->failed_idxs[$idx])){
				//$app->setMessage();
				if($log){
					self::writeLog("FAIL: ".self::serialize($resp->result[$idx]));
				}else{
					d::ldump("Failed trans '".$resp->result[$idx]->q."'");
				}
				continue;
			}
			
			if($log){
				$debug = [
				    '#'=>self::$it,
				    'c'=>get_class($item), 
				    'id'=>$item->id, 
				    'fld'=>$field, 
				    'frln'=>$from, 
				    'toln'=>$to, 
				    'src'=>$resp->result[$idx]->q, 
				    'before'=>$item->get("{$field}_{$to}"), 
				    'res'=> $resp->result[$idx]->res];
			
				self::writeLog("OK: ". self::serialize($debug));
			}
			
			$item->set("{$field}", $resp->result[$idx]->res, $to);
			
			
			if(!isset($_GET['autotransdebug']) && $commit){
				$item->updateChanged();
			}
		}
		
		
	}
}