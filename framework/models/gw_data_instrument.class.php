<?php

class GW_Data_Instrument extends GW_i18n_Data_Object
{
	public $table = 'gw_data_instruments';
	public $i18n_fields = ['title'=>1];
		
	
	function getOptions($lang = 'lt')
	{
		//return $this->getAssoc(['code', 'title_' . $lang], '', ['order' => 'title_' . $lang . ' ASC']);
		$opts= [];
		
		foreach($this->findAll(false) as $item)
		{
			$opts[$item->id] = $item->get('title', $lang);
		}
		
		return $opts;
	}
	
}