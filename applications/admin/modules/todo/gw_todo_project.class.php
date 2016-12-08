<?php


class GW_Todo_Project extends GW_Data_Object
{
	public $table = 'gw_todo_projects';
	
	public $validators = ['title' => ['gw_string', [ 'required'=>1 ]]];	
	
	
	function getOptions($conds=null)
	{
		//$cond = $active ? 'active!=0 AND removed=0' : '';
		
		return $this->getAssoc(Array('id','title'), $conds);
	}	
}