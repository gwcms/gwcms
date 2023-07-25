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

	
	function getOptionsKey($active=true)
	{
		//$cond = $active ? 'active!=0' : '';
		$cond="";
		
		return $this->getAssoc(['key','title'], $cond);
	}	


	function getChildOptions($passive=false)
	{
		$opts = [];
		foreach($this->childs as $child){
			if($passive || $child->active==1)
				$opts[$child->key] = $child->title;
		}
		
		return $opts;
	}	
}