<?php


class GW_NL_Groups extends GW_Data_Object
{
	var $table = 'gw_nl_subs_groups';
	
	
	function getOptions($active=true)
	{
		$cond = $active ? 'active!=0' : '';
		
		return $this->getAssoc(['id','title'], $cond);
	}
	
}