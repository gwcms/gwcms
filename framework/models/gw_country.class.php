<?php

class GW_Country extends GW_i18n_Data_Object
{

	public $table = 'gw_countries';
	public $ignore_fields=['insert_time'=>1];
	
	
	public $i18n_fields = Array(
		'title' => 1,
	);	

	function getOptions($lang = 'lt', $extracond=false)
	{
		//return $this->getAssoc(['code', 'title_' . $lang], '', ['order' => 'title_' . $lang . ' ASC']);
		$opts= [];
		
		foreach($this->findAll($extracond, ['order'=>"title_$lang ASC"]) as $country)
		{
			$opts[$country->code] = $country->get('title', $lang);
		}
		
		return $opts;
	}
	
	
	function get_code3_to_code2_map($lang = 'lt')
	{
		//return $this->getAssoc(['code', 'title_' . $lang], '', ['order' => 'title_' . $lang . ' ASC']);
		$opts= [];
		
		foreach($this->findAll(false) as $country)
		{
			$opts[$country->code2] = $country->code;
		}
		
		return $opts;
	}
	
	function getIdOptions($lang = 'lt')
	{
		return $this->getAssoc(['id', 'title_' . $lang], '', ['order' => 'title_' . $lang . ' ASC']);
	}	

	/**
	 * Used to update titles, codes from google maps autocomplete location service
	 */
	function updateCountry($code, $title_en)
	{
		if ($country = $this->find(['code=?', $code])) {
			if ($country->title_en != $title_en)
				$country->saveValues(['title_en' => $title_en]);
		}else {
			$new = $this->createNewObject(['code' => $code, 'title_en' => $title_en]);
			$new->insert();
		}
	}
	
	function getCountryByCode($cc,$lang)
	{
		$country = $this->getAssoc(['code', 'title_' . $lang], ['code=?',$cc], ['order' => 'title_' . $lang . ' ASC']);
		return isset($country[$cc]) ? $country[$cc] : $cc;
	}
	
	function eventHandler($event, &$context_data = array()) {
		
		switch ($event){
			case "BEFORE_INSERT":
				$this->update_time = date('Y-m-d H:i:s');
			break;
		}
		
		return parent::eventHandler($event, $context_data);
	}
	
	function isEuCountry($country)
	{
		static $eucountries;
		
		if(!$eucountries){
			$eucountries = GW_Config::singleton()->get('datasources__countries/eu_countries');

			$eucountries = json_decode($eucountries);
		}
		
		if(!$eucountries)
			return null;		
		
		return in_array($country, $eucountries);
	}
}
