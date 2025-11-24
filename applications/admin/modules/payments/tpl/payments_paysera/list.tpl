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
	
	{$dl_actions=[edit,delete,ext_actions]}
	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}
	
	{function dl_cl_actions_doPayseraRetryProcess}
		{if $m->canBeAccessed($item, [access=>$smarty.const.GW_PERM_WRITE,nodie=>1])}
			<option value="checked_action('{$m->buildUri(false,[act=>doPayseraRetryProcessSeries])}', 1)">{GW::l('/A/VIEWS/doPayseraRetryProcess')}</option>
		{/if}
	{/function}	
	
	{if $app->user->isRoot()}
		{$dl_cl_actions[]=doPayseraRetryProcess}
	{/if}
	
	
{/block}