{extends file="default_list.tpl"}


{block name="init"}


	{$display_fields=[title=>1,
		email=>1,
		lang=>1,
		groups=>1,insert_time=>1,update_time=>1]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[invert_active,edit,delete]}
	
	{$dl_filters=[
		name=>1, 
		surname=>1, 
		email=>1,
		lang=>1,
		insert_time=>1, 
		active=>[type=>select, options=>$lang.ACTIVE_OPT],
		groups=>[type=>multiselect, options=>$options.groups]]
	}
	
	{$dl_smart_fields=[title,groups]}
	
	{function dl_cell_title}
		{if $item->unsubscribed}<s style="color:gray">{$item->title}</s>{else}{$item->title}{/if}
	{/function}

	{function dl_cell_groups}
		{foreach from=$item->groups key=ind item=gid}
			<a href="{$app->ln}/{$app->page->path}/groups?id={$gid}" title="{$lang.EDIT}">{$options.groups.$gid}</a>
		{/foreach}	
	{/function}
	
	
	
	{gw_unassign var=$display_fields.image} 	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}