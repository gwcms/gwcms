<?php

/**
 * Http agent class
 *
 * @copyright   sms.gw.lt 2012
 * @author      Vidmantas Norkus
 */



class GW_Http_Agent
{
	
	var $headers=Array(
		//'Connection'=>'keep-alive',
		//'Alive'=>'300',
		'Accept-Language'=>'en-us,en;q=0.7,lt;q=0.3',
		//'Accept-Encoding'=>'gzip,deflate',
		'Accept-Charset'=>'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
	);

	var $timeout=60;
	var $max_redirects=2;//-1 disabled 
	var $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322)';


	var $cookies=Array();
	var $last_request = '';
	var $last_response_header;
	var $last_request_time;	
	var $redirect_count;
	var $last_url;
	var $debug=0;
	var $debug_data;
	var $cookie_file;
	
	var $lgr;

	


	function __construct($headers=Array())
	{
		$this->headers += $headers;
		
	}

	function setAuth($user,$pass)
	{
		$this->headers['Authorization'] = 'Basic '.base64_encode("$user:$pass");
	}

	
	static function getUrlBase($url)
	{
		
		if(preg_match('/(https?:\/\/.*?)\//i',$url,$m))
			return $m[1];
	}
	
	function folowRedirect()
	{
		
		$this->redirect_count++;
		if($this->redirect_count > $this->max_redirects)
		{
			trigger_error('Redirect limit reached',  E_USER_WARNING);
			return false;
		}


		if(preg_match('/Location: (.*)/i',$this->last_response_header,$m)){
			//AddBase---------------	
			$url = $m[1];
			
			if(!preg_match('/https?:\/\//i',$url))
				$url=self::getUrlBase($this->last_url).$url;
			///---------------------
			if($this->debug)
				$this->debug_data[]="Redirecting to: $url";				
				
			return $this->getContents($url, Array('Referer'=>$this->last_url) , Array(), false, 1);
		}else{
			return false;
		}
	}


	function parseCookies()
	{
		
		//labas exception
		$header = explode('Location: ', $this->last_response_header);
		preg_match_all('/Set-Cookie: ([^=]*)=([^;]*)/i',$header[0],$m,PREG_SET_ORDER);
		
		
		foreach($m as $i => $tmp)
			$this->cookies[rawurldecode($tmp[1])]=rawurldecode($tmp[2]);
			
		$this->saveCookies();	
	}


	function &getCookies()
	{
		if(!$this->cookies && file_exists($this->cookie_file))
			$this->cookies=unserialize(file_get_contents($this->cookie_file));
		
		return $this->cookies;
	}	
	
	function saveCookies()
	{
		if($this->cookie_file)
			file_put_contents($this->cookie_file,serialize($this->cookies));
	}
	
	function resetCookies()
	{
		$this->cookies = Array();
		$this->saveCookies();
	}	
	
	
	function file_get_contents($url, $context_options)
	{
		$error = false;
		
		set_error_handler(
		    create_function(
		        '$severity, $message, $file, $line',
		        'throw new ErrorException($message, $severity, $severity, $file, $line);'
		    )
		);
		
		try {
		    $body = file_get_contents($url,false, stream_context_create($context_options));
		}
		catch (Exception $e) {
		    $error = $e->getMessage();
		}
		
		restore_error_handler();

		return Array($body, $error, $http_response_header);
	}	
	

	function getContents($url, $headers=Array(), $post_params=Array(), $max_length=false, $redirect=0)
	{
			
		$this->last_url=$url;

		if($redirect==0)
			$this->redirect_count = 0;

		$headers = $this->headers + $headers;

		$context_options = Array('http'=>Array());
		$context =& $context_options['http'];
	

		$context = Array
		(
			'timeout'=>$this->timeout,
			//'max_redirects'=>$this->max_redirects,
			'user_agent'=>$this->user_agent,
			'follow_location' => false		    
		);

		if(count($post_params))
		{
			$context['method'] = 'POST';
			$post_data = http_build_query($post_params);
			$headers['Content-type']="application/x-www-form-urlencoded";
			$headers['Content-Length']= strlen($post_data);
			$context['content']=$post_data;
		}
		else
		{
			$context['method'] = 'GET';
		}

		$header='';


		if(count($this->getCookies()))
		{
			$cookie =& $headers['Cookie'];
			foreach($this->cookies as $name => $value)
				$cookie.=rawurlencode($name).'='.rawurlencode($value)."; ";
			$cookie = substr($cookie,0,-2);
		}

		foreach($headers as $name => $value)
			$header.="$name: $value\r\n";


		$context['header'] = $header = substr($header,0,-2);

		$this->last_request=$header;
		$this->last_request_time=time();
		$this->last_response_header=Null;

		$timer = new GW_Timer;
		
		list($body, $error, $http_response_header)=self::file_get_contents($url, $context_options);

		
		
		if(count($http_response_header))
			$this->last_response_header = implode("\n",$http_response_header)."\n";

		$this->parseCookies();

		if($this->lgr)
			$this->lgr->msg($context['method']." ".$url);
		
		if($this->debug)
		{
			$s = Array
			(
				$context['method']=>$url,
				'size'=>strlen($body), 
				'time'=>$timer->stop(),
				'error'=>$error
			);
			$this->debug_data['small'][]=$s;
			$this->debug_data['large'][]=$s+Array(
				'url'=>$url,
				'size'=>strlen($body), 
				'time'=>$timer->stop(),
				'request_heder'=>$context['header'],
				'request_body'=>isset($context['content']) ? $context['content'] : false,
				'received_header'=>$this->last_response_header,
				'received_body'=>$body, 			
			);			
		}

		if(($this->max_redirects != -1) && ($body1 = $this->folowRedirect()))
			$body = $body1;

		return $body;
	}
	
	
	function flushDebugInfo()
	{
		return $this->debug_data;
		
		$this->debug_data=Array();//clean
	}

	function out($msg)
	{
		echo date('ymd His').' '.$msg."\n";
	}
	
	
	function impuls($url, $post_params=[])
	{	
		$parts=parse_url($url);
		

		$fp = fsockopen($parts['host'],
		    isset($parts['port'])?$parts['port']:80,
		    $errno, $errstr, 30);

		$out = "GET ".$parts['path'].'?'.$parts['query']." HTTP/1.1\r\n";
		$out.= "Host: ".$parts['host']."\r\n";
		$out.= "Connection: Close\r\n\r\n";
		fwrite($fp, $out);
		fclose($fp);		
		
		
		return true;
		
		/*
		$post_string = http_build_query($post_params);
		$parts=parse_url($url);

		$fp = fsockopen($parts['host'],
		    isset($parts['port'])?$parts['port']:80,
		    $errno, $errstr, 30);

		$out = "POST ".$parts['path']." HTTP/1.1\r\n";
		$out.= "Host: ".$parts['host']."\r\n";
		$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out.= "Content-Length: ".strlen($post_string)."\r\n";
		$out.= "Connection: Close\r\n\r\n";
		if (isset($post_string)) $out.= $post_string;

		fwrite($fp, $out);
		fclose($fp);
		 * 
		 */
	}	
	
	
}

?>