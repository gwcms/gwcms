<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Nowakowskir\JWT\JWT;
use Nowakowskir\JWT\TokenDecoded;
use Nowakowskir\JWT\TokenEncoded;


class GW_PayMontonio_Api
{
	//https://payments-docs.montonio.com/#introduction
	
	function getAccess()
	{
		$payment_data = array(
		    'access_key' => $this->access_key,
		    'iat'        => time(),
		    'exp'        => time() + (60 * 60)
		);

		$tokenDecoded = new TokenDecoded($payment_data, []);
		$tokenEncoded = $tokenDecoded->encode($this->secret_key, JWT::ALGORITHM_HS256);   
		$this->access_token = $tokenEncoded->toString();
	}	
	
	function decodeToken($token)
	{
		$token = new TokenEncoded($token);
		
		if(!$token->validate($this->secret_key, JWT::ALGORITHM_HS256))
			d::dumpas("MONTONIO TOKEN ERROR");
		
		$tokendecoded = $token->decode();
		
		return ['payload'=>$tokendecoded->getPayload(), 'header'=>$tokendecoded->getHeader()];
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

    

		$opts = [
		    'http'=>[
			'method'=>"GET",
			'header'=>"Authorization: Bearer {$this->access_token}\r\n"
		    ]
		];

		$context = stream_context_create($opts);

		// Open the file using the HTTP headers set above
		return file_get_contents($url, false, $context);

	}
	
	
	function getBanks()
	{
	    $resp = $this->request('https://api.payments.montonio.com/pis/v2/merchants/aspsps', $this->getAccess());

	    echo "<pre>"; print_r(json_decode($resp)); echo "</pre>";

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
	
	function getRedirectLink($payment_data)
	{
		$tokenDecoded = new TokenDecoded($payment_data, []);
		$tokenEncoded = $tokenDecoded->encode($this->secret_key, JWT::ALGORITHM_HS256);
		
		return 'https://'.($this->sandbox ? 'sandbox-' : '').'payments.montonio.com?payment_token='.$tokenEncoded->toString();
	}
	

}










