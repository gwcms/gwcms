<?php

/**
 * Http agent class
 *
 * @copyright   sms.gw.lt 2016
 * @author      Vidmantas Norkus
 */
class GW_Http_Agent {

	public $headers = Array(
		//'Alive'=>'300',
		'User-Agent' => 'Mozilla/5.0',
		//'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		//'Accept-Language'=>'en-US,en;q=0.5',
		//'Accept-Encoding'=>'gzip, deflate',
		//'Accept-Charset'=>'ISO-8859-1,utf-8;q=0.7,*;q=0.7'
		//'Connection'=>'keep-alive',
	);
	public $timeout = 60;
	public $max_redirects = 2; //-1 disabled 
	public $cookies = Array();
	public $last_request = '';
	public $last_response_header;
	public $last_response_body;
	public $last_request_time;
	public $redirect_count;
	public $last_url;
	public $debug = 0;
	public $debug_data;
	public $cookie_file;
	public $proxy_script = false;
	public $proxy_script_pass = false;
	public $request_context = [];
	public $lgr;
	public $tidy_html = false;

	function __construct($headers = Array()) {
		$this->headers += $headers;
	}

	function setAuth($user, $pass) {
		$this->headers['Authorization'] = 'Basic ' . base64_encode("$user:$pass");
	}

	function setProxyScriptOn($url, $pass) {
		$this->proxy_script = $url;
		$this->proxy_script_pass = $pass;
	}

	function setProxyScriptOff() {
		$this->proxy_script = false;
		$this->proxy_script_pass = false;
	}

	function setPrivoxyOn() {
		$this->setProxyScriptOff();
		$this->request_context['request_fulluri'] = true;
		$this->request_context['proxy'] = "tcp://localhost:8118";
	}

	function setPricoxyOff() {
		unset($this->request_context['request_fulluri']);
		unset($this->request_context['proxy']);
	}

	static function getUrlBase($url) {

		if (preg_match('/(https?:\/\/.*?)\//i', $url, $m))
			return $m[1];
	}

	function folowRedirect() {

		$this->redirect_count++;
		if ($this->redirect_count > $this->max_redirects) {
			trigger_error('Redirect limit reached', E_USER_WARNING);
			return false;
		}


		if (preg_match('/Location: (.*)/i', $this->last_response_header, $m)) {
			//AddBase---------------	
			$url = $m[1];

			if (!preg_match('/https?:\/\//i', $url))
				$url = self::getUrlBase($this->last_url) . $url;
			///---------------------
			if ($this->debug)
				$this->debug_data[] = "Redirecting to: $url";

			return $this->getContents($url, Array('Referer' => $this->last_url), Array(), false, 1);
		}else {
			return false;
		}
	}

	function parseCookies() {

		//labas exception
		$header = [$this->last_response_header];
		//$header = explode('Location: ', $this->last_response_header);

		preg_match_all('/Set-Cookie: ([^=]*)=([^;]*)/i', $header[0], $m, PREG_SET_ORDER);


		foreach ($m as $i => $tmp)
			$this->cookies[rawurldecode($tmp[1])] = $tmp[2];

		$this->saveCookies();
	}

	function &getCookies() {
		if (!$this->cookies && file_exists($this->cookie_file))
			$this->cookies = json_decode(file_get_contents($this->cookie_file), true);

		return $this->cookies;
	}

	function saveCookies() {
		if ($this->cookie_file)
			file_put_contents($this->cookie_file, json_encode($this->cookies, JSON_PRETTY_PRINT));
	}

	function resetCookies() {
		$this->cookies = Array();
		$this->saveCookies();
	}

