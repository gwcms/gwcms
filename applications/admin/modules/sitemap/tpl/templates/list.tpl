{extends file="default_list.tpl"}

{block name="init"}

	{function dl_cell_templatevars}
		{capture assign=tmp}<i title='Template Vars' class='fa fa-object-ungroup'></i>{/capture}
		{gw_link relative_path="`$id`/tplvars" title=$tmp} {if $item->tplvars_count}({$item->tplvars_count}){/if}
	{/function}
	
	{$display_fields = [
		id=>0,
		title=>1,
		path=>1,
		templatevars=>1,
		insert_time=>0,
		update_time=>0
	]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{$dl_smart_fields=[templatevars]}

	{$do_toolbar_buttons = [addinlist,hidden]}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}		
	
	
	{$dl_actions=[invert_active,edit,delete,clone]}
	
	{$dl_filters=$display_fields}
	
	{$order_enabled_fields = array_keys($display_fields)}
{/block}
