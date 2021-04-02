{extends file="default_list.tpl"}


{block name="init"}
	
	
	{function name=do_toolbar_buttons_modactions} 
		{toolbar_button title="Suskaiciuoto produktus" href=$m->buildUri(false,[act=>doCounts])  iconclass="fa fa-refresh"}
	{/function}	
	
	{$do_toolbar_buttons_hidden=[modactions]}
	{$do_toolbar_buttons[]=dialogconf}
	{$do_toolbar_buttons[]=hidden}
	{$do_toolbar_buttons[]=search}

	
	
	
	
	{$dl_actions=[invert_active,move,edit,delete]}
	{$dl_output_filters=[
		insert_time=>short_time, 
		update_time=>short_time]}
	
		

	{$dl_inline_edit=1}	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}
	
	{function dl_cell_count}
		<a href="{$m->buildUri("products",[act=>doSetSingleFilter,field=>$prod_field,value=>$item->id],[level=>1])}">{$item->count}</a>
	{/function}
	{$dl_smart_fields=[count]}
	
{/block}