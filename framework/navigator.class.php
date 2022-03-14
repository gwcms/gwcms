<?php

class Navigator
{
	
	static public function __getAbsBase()
	{
		$arr = & $_SERVER;

		$HTTP_HOST = $arr['HTTP_HOST'];
		$HTTPS = isset($arr['HTTPS']) ? $arr['HTTPS'] : false;
		$SERVER_PORT = $arr['SERVER_PORT'];
		$REQUEST_URI = $arr['REQUEST_URI'];

		if (isset($arr['ORIG_PATH_INFO']))
			$PATH_INFO = $arr['ORIG_PATH_INFO'];
		elseif (isset($arr['PATH_INFO']))
			$PATH_INFO = $arr['PATH_INFO'];

		$HTTP_SCHEME = isset($arr['HTTP_SCHEME']) ? $arr['HTTP_SCHEME'] : false;

		if (!empty($HTTP_SCHEME))
			$base = $HTTP_SCHEME . '://';
		else
			$base = ((!empty($HTTPS) && strtolower($HTTPS) != 'off') ? 'https' : 'http') . '://';


		$port_in_HTTP_HOST = (strpos($HTTP_HOST, ':') > 0);
		$base.= $HTTP_HOST;

		if (!(!$port_in_HTTP_HOST && !empty($SERVER_PORT) && ($SERVER_PORT == 80 || $SERVER_PORT == 443)))
			$base.= ((!empty($SERVER_PORT) && !$port_in_HTTP_HOST) ? ':' . $SERVER_PORT : '');

		return $base;
	}

	static private function __getRelBase()
	{
		static $base;

		if ($base)
			return $base; //catched

		$tmp1 = $_SERVER['SCRIPT_NAME'];
		$tmp2 = $_SERVER['REQUEST_URI'];

		$base = '';

		$len = min(strlen($tmp1), strlen($tmp2));

		for ($i = 0; $i < $len; $i++)
			if ($tmp1[$i] == $tmp2[$i])
				$base.=$tmp1[$i];
			else
				break;

		return $base;
	}

	static function getBase($absolute = false)
	{

		//cli
		if (!isset($_SERVER['HTTP_HOST']))
			return GW::s("SITE_URL");

		$arr = & $_SERVER;
		$base = '';

		if ($absolute)
			$base = self::__getAbsBase();

		$base.=self::__getRelBase();
		$base.=($base[strlen($base) - 1] != '/' ? '/' : '');

		return $base;
	}

	static function getUri()
	{
		return $_SERVER['REQUEST_URI'];
	}

	static function jump($url, $params = array())
	{
		$uri = self::buildURI($url, $params);

		/*
		  ob_start();
		  d::dump($uri);
		  $out = ob_get_contents();
		  ob_end_clean();


		  file_put_contents(GW::s('DIR/REPOSITORY').'redirect.html', $out, FILE_APPEND);
		 */


		header("Location: $uri");
		exit;
	}

	static function &explodeURI($url)
	{
		$parts = parse_url($url);
		
		if(isset($parts['query'])){
			parse_str($parts['query'], $parts['query']);
		}else{
			$parts['query'] = [];
		}
		
		return $parts;
	}

	static function implodeURI(&$parsed)
	{
		if (!is_array($parsed))
			return false;

		$uri = isset($parsed['scheme']) ? $parsed['scheme'] . ':' . ((strtolower($parsed['scheme']) == 'mailto') ? '' : '//') : '';
		$uri .= isset($parsed['user']) ? $parsed['user'] . (isset($parsed['pass']) ? ':' . $parsed['pass'] : '') . '@' : '';
		$uri .= isset($parsed['host']) ? $parsed['host'] : '';
		$uri .= isset($parsed['port']) ? ':' . $parsed['port'] : '';

		
		if (isset($parsed['addpath'])) {
			$parsed['path'] =  str_replace('//','/', $parsed['path'] . '/'. $parsed['addpath']);
		}		
		
		if (isset($parsed['path'])) {
			$uri .= (substr($parsed['path'], 0, 1) == '/') ?
			    $parsed['path'] : ((!empty($uri) ? '/' : '' ) . $parsed['path']);
		}
			
		$uri .= isset($parsed['query']) ? '?' . http_build_query($parsed['query']) : '';
		$uri .= isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

		return $uri;
	}

	static function buildURI($url, $params = Array(), $opts=[])
	{
		if (!$url)
			$url = $_SERVER['REQUEST_URI'];

		if (!$params && !$opts)
			return $url;
		
		
		//one dirback directive allowed
		if(strpos($url,'/../')!==false){
			$url = explode('/../', $url,2);
			$url = dirname($url[0]).'/'.$url[1];
		}
		

		$url = self::explodeURI($url);
		if(isset($opts['path']))
			$url['addpath'] = $opts['path'];
			
		unset($url['query']['url']);
		$url['query'] = array_merge($url['query'], $params);

		return self::implodeURI($url);
	}