	function file_get_contents($url, $context_options) {
		$error = false;

		set_error_handler(
			create_function(
				'$severity, $message, $file, $line', 'throw new ErrorException($message, $severity, $severity, $file, $line);'
			)
		);

		$body = false;

		try {
			$body = file_get_contents($url, false, stream_context_create($context_options));
		} catch (Exception $e) {
			$error = $e->getMessage();
			$this->log("FAIL $url" . $error);
			$this->log('<pre>' . json_encode($context_options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		}

		restore_error_handler();

		return Array($body, $error, $http_response_header);
	}

	function postRequest($url, $post_vars, $headers = []) {
		$headers['Content-type'] = 'application/x-www-form-urlencoded';

		$header = '';
		foreach ($headers as $name => $value)
			$header.="$name: $value\r\n";

		$opts = array('http' =>
			array(
				'method' => 'POST',
				'header' => $header,
				'content' => http_build_query($post_vars)
			)
		);

		$context = stream_context_create($opts);

		return file_get_contents($url, false, $context);
	}

	function getContents($url, $headers = Array(), $post_params = Array(), $max_length = false, $redirect = 0) {

		$this->last_url = $url;

		if ($redirect == 0)
			$this->redirect_count = 0;

		$headers = $this->headers + $headers;


		$context_options = Array('http' => Array());
		$context = & $context_options['http'];


		$context = $this->request_context + Array
			(
			'timeout' => $this->timeout,
			//'max_redirects'=>$this->max_redirects,
			'follow_location' => false
		);

		if (count($post_params)) {
			$context['method'] = 'POST';
			$post_data = http_build_query($post_params);
			$headers['Content-type'] = "application/x-www-form-urlencoded";
			$headers['Content-Length'] = strlen($post_data);
			$context['content'] = $post_data;
		} else {
			$context['method'] = 'GET';
		}

		$header = '';


		if (count($this->getCookies())) {
			$cookie = & $headers['Cookie'];
			foreach ($this->cookies as $name => $value)
				$cookie.=rawurlencode($name) . '=' . $value . "; ";
			$cookie = substr($cookie, 0, -2);
		}

		foreach ($headers as $name => $value)
			$header.="$name: $value\r\n";


		$context['header'] = $header = substr($header, 0, -2);

		$this->last_request = $header;
		$this->last_request_time = time();
		$this->last_response_header = Null;

		$timer = new GW_Timer;



		if ($this->proxy_script) {

			$r = $this->postRequest($this->proxy_script, ['url' => $url, 'context_options' => serialize($context_options)], ['auth' => $this->proxy_script_pass]);
			$r = unserialize($r);
			$body = $r['body'];
			$error = $r['error'];
			$http_response_header = $r['http_response_header'];
		} else {
			list($body, $error, $http_response_header) = self::file_get_contents($url, $context_options);
		}



		if (count($http_response_header))
			$this->last_response_header = implode("\n", $http_response_header) . "\n";

		$this->parseCookies();

		if ($this->lgr)
			$this->lgr->msg($context['method'] . " " . $url);

		if ($this->debug) {
			$s = Array
				(
				$context['method'] => $url,
				'size' => strlen($body),
				'time' => $timer->stop(),
				'error' => $error
			);
			$this->debug_data['small'][] = $s;
			$this->debug_data['large'][] = $s + Array(
				'url' => $url,
				'size' => strlen($body),
				'time' => $timer->stop(),
				'request_heder' => $context['header'],
				'request_body' => isset($context['content']) ? $context['content'] : false,
				'received_header' => $this->last_response_header,
				'received_body' => $body,
			);
		}

		if (($this->max_redirects != -1) && ($body1 = $this->folowRedirect()))
			$body = $body1;

		if ($this->tidy_html)
			$body = $this->tidy($body);

		$this->last_response_body = $body;

		return $body;
	}

	function flushDebugInfo() {
		return $this->debug_data;

		$this->debug_data = Array(); //clean
	}

	function out($msg) {
		echo date('ymd His') . ' ' . $msg . "\n";
	}

	function impuls($url, $post_params = []) {
		$parts = parse_url($url);


		$fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);

		$out = "GET " . $parts['path'] . '?' . $parts['query'] . " HTTP/1.1\r\n";
		$out.= "Host: " . $parts['host'] . "\r\n";
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

	function log($msg) {
		if ($this->lgr)
			$this->lgr->msg($msg);
	}

	function tidy($html) {
		$html = str_replace(["\r", "\n", "\n"], ' ', $html);
		$html = preg_replace('/\s+</', '<', $html);
		$html = preg_replace('/>\s+/', '>', $html);
		$html = preg_replace('/\s{2,999}/', ' ', $html); //tarpu seka keisti i viena tarpa

		return $html;
	}

}
