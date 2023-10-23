{extends file="default_list.tpl"}



{block name="init"}

	
	

	{$dl_filters=[]}
	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf]}	
	{$do_toolbar_buttons[] = search}
	
	{$dl_actions=[invert_active_ajax,editshift,move,ext_actions]}
	
	



{/block}

