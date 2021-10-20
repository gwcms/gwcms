{include "default_open.tpl"}

<h2>{GW::ln('/m/YOUR_ORDERS')}</h2>


{if $list}

<table class="orderlist">
	<tr><th>#</th><th>{GW::ln('/g/CREATE_DATE')}</th><td>{GW::ln('/m/PARTS')}</td><th>{{GW::ln('/g/CART_TOTAL')}}</th></tr>
	{foreach $list as $order}
		{$citems = $order->items}
		<tr class="{if $smarty.get.id==$order->id}alert-warning{/if}{if $citems}rowwitms{else}rownoitms{/if}">
			<td>{$order->id}</td>
			<td>{$order->insert_time}</td>
			<td>{count($citems)}</td>
			<td>
				{$order->amount} Eur
				{if !$order->pay_confirm_id && $order->amount}
					<a href="{$m->buildUri('direct/orders/orders', [act=>doOrderPay,id=>$order->id])}" class="btn u-btn-brown btn-xs rounded-0">
						<i class="fa fa-credit-card g-mr-2"></i>
						{GW::ln('/g/PROCEED_PAYMENT')}
					      </a>
					      <a href="{$m->buildUri('direct/orders/orders', [act=>doCancelOrder,id=>$order->id])} " class="btn u-btn-brown btn-xs rounded-0">
						      <i class="fa fa-times"></i> {GW::ln('/g/CANCEL')}
						      </a>
				{/if}
			</td>
			<td>
				{if $order->pay_confirm_id}
					{$order->pay_confirm->title}
					<a href="{$m->buildDirectUri('invoice', [id=>$order->id])}"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {GW::ln('/m/INVOICE')}</a>
				{/if}
			</td>
			
		</tr>
		
		{if $citems}
		<tr class="itmsrow {if $smarty.get.id==$order->id}alert-warning{/if}">
			<td colspan="5">
				
			<ul class="u-alert-list g-mt-10">
				{foreach $citems as $citem}
					{$obj=$citem->obj}
					<li>{GW::ln("/g/CART_ITM_{$citem->obj_type}")} - {if $obj->context_short}<i>{$obj->context_short}</i> - {/if} {$obj->title} {$citem->qty}x{$citem->unit_price} Eur 
						{if !$order->pay_confirm_id}
							<a href="{$m->buildUri(false, [act=>doCartItemRemove,id=>$citem->id])}"><i class="fa fa-times"></i></a></li>
						{/if}
					{* 
					<li>{$pteam->partic1->title} + {$pteam->partic2->title} - {$pteam->payment_amount} Eur</li>
					*}
				{/foreach}
                        </ul> 
				
			</td>
		</tr>
		{else}
			
		{/if}
	{/foreach}
</table>

{else}
	<p>{GW::ln('/g/EMPTY_LIST')}</p>
{/if}

<br/><br/><br/>


<style>
	.orderlist{ border-collapse: collapse; }
	.orderlist td, .orderlist td{ padding: 2px 5px 2px 5px;  }
	.rowwitms td{ border: 1px solid silver; border-bottom:0; }
	.rownoitms td{ border: 1px solid silver; }
	.itmsrow td{ border: 1px solid silver; border-top:0; }
</style>
{include "default_close.tpl"}