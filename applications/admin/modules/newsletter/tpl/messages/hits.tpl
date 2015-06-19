{extends file="default_list.tpl"}


{block name="init"}


	
	
	
	{$display_fields=[subscriber_id,link,insert_time]}
	{$dl_smart_fields=['subscriber_id']}
	
	{$dl_fields=$display_fields}
	{$dl_toolbar_buttons = ['filters']}	
	
	{$dl_actions=[]}
	
	{$dl_filters=[subscriber=>1, insert_time=>1, link=>1]}
	
	
	{function dl_cell_subscriber_id}			
		{if $item->subscriber}
			<a href="{$app->ln}/{$m->module_path.0}/subscribers?id={$item->subscriber->id}">{$item->subscriber->email}</a>
		{else}
			{$item->subscriber_id}
		{/if}
	{/function}
	
	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}