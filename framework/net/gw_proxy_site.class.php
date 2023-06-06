<?php

function build_post_fields($data, $existingKeys = '', &$returnArray = []) {
	if (($data instanceof CURLFile) or !(is_array($data) or is_object($data))) {
		$returnArray[$existingKeys] = $data;
		return $returnArray;
	} else {
		foreach ($data as $key => $item) {
			build_post_fields($item, $existingKeys ? $existingKeys . "[$key]" : $key, $returnArray);
		}
		return $returnArray;
	}
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

class GW_Proxy_Site {

	static function redirect($mirrorhost) 
	{

		$cnt=file_get_contents($f=GW::s('DIR/TEMP').'botcount'.date('Y-m-d'));
		file_put_contents($f, $cnt+1);
		
		/* Set it true for debugging. */
		$logHeaders = false;
		$logRequests = false;

		/* Site to forward requests to.  */


		/* Domains to use when rewriting some headers. */
		
		
		if(strpos($mirrorhost, 'http')===0){
			$parts = parse_url($mirrorhost);
			$remoteDomain = $parts['host'];
			$site = trim($mirrorhost,'/');
		}else{
			$remoteDomain = $mirrorhost;
			$site = "http://$mirrorhost";
		}
		//d::dumpas([$remoteDomain, $site]);
		//$remoteDomain = "test1.voro.lt:2080";
		

		$proxyDomain = $_SERVER['HTTP_HOST'];

				
		$request = Navigator::buildURI($_SERVER['REQUEST_URI'], ['redirmirror'=>$cnt]);
		

		$ch = curl_init();

		/* If there was a POST request, then forward that as well. */
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			curl_setopt($ch, CURLOPT_POST, TRUE);

			$test = build_post_fields($_POST);
			//echo print_r($test);
			curl_setopt($ch, CURLOPT_POSTFIELDS, build_post_fields($_POST));
		}
		curl_setopt($ch, CURLOPT_URL, $site . $request);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$headers = getallheaders();

		/* Translate some headers to make the remote party think we actually browsing that site. */
		$extraHeaders = array();
		if (isset($headers['Referer'])) {
			$extraHeaders[] = 'Referer: ' . str_replace($proxyDomain, $remoteDomain, $headers['Referer']);
		}
		if (isset($headers['Origin'])) {
			$extraHeaders[] = 'Origin: ' . str_replace($proxyDomain, $remoteDomain, $headers['Origin']);
		}


		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			$extraHeaders[] = 'X-Requested-With: ' . $_SERVER['HTTP_X_REQUESTED_WITH'];
		}
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$extraHeaders[] = 'User-Agent: ' . $_SERVER['HTTP_USER_AGENT'];
		}		
		
		$extraHeaders[] = 'Mirror-Redirect: ' . $cnt;

		/* Forward cookie as it came.  */
		curl_setopt($ch, CURLOPT_HTTPHEADER, $extraHeaders);
		if (isset($headers['Cookie'])) {
			curl_setopt($ch, CURLOPT_COOKIE, $headers['Cookie']);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		/*
		  if ($logHeaders)
		  {
		  $f = fopen("repos/headers.txt", "a");
		  curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		  curl_setopt($ch, CURLOPT_STDERR, $f);
		  }


		  if($logRequests){
		  file_put_contents(__DIR__.'/repos/'.$_SERVER['REMOTE_ADDR'].'_log.dat', json_encode($_SERVER+['time'=>date('Y-m-d h:i:s')], JSON_PRETTY_PRINT));
		  }
		 */

		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($ch);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		$headerArray = explode(PHP_EOL, $headers);

		/* Process response headers. */
		foreach ($headerArray as $header) {
			$colonPos = strpos($header, ':');
			if ($colonPos !== FALSE) {
				$headerName = substr($header, 0, $colonPos);

				/* Ignore content headers, let the webserver decide how to deal with the content. */
				if (trim($headerName) == 'Content-Encoding')
					continue;
				if (trim($headerName) == 'Content-Length')
					continue;
				if (trim($headerName) == 'Transfer-Encoding')
					continue;
				if (trim($headerName) == 'Location') {
					if ($header == "Location: //favicon.ico")
						continue;
					//print_r([$headerName,$header]);
					//exit;
				} //continue;
				/* -- */
				/* Change cookie domain for the proxy */
				if (trim($headerName) == 'Set-Cookie') {
					$header = str_replace('domain=' . $remoteDomain, 'domain=' . $proxyDomain, $header);
				}
				/* -- */
			}
			header($header, FALSE);
		}

		echo $body;

		if ($logHeaders) {
			fclose($f);
		}
		curl_close($ch);
	}

}
