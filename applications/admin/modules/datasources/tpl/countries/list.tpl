{extends file="default_list.tpl"}


{block name="init"}

	
	{$display_fields=[code=>1,title_en=>1]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{$dl_toolbar_buttons[] = hidden}
	{$dl_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}		
	
	{$dl_actions=[edit,delete]}
	
	{$dl_filters=[title=>1, insert_time=>1, active=>[type=>select, options=>$lang.ACTIVE_OPT]]}
	
	
	{gw_unassign var=$display_fields.image} 	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}