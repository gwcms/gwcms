<?php


class Navigator
{
	
	static private function __getAbsBase()
	{
		$arr =& $_SERVER;

		$HTTP_HOST = $arr['HTTP_HOST'];
		$HTTPS = isset($arr['HTTPS']) ? $arr['HTTPS'] : false;
		$SERVER_PORT = $arr['SERVER_PORT'];
		$REQUEST_URI = $arr['REQUEST_URI'];
	    
		if (isset($arr['ORIG_PATH_INFO']))
			$PATH_INFO = $arr['ORIG_PATH_INFO'];
		elseif(isset($arr['PATH_INFO']))
			$PATH_INFO = $arr['PATH_INFO'];

		$HTTP_SCHEME = isset($arr['HTTP_SCHEME']) ? $arr['HTTP_SCHEME'] : false;

		if (!empty($HTTP_SCHEME))
			$base=$HTTP_SCHEME. '://';
		else
			$base= ((!empty($HTTPS) && strtolower($HTTPS) != 'off') ? 'https' : 'http') . '://';


		$port_in_HTTP_HOST=(strpos($HTTP_HOST, ':') > 0);
		$base.= $HTTP_HOST;

		if(!(!$port_in_HTTP_HOST && !empty($SERVER_PORT) && ($SERVER_PORT == 80 || $SERVER_PORT == 443)))
			$base.= ((!empty($SERVER_PORT) && !$port_in_HTTP_HOST) ? ':' . $SERVER_PORT : '');
		
		return $base;	
	}
	
	static private function __getRelBase()
	{
		static $base;
		
		if($base)
			return $base;//catched
		
		$tmp1=$_SERVER['SCRIPT_NAME'];
		$tmp2=$_SERVER['REQUEST_URI'];
		
		$base='';
		
		for($i=0;$i<strlen($tmp1)-1;$i++)
			if($tmp1[$i]==$tmp2[$i])	
				$base.=$tmp1[$i];
			else
				break;
				
		return $base;
	}
	
	static function getBase($absolute=false)
	{
		$arr =& $_SERVER;
		$base = '';
		
		if($absolute)
			$base=self::__getAbsBase();

		$base.=self::__getRelBase();
		$base.=($base[strlen($base)-1]!='/' ? '/' : '');

		return $base;
	}
	
	static function getUri()
	{
		return $_SERVER['REQUEST_URI'];
	}
	    
	static function jump($url, $params = array())
	{
		$uri = self::buildURI($url, $params);
		
		header( "Location: $uri");
		exit;
	}
	
	
	static function &explodeURI($url)
	{
		$parts = parse_url($url);
		parse_str($parts['query'], $parts['query']);
		return $parts;	
	}
	
	static function implodeURI(&$parsed) 
	{
		if (!is_array($parsed))
			return false;
			
		$uri = isset($parsed['scheme']) ? $parsed['scheme'].':'.((strtolower($parsed['scheme']) == 'mailto') ? '' : '//') : '';
		$uri .= isset($parsed['user']) ? $parsed['user'].(isset($parsed['pass']) ? ':'.$parsed['pass'] : '').'@' : '';
		$uri .= isset($parsed['host']) ? $parsed['host'] : '';
		$uri .= isset($parsed['port']) ? ':'.$parsed['port'] : '';

		if (isset($parsed['path'])) {
			$uri .= (substr($parsed['path'], 0, 1) == '/') ?
				$parsed['path'] : ((!empty($uri) ? '/' : '' ) . $parsed['path']);
		}

		$uri .= isset($parsed['query']) ? '?'.http_build_query($parsed['query']) : '';
		$uri .= isset($parsed['fragment']) ? '#'.$parsed['fragment'] : '';

		return $uri;
	} 	
    
	static function buildURI($url, $params=Array())
	{
		if(!$url)
			$url=$_SERVER['REQUEST_URI'];
		
		if(!$params)
			return $url;
			
		$url = self::explodeURI($url);
		unset($url['query']['url']);
		$url['query']=array_merge($url['query'], $params);

		return self::implodeURI($url);
	}
	
	static function mergeGetParams($str_params)
	{
		parse_str($str_params, $params);
		$params=$params+$_GET;
		return http_build_query($params);
	}
}