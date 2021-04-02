<?php


class GW_Classificator_Types extends GW_Data_Object
{
	
	function getOptions($active=true)
	{
		//$cond = $active ? 'active!=0' : '';
		$cond="";
		
		return $this->getAssoc(['id','title'], $cond);
	}	

}