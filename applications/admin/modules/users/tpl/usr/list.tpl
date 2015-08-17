{extends file="default_list.tpl"}

{block name="init"}

	{function dl_actions_switchtouser}
		{gw_link do="switch_user" icon="switch_user" params=[id=>$item->id] show_title=0}
	{/function}
	
	{$display_fields = [
		id=>1,
		username=>1,
		name=>1,
		group_ids=>1,
		online=>1,
		insert_time=>0,
		update_time=>0
	]}
	
	{if $m->rootadmin}
		{$display_fields.parent_user_id=1}
	{/if}
	
	{$dl_smart_fields=[group_ids,name,online]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{function dl_cell_group_ids}
		{foreach from=$item->group_ids key=ind item=gid}
			{if $ind!=0}, {/if}<a href="{$app->ln}/{$app->page->path}/groups?id={$gid}" title="{$lang.EDIT}">{$options.group_ids.$gid}</a>
		{/foreach}	
	{/function}

	{function dl_cell_name}
		{$item->name} {$item->surname}
	{/function}	
	
	{function dl_cell_online}
		<img src="{$app_root}img/icons/{if $item->online}dot_green{else}dot_white{/if}.png">
	{/function}			
	
	
	
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[message,switchtouser,invert_active,edit,delete]}
	
	{function dl_actions_message}
		{gw_link relative_path="`$item->id`/message" params=[id=>$item->id] icon="message" title="write message" show_title=0}
	{/function}
	
	{$dl_filters=$display_fields}
	
	{$order_enabled_fields = array_keys($display_fields)}
{/block}
