{extends file="default_list.tpl"}


{block name="init"}

	{function name=dl_cell_image}
		{$image=$item->image}
		{if $image}
			<img src="{$app->sys_base}tools/imga/{$image->id}?size=50x50" align="absmiddle" vspace="2" title="{$item->title|escape}" />
		{/if}
	{/function}
	
	

	{$dl_smart_fields=[image]}
	
	{$display_fields=[image=>1,title=>1,insert_time=>1,update_time=>1]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$do_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[invert_active,edit,delete]}
	
	{$dl_filters=[title=>1, insert_time=>1, active=>[type=>select, options=>$lang.ACTIVE_OPT]]}
	
	
	{gw_unassign var=$display_fields.image} 	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}