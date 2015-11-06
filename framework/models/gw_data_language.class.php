<?php

class GW_Data_Language extends GW_Data_Object
{
	public $table = 'gw_data_languages';
	public $default_order='name ASC';
	
	function getOptions($field='name', $order='`name` ASC')
	{
		return $this->getAssoc(['iso639_1', $field],'', ['order'=>$order]);
	}
	
	function getOptionsNative()
	{
		return $this->getAssoc(['iso639_1','name'],'', ['order'=>'`popularity` DESC']);
	}
	
	function getMostPopularTop($mostpop_cnt, $field='name')
	{
		$x = $this->getAssoc(['iso639_1', $field],'', ['order'=>'`popularity` DESC','limit'=>$mostpop_cnt]);
		
		return $x;
	}

}