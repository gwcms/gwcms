{extends file="default_list.tpl"}


{block name="init"}
	
	
	{function name=do_toolbar_buttons_modactions} 
		{*{toolbar_button title="Suskaiciuoto produktus" href=$m->buildUri(false,[act=>doCounts])  iconclass="fa fa-refresh"}*}
	{/function}	
	
	
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}
	
	{$do_toolbar_buttons[]=hidden}
	{$do_toolbar_buttons[]=search}

	
	
	
	
	{$dl_actions=[edit,delete]}

	{$dl_output_filters=[
		value=>truncate,
		time=>short_time]}	
		

	{$dl_inline_edit=1}	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}
	

	
	
	
	{$dl_smart_fields=[type,count]}
	
{/block}