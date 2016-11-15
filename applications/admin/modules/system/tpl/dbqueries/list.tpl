{extends file="default_list.tpl"}


{block name="init"}
	{$dl_fields=[name,insert_time,update_time]}
	{$dl_actions=[run,invert_active,edit,delete]}
	
	{function dl_actions_run}
		{list_item_action_m url=[false,[act=>doExecuteQuery,id=>$item->id]] iconclass="fa fa-caret-square-o-right"}
	{/function}	
	
{/block}



