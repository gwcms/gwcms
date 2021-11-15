{extends file="default_list.tpl"}


{block name="init"}

	

	{$do_toolbar_buttons[] = dialogconf}	
	{$dlgCfg2MWdth=300}
	{$do_toolbar_buttons[] = dialogconf2}		
	{$do_toolbar_buttons[] = search}		
	
	{$dl_actions=[edit,delete,ext_actions]}
	{$dl_output_filters=[insert_time=>short_time, update_time=>short_time]}		
	

	{function dl_cell_order_id}
		<a target='_blank' href="{$app->buildUri("payments/ordergroups/{$item->order_id}/form")}">{$item->order_id}</a>
	{/function}	
	
	
	{$dl_smart_fields=[order_id]}
{/block}