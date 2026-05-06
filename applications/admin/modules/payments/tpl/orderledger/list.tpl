{extends file="default_list.tpl"}

{block name="init"}
	{$do_toolbar_buttons_hidden=[dialogconf,dialogconf2]}
	{$dl_smart_fields=[order_id,created_by,change_transaction_id]}
	{$dl_actions=[edit,ext_actions]}
	
	{function dl_cell_order_id}
		<a target="_blank" href="{$app->buildUri("payments/ordergroups/`$item->order_id`/form")}">#{$item->order_id}</a>
	{/function}
	
	{function dl_cell_change_transaction_id}
		{if $item->change_transaction_id}
			<a class="iframe-under-tr" href="{$app->buildUri("datasources/changetransactions",[id=>$item->change_transaction_id,clean=>2])}">#{$item->change_transaction_id}</a>
		{/if}
	{/function}
	
	{function dl_cell_created_by}
		{if $item->created_by}
			<a class="iframeopen" href="{$app->buildUri("users/usr/`$item->created_by`/form",[clean=>2,readonly=>1])}">#{$item->created_by}</a>
		{/if}
	{/function}
	
	{function dl_cell_amount}
		{if $item->direction == 'refund'}-{/if}{$item->amount} {$item->currency}
	{/function}
{/block}