	static function mergeGetParams($str_params)
	{
		parse_str($str_params, $params);
		$params = $params + $_GET;
		return http_build_query($params);
	}

	/**
	 * Is limited to http. 
	 * https request does not works
	 */
	static function tempAccessUrl($user_id, $path, $get_args = [])
	{
		if(!isset($get_args['GWSESSID'])){
			$token = GW_Temp_Access::singleton()->getToken($user_id, '10 minute', $path);

			$get_args['temp_access'] = $user_id . ',' . $token;
			$get_args['sys_call'] = 1;
		}

		$path = self::buildURI($path, $get_args);

		if (GW::s('APP_BACKGROUND_REQ_TYPE') == 'localhost_base') {
			$base = GW::s("SITE_LOCAL_URL");
		} elseif (GW::s('APP_BACKGROUND_REQ_TYPE') == 'force_http') {
			$base = Navigator::getBase(true);
			$base = str_replace('https://', 'http://', $base);
		} else {
			$base = Navigator::getBase(true);
		}

		$url = $base . $path;		
		return $url;
	}
	
	static function backgroundRequest($path, $get_args = [], $uid=false)
	{
		GW_Http_Agent::impuls($url=self::tempAccessUrl($uid ? $uid: GW_USER_SYSTEM_ID, $path, $get_args));

		return $url;
	}
	
	/**
		request from any part of unauthorised zones 2 admin section
		expected result format: json
	 */
	static function sysRequest($path, $get_args=[])
	{
		$path = Navigator::getBase(true).$path;
		
		$token = GW::getInstance('gw_temp_access')->getToken(GW_USER_SYSTEM_ID);
		$get_args['temp_access'] = GW_USER_SYSTEM_ID . ',' . $token;
		$get_args['sys_call'] = 1;
		
		$path .= (strpos($path, '?')===false ? '?' : '&'). http_build_query($get_args);
		$raw = file_get_contents($path);
		$decode = json_decode($raw);	

		return $decode ? $decode : ['decode_err'=>1,'raw_response'=>$raw];
	}
	
	static function isAjaxRequest()
	{
		return isset($_GET["ajax_request"]) || !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';		
	}
	
	static public function isSecure() {
		return
		  (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		  || $_SERVER['SERVER_PORT'] == 443;
	}


	static public function checkUserAgent($type = NULL) 
	{
		$user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
		if ( $type == 'bot' ) {
			// matches popular bots
			if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent ) ) {
				return true;
				// watchmouse|pingdom\.com are "uptime services"
			}
		} else if ( $type == 'browser' ) {
			// matches core browser types
			if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) ) {
				return true;
			}
		} else if ( $type == 'mobile' ) {
			// matches popular mobile devices that have small screens and/or touch inputs
			// mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
			// detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
			if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) {
				// these are the most common
				return true;
			} else if ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
				// these are less common, and might not be worth checking
				return true;
			}
		}
		return false;
	}
	
	static function setCookie($name, $val)
	{		
		if(is_array($val) || is_object($val))
			$val = json_encode ($val);
		
		$_COOKIE[$name] = $val;
		
		setcookie($name, $val, time()+3600*24*365*10, Navigator::getBase());	
	}	
	
	function postLink($url, $title, $args,$opts=[])
	{
		$GLOBALS['postlinkformidx'] = ($GLOBALS['postlinkform'] ?? 0) +1;
		
		$str = "<form id='postlinkform".$GLOBALS['postlinkformidx']."' action='$url' method='post' style='display:none'>";
		$postkeys = GW_Array_Helper::arrayFlattenSep('/:/', $args);
		$newpostkeys = [];
		foreach($postkeys as $key => $val)
		{
			$key = explode('/:/', $key);
			
			if(count($key)>1){
				
				$newkey = '';
				for($i=0;$i<count($key);$i++){
					$newkey .= $key[$i] . (($i>0)?'][':'[');
				}
				$key = substr($newkey,0, -1);
				
			}else{
				$key = $key[0];
			}
			$newpostkeys[$key] = $val;
			
		}
		foreach($newpostkeys as $key => $value){
			$str.='<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">';
		}
		
		
		$str.='</form><a class="'.($opts['aclass']??'').'" href="'.$url.'" onclick="document.querySelector(\'#postlinkform'.$GLOBALS['postlinkformidx'].'\').submit();return false;">'.$title.'</a>';
		return $str;
	}
}
