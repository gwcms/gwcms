{extends file="default_list.tpl"}


{block name="init"}

	{$do_toolbar_buttons[] = dialogconf}	
	{$dlgCfg2MWdth=300}
	{$do_toolbar_buttons[] = dialogconf2}		
	{$do_toolbar_buttons[] = search}		
	
	{$dl_actions=[edit,delete]}
	
{/block}