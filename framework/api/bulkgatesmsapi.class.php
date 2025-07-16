<?php

class BulkGateSMSApi {

	private $apiUrl = "https://portal.bulkgate.com/api/1.0/simple/transactional";
	//private $apiUrl = "http://1.voro.lt/portalhotfix.php";
	
	private $appId;
	private $appToken;

	public function __construct($appId, $appToken) {
		$this->appId = $appId;
		$this->appToken = $appToken;
	}

	public function sendSMS($phoneNumber, $message, $sender = "labway.eu") {
		$data = [
		    "application_id" => $this->appId,
		    "application_token" => $this->appToken,
		    "number" => $phoneNumber,
		    "text" => $message,
		    "sender_id_value" => $sender,
		    "sender_id" => "gText"
		];

		$requestBody = json_encode($data);
		$sentHeaders = ['Content-Type: application/json'];
		$responseHeaders = '';

		$ch = curl_init($this->apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $sentHeaders);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); // 5 seconds to connect
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);        // 10 seconds total max execution time

		// Capture response headers
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$responseHeaders) {
			$responseHeaders .= $header;
			return strlen($header);
		});

		$responseBody = curl_exec($ch);
		$curlInfo = curl_getinfo($ch);
		$curlError = curl_error($ch);
		curl_close($ch);

		if (!isset($_GET['debug']))
			return json_decode($responseBody, true);

		d::dumpas([
		    "request" => [
			"url" => $this->apiUrl,
			"headers" => $sentHeaders,
			"body" => $requestBody,
		    ],
		    "response" => [
			"headers" => $responseHeaders,
			"body" => $responseBody,
			"http_code" => $curlInfo['http_code'] ?? null,
		    ],
		    "error" => $curlError ?: null,
		    "decoded" => json_decode($responseBody, true),
		]);
	}

}
