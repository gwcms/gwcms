<?php


class Module_Subscribers extends GW_Common_Module
{	
	public $default_view='viewList';
	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		$this->options['groups']=GW::getInstance('GW_NL_Group')->getOptions();
		
	}

	
	function viewDefault()
	{
		return $this->viewList();
	}
	
	
	function overrideFilterGroups($value)
	{
		
		
		$ids = array_filter($value,'intval');
				
		
		/* FIND BY TITLE
		 * //$value = "%$value%";
		$ids = GW::getInstance('Sms_Contact_Group')->getAssoc(['id','title'], ['user_id=? AND title LIKE ?',$this->app->user->id, $value]);
		
		if(!$ids)
			return "";
		$ids = array_keys($ids);
		
		*/
		
		$cond = " (SELECT count(*) FROM gw_nl_subs_bind_groups WHERE subscriber_id=id AND group_id IN (".implode(",",$ids)."))>0 ";
		
		
		return $cond;
	}
}
