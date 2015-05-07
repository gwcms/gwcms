{extends file="default_list.tpl"}


{block name="init"}
	{$dl_fields=[title,insert_time,update_time]}
	{$dl_actions=[permissions,invert_active,edit,delete]}
	
	{function dl_actions_permissions}
		{gw_link relative_path="`$item->id`/permissions" icon="action_set_permissions" params=[id=>$item->id] show_title=0 title=$m->lang.PERMISSIONS}
	{/function}	
{/block}