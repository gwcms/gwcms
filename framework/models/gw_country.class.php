<?php

class GW_Country extends GW_Data_Object
{
	public $table = 'gw_countries';

	
	function getOptions($lang='lt')
	{
		return $this->getAssoc(['code','title_'.$lang],'', ['order'=>'title_'.$lang.' ASC']);
	}


}