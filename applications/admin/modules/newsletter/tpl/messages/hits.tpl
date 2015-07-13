{extends file="default_list.tpl"}


{block name="init"}

	{$display_fields=[subscriber_id,link,ip,browser,referer,insert_time]}
	{$dl_smart_fields=[subscriber_id,link]}
	{$dl_output_filters=[browser=>truncate40_hint,referer=>truncate40_hint]}
	
	{function name=dl_output_filters_truncate40_hint}
		{call name="truncate_hint" value=$item->$field length=40}
	{/function}	
	
	
	{$dl_fields=$display_fields}
	{$dl_toolbar_buttons = ['filters']}	
	
	{$dl_actions=[]}
	
	{$dl_filters=[email=>1, insert_time=>1, link=>1]}
	
	
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
			{$item->link}
		{/if}
	{/function}	
	
	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}