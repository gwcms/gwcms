{extends file="default_list.tpl"}


{block name="init"}

	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}	
	{$do_toolbar_buttons[] = search}	
	
	{$dl_actions=[edit,delete]}
	


{/block}