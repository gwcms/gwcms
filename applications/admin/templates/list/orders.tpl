<table class="gwTable" style="width:auto">
	<tr>
	<th>{$lang.ORDERS_LABEL}:</th> 		
	<td>
	{foreach $list_orders as $list_order}
		<a href="{$app->buildUri(false,[act=>doSetOrder,name=>$list_order.name],[carry_params=>1])}"
			{if $list_order.active} style="font-weight:bold"{/if}
			title="{if $list_order.hint}{$list_order.hint|escape}{else}{$list_order.order|escape:'html'}{/if}"
		>{$list_order.name}</a>
	{/foreach}
	
	{$pgid=$app->page->id}
	</td>
	<td>
	{if $app->user->isRoot()}
		{gw_link path="system/modules/`$pgid`/form" icon="action_edit" title="Edit Orders" show_title=0 params=[return_to=>$page->path]}
	{/if}
	</td>
	</tr>
</table>
