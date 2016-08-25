{extends file="default_list.tpl"}


{block name="init"}
	{$do_toolbar_buttons = [addinlist]}
	
	{$dl_fields=[title,insert_time,update_time]}
	{$dl_actions=[permissions,invert_active,edit,delete]}
	
	{function dl_actions_permissions}
		
		{list_item_action_m url=["`$item->id`/permissions",[id=>$item->id]] iconclass="fa fa-key"}
	{/function}	
{/block}