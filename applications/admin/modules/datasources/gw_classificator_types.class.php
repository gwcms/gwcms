<?php


class GW_Classificator_Types extends GW_Composite_Data_Object
{
	public $composite_map = [
		'childs' => ['gw_related_objecs', ['object'=>'GW_Classificators','relation_field'=>'type']],	    
	];	
	
	
	function getOptions($active=true)
	{
		//$cond = $active ? 'active!=0' : '';
		$cond="";
		
		return $this->getAssoc(['id','title'], $cond);
	}	

}