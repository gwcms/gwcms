{extends file="default_list.tpl"}

{block name="init"}

	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf]}
	{$do_toolbar_buttons[] = search}

	{$dl_actions=[edit,ext_actions]}
	{$dl_smart_fields=[changetrack_count,order_id,username]}

	{$dl_output_filters.update_time=short_time}
	{$dl_output_filters.insert_time=short_time}

	{function dl_cell_changetrack_count}
		{if $item->changetrack_count}
			<a class='badge bg-brown iframe-under-tr' href="{$app->buildUri("datasources/changetrack",[transaction_id=>$item->id,clean=>2])}">{$item->changetrack_count}</a>
		{/if}
	{/function}

	{function dl_cell_order_id}
		{if $item->order_id}
			<a target="_blank" href="{$app->buildUri("payments/ordergroups/`$item->order_id`/form")}">#{$item->order_id}</a>
		{/if}
	{/function}

	{function dl_cell_username}
		{if $item->user_id}
			<a class="iframeopen" href="{$app->buildUri("users/usr/`$item->user_id`/form",[clean=>2])}" title="User info - {$item->usertitle|default:$item->username|escape}">
				{$item->usertitle|default:$item->username|escape}
			</a>
		{else}
			{$item->usertitle|default:$item->username|escape}
		{/if}
	{/function}

	{if $transaction}
		<div class="alert alert-info" style="margin-bottom:10px;">
			<strong>Transaction #{$transaction->id}</strong>: {$transaction->note|escape}
		</div>
	{/if}

{/block}
