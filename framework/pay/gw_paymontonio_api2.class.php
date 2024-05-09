<?php


//php7.4 /var/www/gw/composer.phar require firebase/php-jwt

class GW_PayMontonio_Api2
{
	//https://payments-docs.montonio.com/#introduction
	
	function getAccess()
	{
		/*
		$payment_data = array(
		    'access_key' => $this->access_key,
		    'iat'        => time(),
		    'exp'        => time() + (60 * 60)
		);

		$tokenDecoded = new TokenDecoded($payment_data, []);
		$tokenEncoded = $tokenDecoded->encode($this->secret_key, JWT::ALGORITHM_HS256);   
		$this->access_token = $tokenEncoded->toString();
		*/
		
		// 1. Create the authorization token payload
		$payload = [
		    'accessKey' => $this->access_key,
		];

		// add expiry to the token for JWT validation
		$payload['exp'] = time() + (10 * 60);

		// 2. Generate the token using Firebase's JWT library
		$this->access_token = \Firebase\JWT\JWT::encode($payload, $this->secret_key, 'HS256');		
		
		//d::dumpas([$this->access_key, $this->secret_key, $this->access_token]);
		
	}	
	
	function decodeToken2($orderToken)
	{

		// The Order ID you got from Montonio as a response to creating the order
		

		// Add a bit of leeway to the token expiration time
		\Firebase\JWT\JWT::$leeway = 60 * 100; // 5 minutes

		// Use your secret key to verify the orderToken
		$decoded = \Firebase\JWT\JWT::decode(
		    $orderToken, 
		    new \Firebase\JWT\Key($this->secret_key, 'HS256'), 
		);
		
		
		if($decoded->accessKey != $this->access_key)
			$decoded= ['error'=>'access_token mismatch'];
		
		return ['payload'=>$decoded, 'header'=>false];
	}
	
	
	function __construct($cfg) 
	{
		
		$this->access_key = $cfg->access_key;
		$this->secret_key = $cfg->secret_key;
		$this->sandbox = $cfg->sandbox;
		
		if($this->sandbox){
			$this->access_key = $cfg->access_key_sandbox;
			$this->secret_key = $cfg->secret_key_sandbox;			
		}
		
		
		$this->getAccess();
	}
	
	function request($url)
	{

    

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    'Content-Type: application/json',
		    "Authorization: Bearer {$this->access_token}"
		]);

		$result = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		//d::dumpas(['url'=>$url, 'access_token'=>$this->access_token, 'response_status'=>$status ]);
		
		if ($status >= 400) {
		    var_dump($result);
		    // "{"statusCode":401,"message":"STORE_NOT_FOUND","error":"Unauthorized"}"
		    exit;
		}

		
		// 4. Decode the list of enabled payment methods
		$data = $result;
		//$data = json_decode($result, true);
		return $data;
	}
	
	function rootConfirmJson($array)
	{
		$payload = json_encode($array, JSON_PRETTY_PRINT);

		$form = ['fields'=>[
			'data'=>['type'=>'code_json', 'required'=>1, 'default'=>$payload],
		    ],'cols'=>4];

		if(!($answers=$this->prompt($form, "Confirm redirect data")))
			return false;

		return json_decode($answers['data'], true);	
	}	
	
	function getBanks()
	{
		$resp = $this->request('https://stargate.montonio.com/api/stores/payment-methods');
		
	
		return json_decode($resp);

	}
	
	
