<?php


class GW_Articles_Group extends GW_i18n_Data_Object
{
	public $table = 'gw_articles_groups';
	
	public $i18n_fields = [
	    'title'=>1,
	];		
	
	function getOptions($active=true, $ln)
	{
		$cond = $active ? 'active!=0' : '';
		
		return $this->getAssoc(['id',"title_{$ln}"], $cond);
	}	
	
	function getOptionsShort($active=true, $ln)
	{
		$cond = $active ? 'active!=0' : '';
		
		return $this->getAssoc(['id',"short_title_{$ln}"], $cond);
	}	
	
		
	
}