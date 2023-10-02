{extends file="default_list.tpl"}


{block name="init"}

	
	{$do_toolbar_buttons[] = hidden}	

	
	{function name=do_toolbar_buttons_modactions} 
		{toolbar_button title="Sync payment methods" iconclass='fa fa-refresh' href=$m->buildUri(false,[act=>doSyncPayMethods])}	
	{/function}		
	
	{function name=do_toolbar_buttons_log}
		{toolbar_button onclick="gwcms.open_rtlogview('paysera.log');" title="Log" iconclass="gwico-Console"}
	{/function}		
		
	
	{$do_toolbar_buttons_hidden=[dialogconf,dialogconf2,dialogconf,modactions,log]}		
	

	{$dlgCfg2MWdth=300}
	
	{$do_toolbar_buttons[] = search}		
	
	{$dl_actions=[edit,delete]}
	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}
{/block}