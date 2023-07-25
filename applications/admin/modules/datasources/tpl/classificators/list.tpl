{extends file="default_list.tpl"}


{block name="init"}
	
	
	{function name=do_toolbar_buttons_modactions} 
		{toolbar_button title="Add from text (1 item per line)" href=$m->buildUri(false,[act=>doImportOnePerLine])  iconclass="fa fa-download"}
	{/function}	
	
	{function name=do_toolbar_buttons_types} 
		{toolbar_button title="Tipai" href=$m->buildUri(classificator_types,[clean=>2],[level=>1]) btnclass="iframeopen" iconclass="fa fa-chevron-circle-down" tag_params=['data-dialog-width'=>"1200px"]}
	{/function}	
	
	
	{$do_toolbar_buttons = [addinlist]}	
	{$do_toolbar_buttons_hidden=[modactions,types,exportdata,importdata]}
	
	
	{$do_toolbar_buttons[]=dialogconf}
	{$do_toolbar_buttons[]=hidden}
	{$do_toolbar_buttons[]=search}

	
	
	
	
	{$dl_actions=[move,invert_active,edit,delete,clone]}

	{$dl_output_filters=[
		insert_time=>short_time, 
		update_time=>short_time]}	
		

	{$dl_inline_edit=1}	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}
	
	{function dl_cell_count}
		<a href="{$m->buildUri("products",[act=>doSetSingleFilter,field=>$prod_field,value=>$item->id],[level=>1])}">{$item->count}</a>
	{/function}
	{function dl_cell_type}
		{$options.classtypes[$item->type]}
	{/function}
	
	
	
	{$dl_smart_fields=[type,count]}
	
	
	
{/block}