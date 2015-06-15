<?php


class GW_NL_Subscriber extends GW_Data_Object
{
	var $table = 'gw_nl_subscribers';
	
	public $calculate_fields = ['title'=>'getTitle'];
	
	function getTitle()
	{
		return "$this->name $this->surname <$this->email>";
	}
}			