<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gw_crypt
 *
 * @author wdm
 */

class GW_Crypt_Helper{
	static function encryptExt($textToEncrypt, $config){
		$key = substr(hash('sha256', $config['pw'], true), 0, 32);
		$iv_len = openssl_cipher_iv_length($config['alg']);
		$iv = openssl_random_pseudo_bytes($iv_len);
		$tag = ""; // will be filled by openssl_encrypt
		$ciphertext = openssl_encrypt($textToEncrypt, $config['alg'], $key, OPENSSL_RAW_DATA, $iv, $tag, "", $config['tag']);
		$encrypted = self::base64url_encode($iv.$tag.$ciphertext);		
		return $encrypted;
	}
	
	static function decryptExt($textToDecrypt, $config)
	{
		$encrypted = self::base64url_decode($textToDecrypt);
		$key = substr(hash('sha256', $config['pw'], true), 0, 32);
		$iv_len = openssl_cipher_iv_length($config['alg']);
		$iv = substr($encrypted, 0, $iv_len);
		$tag = substr($encrypted, $iv_len, $config['tag']);
		$ciphertext = substr($encrypted, $iv_len + $config['tag']);
		$decrypted = openssl_decrypt($ciphertext, $config['alg'], $key, OPENSSL_RAW_DATA, $iv, $tag);	
		return $decrypted;
	}
	
	
	
	static function encrypt($str, $confid="URL"){
		return self::encryptExt($str, GW::s('CRYPT_CONF/'.$confid));
	}
	
	static function decrypt($str, $confid="URL"){
		return self::decryptExt($str, GW::s('CRYPT_CONF/'.$confid));
	}	
	
	static function encryptArr($arr, $confid="URL")
	{
		return self::encrypt(json_encode($arr), $confid);
	}
	
	static function decryptArr($arr, $confid="URL")
	{
		return json_decode(self::decrypt(json_encode($arr), $confid));
	}

	static function encryptUrl($arr, $confid="URL")
	{
		return self::encrypt(http_build_query($arr), $confid);
	}
	
	static function decryptUrl($encoded, $confid="URL")
	{
		parse_str(self::decrypt($encoded, $confid), $arr);
		return $arr;
	}	
	
	static function simpleEncryptUrl($arr)
	{
		return self::base64url_encode(http_build_query($arr));
	}
	
	static function simpleDecryptUrl($encoded)
	{
		parse_str(self::base64url_decode($encoded), $arr);
		return $arr;
	}		
	
	static function base64url_encode($data) {
	  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	static function base64url_decode($data) {
	  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}	
	
}