{extends file="default_list.tpl"}

{block name="init"}

	{function dl_actions_tplvars}
		{gw_link relative_path="`$id`/tplvars" title="Template vars"}
	{/function}
	
	{$display_fields = [
		id=>0,
		title=>1,
		path=>1,
		insert_time=>0,
		update_time=>0
	]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{$dl_smart_fields=[]}
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[tplvars,invert_active,edit,delete]}
	
	{$dl_filters=$display_fields}
	
	{$order_enabled_fields = array_keys($display_fields)}
{/block}
