{extends file="default_list.tpl"}


{block name="init"}

	
	{$display_fields=[iso639_1=>1,name=>1,native_name=>1,popularity=>1]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$do_toolbar_buttons[] = dialogconf}	
	{$do_toolbar_buttons[] = search}
	
	{$dl_actions=[edit,delete]}
	
	{$dl_filters=[iso639_1=>1,name=>1,native_name=>1, insert_time=>1, active=>[type=>select, options=>GW::l('/g/ACTIVE_OPT')]]}
	
	
	{gw_unassign var=$display_fields.image} 	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}