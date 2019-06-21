<?php

class GW_Country extends GW_Data_Object
{

	public $table = 'gw_countries';
	public $ignore_fields=['insert_time'=>1];

	function getOptions($lang = 'lt')
	{
		return $this->getAssoc(['code', 'title_' . $lang], '', ['order' => 'title_' . $lang . ' ASC']);
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
