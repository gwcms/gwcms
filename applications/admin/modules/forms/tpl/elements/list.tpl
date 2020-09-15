{extends file="default_list.tpl"}



{block name="init"}


	{$dl_filters=[]}
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf]}	
	{$do_toolbar_buttons[] = search}
	
	{$dl_actions=[invert_active_ajax,edit,move,ext_actions]}
	
	



{/block}

