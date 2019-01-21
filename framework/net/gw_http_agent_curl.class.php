<?php

class GW_Http_Agent_Curl
{
	
	public $user_agent;
	public $cookies;
	public $acceptCookies=true;
	public $classHeaders=[];
	public $urlBegin="";
	public $proxy_url;
	
	function explodeHeader($header)
	{
		$header = explode("\n", $header);
		$hdr = [];
		foreach($header as $row)
		{
			$row = explode(": ", $row);
			if(count($row) > 1)
				$hdr[$row[0]][] = trim($row[1]);
		}

		return $hdr;
	}
	
	
	
	function getCookieString(&$hdr)
	{
		if($this->cookies){
			$tmp = "";
			
			if(!is_array($this->cookies))
				return false;
			
			foreach($this->cookies as $host => $cookies)
				if(is_array($cookies))
				foreach($cookies as $key => $val)
					$tmp.=rawurlencode($key)."=".$val."; ";
				
			$hdr['Cookie'] = $tmp;
			//d::dumpas($tmp);
		}

		
	}
	
	function _request($opts)
	{
		$ch = curl_init();
		curl_setopt_array($ch, $opts);

		$data = curl_exec($ch);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($data, 0, $header_size);
		$data = substr($data, $header_size);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$header_info = curl_getinfo($ch,CURLINFO_HEADER_OUT);

		return ['data'=>$data, 'header'=>$header, 'httpcode'=>$httpcode, 'headerinfo'=>$header_info];		
	}
	
	
	function request($url,  $data=[], $hdr=[], $opts=[])
	{
		$curl = curl_init();
		$url = $this->urlBegin . $url;
		$this->last_request_body = "";
				
		$default_header = [
			    'Accept'=>'application/json, text/plain, */*',
			    'Accept-Language'=>'en-GB,en-US;q=0.9,en;q=0.8,lt-LT;q=0.7,lt;q=0.6',
			    'Accept-Encoding'=>'gzip, deflate, br',    
			    'User-Agent'=>$this->user_agent	    
		];
		
		$json = false;
		$copts = [];
		
		
		if(isset($opts['options']))
		{
			$copts[CURLOPT_CUSTOMREQUEST] = 'OPTIONS';	
				
			$default_header['Access-Control-Request-Method'] = "POST";
			$default_header['Access-Control-Request-Headers'] = "content-type";
			$default_header["Accept"] = "*/*";
			
			$method = "OPTIONS";				
			
		}else{
			//default json request
			$json = true;
			$method = "GET";				
			
			if($data)
			{
				$copts[CURLOPT_POST] = 1;
				$copts[CURLOPT_POSTFIELDS] = json_encode($data);
						
				$method = "POST";	
				
				$this->last_request_body = json_encode($data);
			}			
			//default content type json
			$default_header['Accept'] = 'application/json, text/plain, */*';
			$default_header['Connection'] = 'keep-alive';
			$default_header['Content-Type'] = 'application/json;charset=utf-8';
			
		
		}
		
		if($method !="OPTIONS" && $this->cookies){
				
			$this->getCookieString($hdr);
			//d::dumpas($tmp);
		}
			
		$hdr = array_merge($hdr, $default_header);
		$hdr = array_merge($hdr, $this->classHeaders);
		$h=[];
		
		foreach($hdr as $key => $value)
		{
			$h[] = "$key: $value";
		}		

		$this->last_request_header = $h;
		
		$copts = $copts+[
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLINFO_HEADER_OUT => true,
			CURLOPT_HTTPHEADER => $h,
			CURLOPT_ENCODING => 'gzip, deflate'
		];
						
		if($this->proxy_url){
			$data = GW_Http_Agent::singleton()->curlProxyRequest($this->proxy_url, $copts);
		}else{
			$data = $this->_request($opts);
		}
		
		$header = $data['header'];
		$httpcode = $data['httpcode'];
		$header_info = $data['headerinfo'];		
		$data = $data['data'];
	

		$this->last_response_header = $header;
		$this->last_response_body = $data;		
				
		
		$hdr = $this->explodeHeader($header);
		

		if(isset($hdr['Set-Cookie']) && $this->acceptCookies)
			$this->acceptCookies($hdr['Set-Cookie']);
		
		if($data && $json)
		{
			$data = json_decode($data);
		}
		
		d::ldump([$url,$method,$httpcode, $this->last_request_header, $this->last_request_body, $this->last_response_header]);
		
		return ['header'=>$hdr, 'httpcode'=>$httpcode, 'data'=>$data];		
	}
	
	function acceptCookies($cookies)
	{
		foreach ($cookies as $i => $cookie){
			$cookie = explode(';', $cookie, 2);
			$cookie = explode('=', $cookie[0]);
			
			$this->cookies[rawurldecode($cookie[0])] = $cookie[1];
		}	
	}	
	
}
    
    

