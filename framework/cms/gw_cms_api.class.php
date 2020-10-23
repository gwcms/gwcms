<?php

/**
 * 
 * Pagalbinė cms_api klasė, siųsti užklausimus iš cli ar kitų sistemų
 * pvz atlikti veiksmus galim ir prisijungus
 * https://domenas.galune/admin/sms?act=do:send&to=37060012345&msg=test
 * 
 * nepatogumas kad sujungima igyvendinant reiktu autentifikacijos prisiloginimo, reiktu siusti POST requesta
 *  
 * nepatogumui pasalinti naudojame $_GET parametra - GW_CMS_API_AUTH, juo nurodome vartotoją ir API raktą
 * užklausa su API autentifikacija atrodytu taip:
 * https://domenas.galune/admin/sms?act=do:send&to=37060012345&msg=test&GW_CMS_API_AUTH=vartotojo_vardas:api_raktas
 * 
 * Cms vartotojo api raktą rasti galima vartotoju modulyje, pasirinkus redaguoti vartotoja, vartotojo formos apacioje rasime "api_key"
 * https://domenas.galune/admin/en/users/
 * 
 * pavyzdys su šia klase
 * $cms_api = new GW_Cms_Api('vartotojo_vardas','api_raktas')
 * $cms_api->base='http://domenas.galune/'; //paskutinis slashas turetu but
 * $atsakas = $cms_api->action('sms','send',Array('to'=>'37060012345','msg'=>'test'));
 * 
 * @package GW_CMS
 * @author wdm
 *
 */
class GW_Cms_Api
{

	var $api_key;
	var $username;
	var $lang;
	var $base;

	function __construct($user, $api_key, $lang = 'en')
	{
		$this->username = $user;
		$this->api_key = $api_key;
		$this->lang = $lang;

		$this->http = new GW_Http_Agent();

		$this->base = GW::s("PROJECT_ADDRESS"); //for local access
	}

	function action($path, $action, $get_params = Array())
	{
		$get_params['GW_CMS_API_AUTH'] = "$this->username:$this->api_key";
		$get_params['act'] = 'do:' . $action;

		$r = $this->http->getContents($this->base . $this->lang . "/" . $path . '?' . http_build_query($get_params));

		//dump($this->http->flushDebugInfo());

		return $r;
	}

	/**
	 * TODO 
	 */
	function request($path, $get_params = [], $headers=[], $post=[])
	{
		$get_params['GW_CMS_API_AUTH'] = "$this->username:$this->api_key";
		
		$r = $this->http->getContents($url = $this->base . $this->lang . "/" . $path . '?' . http_build_query($get_params), $headers, $post);

		//d::dumpas($url);
		//dump($this->http->flushDebugInfo());

		return $r;		
	}
}
