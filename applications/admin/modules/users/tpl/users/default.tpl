{extends file="default_list.tpl"}

{block name="init"}

	{function dl_actions_switchtouser}
		{gw_link do="switch_user" icon="switch_user" params=[id=>$item->id] show_title=0}
	{/function}
	
	{$display_fields = [
		id=>1,
		username=>1,
		name=>1,
		link_groups=>1,
		insert_time=>0,
		update_time=>0
	]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{function dl_cell_link_groups}
		{foreach from=$item->group_ids key=ind item=gid}
			{if $ind!=0}, {/if}<a href="{$app->ln}/{$app->page->path}/groups?id={$gid}" title="{$lang.EDIT}">{$groups_options.$gid}</a>
		{/foreach}	
	{/function}

	{function dl_cell_name}
		{$item->name} {$item->surname}
	{/function}	
		
	
	{$dl_smart_fields=[link_groups,name]}
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[switchtouser,invert_active,edit,delete]}
	
	{$dl_filters=$display_fields}
	
	{$order_enabled_fields = array_keys($display_fields)}
{/block}
