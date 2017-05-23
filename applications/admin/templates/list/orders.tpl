<table class="gwListViews gwTable" style="width:auto">
	<tr>
	<th><i class="fa fa-sort-amount-asc" aria-hidden="true" title="{$lang.ORDERS_LABEL}"></i></th> 		
	<td>
	{foreach $list_orders as $list_order}
		<a href="{$app->buildUri(false,[act=>doSetOrder,name=>$list_order.name],[carry_params=>1])}"
			{if $list_order.active} style="font-weight:bold"{/if}
			title="{if $list_order.hint}{$list_order.hint|escape}{else}{$list_order.order|escape:'html'}{/if}"
		>{$list_order.name}</a>
	</td>
	<td>		
	{/foreach}
	
	{$pgid=$app->page->id}

	{if $app->user->isRoot()}
		<a class="fontsz5" href="{$app->buildUri("system/modules/`$pgid`/form",[return_to=>$page->path])}" title="Edit Orders"><i class="fa fa-pencil-square-o"></i></a>
	{/if}
	</td>
	</tr>
</table>
