{extends file="default_list.tpl"}


{block name="init"}

	{function name=dl_toolbar_buttons_import} 
		{gw_link relative_path=import title=Importuoti icon="action_action"} &nbsp;&nbsp;&nbsp; 
	{/function}
	{function name=dl_toolbar_buttons_export} 
		{gw_link relative_path=export title=Eksportuoti icon="action_action"} &nbsp;&nbsp;&nbsp; 
	{/function}
	{function name=dl_toolbar_buttons_emailsfromtext} 
		{gw_link relative_path=emailsfromtext title="Gavėjai iš teksto" icon="action_action"} &nbsp;&nbsp;&nbsp; 
	{/function}	
	
	{$display_fields=[title=>1,
		email=>1,
		lang=>1,
		groups=>1,insert_time=>1,update_time=>1]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{$dl_toolbar_buttons[] = hidden}
	{$dl_toolbar_buttons_hidden=[import,export,dialogconf,emailsfromtext]}	
	
	{$dl_actions=[invert_active,edit,delete]}
	
	{$dl_filters=[
		name=>1, 
		surname=>1, 
		email=>1,
		lang=>1,
		insert_time=>1, 
		active=>[type=>select, options=>$lang.ACTIVE_OPT],
		unsubscribed=>[type=>select, options=>['0'=>$lang.NO, '1'=>$lang.YES]],
		groups=>[type=>multiselect, options=>$options.groups]]
	}
	
	{$dl_smart_fields=[title,email,groups]}
	
	{function dl_cell_title}
		{if $item->unsubscribed}<s style="color:gray">{$item->title}</s>{else}{$item->title}{/if}
	{/function}
	{function dl_cell_email}
		{if $item->unsubscribed}<s style="color:gray">{$item->email}</s>{else}{$item->email}{/if}
	{/function}

	{function dl_cell_groups}
		{foreach from=$item->groups key=ind item=gid}
			<a href="{$app->ln}/{$app->page->path}/groups?id={$gid}" title="{$lang.EDIT}">{$options.groups.$gid}</a>
		{/foreach}	
	{/function}
	
	
	
	{gw_unassign var=$display_fields.image} 	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}