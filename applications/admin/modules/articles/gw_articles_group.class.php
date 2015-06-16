<?php


class GW_Articles_Group extends GW_Data_Object
{
	var $table = 'gw_articles_groups';
	
	
	function getOptions($active=true)
	{
		$cond = $active ? 'active!=0' : '';
		
		return $this->getAssoc(['id','title'], $cond);
	}	
	
}