{extends file="default_list.tpl"}


{block name="init"}

	
	{$do_toolbar_buttons[] = hidden}	

	
	{function name=do_toolbar_buttons_modactions} 
		{toolbar_button title="Sync payment methods" iconclass='fa fa-refresh' href=$m->buildUri(false,[act=>doSyncPayMethods])}	
	{/function}		
	
	{$do_toolbar_buttons_hidden=[dialogconf,dialogconf2,dialogconf,modactions]}		
	

	{$dlgCfg2MWdth=300}
	
	{$do_toolbar_buttons[] = search}
	
	
	{$dl_actions=[edit,delete,ext_actions]}
	{$dl_output_filters=[insert_time=>short_time, update_time=>short_time]}		
	

	{function dl_cell_order_id}
		<a target='_blank' href="{$app->buildUri("payments/ordergroups/{$item->order_id}/form")}">{$item->order_id}</a>
	{/function}	
	
	
	{$dl_smart_fields=[order_id]}
	
	{function dl_cl_actions_doMontonioRetryProcess}
		{if $m->canBeAccessed($item, [access=>$smarty.const.GW_PERM_WRITE,nodie=>1])}
			<option value="checked_action('{$m->buildUri(false,[act=>doMontonioRetryProcessSeries])}', 1)">{GW::l('/A/VIEWS/doMontonioRetryProcess')}</option>
		{/if}
	{/function}	
	
	{if $app->user->isRoot()}
		{$dl_cl_actions=[doMontonioRetryProcess,dialogremove]}	
		{$dl_checklist_enabled=1}
	{/if}
	

{/block}