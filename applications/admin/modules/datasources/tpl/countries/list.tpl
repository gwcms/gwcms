{extends file="default_list.tpl"}


{block name="init"}

	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}	
	{$dlgCfg2MWdth=300}
	{$do_toolbar_buttons[] = dialogconf2}			
	{$do_toolbar_buttons[] = search}	
	
	{$dl_actions=[edit,delete]}
	


{/block}