/*
 * payment data example
 * 
 $payment_data = array(
    'amount'                           => 1,
    'currency'                         => 'EUR',
    'access_key'                       => $access_key,
    'merchant_reference'               => 'ORDER123456',
    'merchant_return_url'              => 'https://webshop24.lt.eu/store_request.php',
    'merchant_notification_url'        => 'https://webshop24.lt/store_request.php',
    'payment_information_unstructured' => 'Test payment message',
    //'preselected_aspsp'                => 'LHVBEE22',
    'preselected_locale'               => 'lt',
    'checkout_email'                   => 'vidmantas.work@gmail.com',
    'exp'                              => time() + (60 * 10), 
);
 */	
	
	function getRedirectLink2($payload)
	{
		
		$payload['accessKey'] = $this->access_key;
		// add expiry to payment data for JWT validation
		$payload['exp'] = time() + (10 * 60);

		// 3. Generate the token using Firebase's JWT library
		$token = \Firebase\JWT\JWT::encode($payload, $this->secret_key, 'HS256');

		// Remove this var_dump once you want the header (location) to work correctly
	
		// eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhY2Nlc3NLZXkiOiJNWV9BQ0NFU1NfS0VZIiwibWVyY2hhbnRSZWZlcmVuY2UiOiJNWS1PUkRFUi1JRC0xMjMiLCJyZXR1cm5VcmwiOiJodHRwczovL215c3RvcmUuY29tL3BheW1lbnQvcmV0dXJuIiwibm90aWZpY2F0aW9uVXJsIjoiaHR0cHM6Ly9teXN0b3JlLmNvbS9wYXltZW50L25vdGlmeSIsImN1cnJlbmN5IjoiRVVSIiwiZ3JhbmRUb3RhbCI6OTkuOTg5OTk5OTk5OTk5OTk0ODg0MDkyMzAyNTI3Mjc4NjYxNzI3OTA1MjczNDM3NSwibG9jYWxlIjoiZW4iLCJiaWxsaW5nQWRkcmVzcyI6eyJmaXJzdE5hbWUiOiJDdXN0b21lckZpcnN0IiwibGFzdE5hbWUiOiJDdXN0b21lckxhc3QiLCJlbWFpbCI6ImN1c3RvbWVyQGN1c3RvbWVyLmNvbSIsImFkZHJlc3NMaW5lMSI6IkthaSAxIiwibG9jYWxpdHkiOiJUYWxsaW5uIiwicmVnaW9uIjoiSGFyanVtYWEiLCJjb3VudHJ5IjoiRUUiLCJwb3N0YWxDb2RlIjoiMTAxMTEifSwic2hpcHBpbmdBZGRyZXNzIjp7ImZpcnN0TmFtZSI6IkN1c3RvbWVyRmlyc3RTaGlwcGluZyIsImxhc3ROYW1lIjoiQ3VzdG9tZXJMYXN0U2hpcHBpbmciLCJlbWFpbCI6ImN1c3RvbWVyQGN1c3RvbWVyLmNvbSIsImFkZHJlc3NMaW5lMSI6IkthaSAxIiwibG9jYWxpdHkiOiJUYWxsaW5uIiwicmVnaW9uIjoiSGFyanVtYWEiLCJjb3VudHJ5IjoiRUUiLCJwb3N0YWxDb2RlIjoiMTAxMTEifSwibGluZUl0ZW1zIjpbeyJuYW1lIjoiSG92ZXJib2FyZCIsInF1YW50aXR5IjoxLCJmaW5hbFByaWNlIjo5OS45ODk5OTk5OTk5OTk5OTQ4ODQwOTIzMDI1MjcyNzg2NjE3Mjc5MDUyNzM0Mzc1fV0sInBheW1lbnQiOnsibWV0aG9kIjoicGF5bWVudEluaXRpYXRpb24iLCJtZXRob2REaXNwbGF5IjoiUGF5IHdpdGggeW91ciBiYW5rIiwiYW1vdW50Ijo5OS45ODk5OTk5OTk5OTk5OTQ4ODQwOTIzMDI1MjcyNzg2NjE3Mjc5MDUyNzM0Mzc1LCJjdXJyZW5jeSI6IkVVUiIsIm1ldGhvZE9wdGlvbnMiOnsicGF5bWVudFJlZmVyZW5jZSI6IlBBWU1FTlQtRk9SLU1ZLU9SREVSLUlELTEyMyIsInBheW1lbnREZXNjcmlwdGlvbiI6IlBheW1lbnQgZm9yIG9yZGVyIDM3IiwicHJlZmVycmVkQ291bnRyeSI6IkVFIiwicHJlZmVycmVkUHJvdmlkZXIiOiJMSFZCRUUyIn19LCJleHAiOjE2NzU4NjQwMTB9.sRq2ngH9eyYxNJy--s_SdpgL4bKYeTNhHrKYCkaHyDs

		// 4. Send the token to the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://stargate.montonio.com/api/orders");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
		    'data' => $token
		]));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    'Content-Type: application/json'
		]);
		$result = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($status >= 400) {
		    var_dump($result);
		    // "{"statusCode":401,"message":"STORE_NOT_FOUND","error":"Unauthorized"}"
		    exit;
		}

		// 5. Get the payment URL
		$data = json_decode($result, true);
		
		//d::dumpas($data);
		
		return $data['paymentUrl'];
	}
	

}








