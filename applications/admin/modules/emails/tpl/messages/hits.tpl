{extends file="default_list.tpl"}


{block name="init"}

	{$display_fields=[subscriber_id,link,ip,browser,referer,insert_time]}
	{$dl_smart_fields=[subscriber_id,link]}
	{$dl_output_filters=[browser=>truncate40_hint,referer=>truncate40_hint]}
	{$dl_filters=[]}
	{*
	{$dl_filters=[email=>1, insert_time=>1, link=>1,ip=>1,browser=>1,referer=>1]}
	*}
	
	{function name=dl_output_filters_truncate40_hint}
		{call name="truncate_hint" value=$item->$field length=40}
	{/function}	
	
	
	{$dl_fields=$display_fields}
	{$do_toolbar_buttons = ['filters']}	
	
	{$dl_actions=[]}
		
	{function dl_cell_subscriber_id}			
		{if $item->subscriber_id}
			<a href="{$app->ln}/{$m->module_path.0}/subscribers?id={$item->subscriber_id}">{$item->email}</a>
		{else}
			{$item->subscriber_id}
		{/if}
	{/function}
	
	{function dl_cell_link}			
		{if is_numeric($item->link)}
			id:{$item->link} {call name="truncate_hint" value=$links[$item->link] length=30}
		{else}
			{call name="truncate_hint" value=$item->link length=30}
		{/if}
	{/function}	
	
	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}