
<table class="ordereditems" style="border-collapse: collapse;" cellspacing="5" cellspacing="5" border="1">
	<tr>
		<th>{GW::ln("/M/products/FIELDS/title")}</th>
		<th>{GW::ln("/M/products/FIELDS/remote_id")}</th>
		<th>{GW::ln("/M/products/QTY")}</th>
		<th>{GW::ln("/M/products/FIELDS/price")}</th>
		<th>{GW::ln("/M/products/NUM_PRODUCT_1")}</th>
		
		
	</tr>
{foreach $list as $item}
	{$product = $products_list[$item.prod_id]}
	<tr>
		<td>{$product->title}</td>
		<td><a target="_blank" href="{$domain}/lt/direct/products/products/p/x?id={$product->id}">{$product->remote_id}</a></td>
		<td>{$item.qty}</td>
		<td>{$item.price} &euro;</td>
		<td>{$item.qty * $item.price} &euro;</td>
		
	</tr>
{/foreach}
</table>

{*
neveikia ant gmail
<style>
	.ordereditems{   }
	.ordereditems td, .ordereditems th{ padding: 5px 10px 5px 10px;border: 1px solid silver; }
</style>

*}