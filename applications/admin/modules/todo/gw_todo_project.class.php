<?php


class GW_Todo_Project extends GW_Data_Object
{
	var $table = 'gw_todo_projects';
	
	
	function getOptions()
	{
		//$cond = $active ? 'active!=0 AND removed=0' : '';
		
		return $this->getAssoc(Array('id','title')/*, $cond*/);
	}	
}