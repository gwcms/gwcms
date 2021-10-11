{include "default_open.tpl"}

<br/>
<h2>{GW::ln('/m/YOUR_ORDERS')}</h2>
<br/>

{function orderactions}
	{if $order->payment_status!=7 && $order->amount_total}
		<a href="{$m->buildUri('direct/orders/orders', [act=>doOrderPay,id=>$order->id])}" class="btn u-btn-brown btn-{$version} rounded-0">
						<i class="fa fa-credit-card g-mr-2"></i>
						{GW::ln('/g/PROCEED_PAYMENT')}
					</a>
		{if $order->banktransfer_allow}
			<a href="{$m->buildUri('paybanktransfer', [id=>$order->id])}" class="btn u-btn-orange btn-{$version} rounded-0">
				<i class="fa fa-credit-card g-mr-2"></i>
				{GW::ln('/g/PROCEED_PAYMENT_BANKTRANSFER')}
			</a>					
		{/if}


		<a href="{$m->buildUri(false, [act=>doCancelOrder,id=>$order->id])} " class="btn u-btn-brown btn-{$version} rounded-0">
			<i class="fa fa-times"></i> {GW::ln('/g/CANCEL')}
		</a>
		
		{if $app->user->get('ext/cart_id') != $order->id && $order->get('extra/bt_confirm_cnt') < 1}
			<a href="{$m->buildUri(false, [act=>doOpenOrder,id=>$order->id])} " class="btn u-btn-primary btn-{$version} rounded-0" title="{GW::ln('/m/VIEWS/doOpenOrder')}">
				<i class="fa fa-shopping-cart"></i> {GW::ln('/m/VIEWS/doOpenOrder_short')}
			</a>			
		{/if}
	{/if}
	
{/function}


{if $list}

	<table class="orderlist">

		<tr><th>#</th><th>{GW::ln('/g/CREATE_DATE')}</th><td>{GW::ln('/m/PARTS')}</td><th>{{GW::ln('/g/CART_TOTAL')}}</th></tr>
	{foreach $list as $order}
			{if $smarty.get.orderid && $order->id!=$smarty.get.orderid}{continue}{/if}

			
		
		
		{$citems = $order->items}
		{$items_cnt=count($citems)}


	{if !$items_cnt}{continue}{/if}

	<tr class="{if $smarty.get.id==$order->id}alert-warning{/if}{if $citems}rowwitms{else}rownoitms{/if}">
		<td>{$order->id}</td>
		<td>{$order->insert_time}</td>
		<td>{$items_cnt}</td>
		<td>
			{$order->amount_total} Eur
			
			{if !$smarty.get.orderid}
				{call orderactions version=xs}
			{/if}
				
			
		</td>
		<td>
			
			{if $order->payment_status==7}
				{$link=$m->buildDirectUri('prepareinvoice', [id=>$order->id])}
			{else}
				{$link=$m->buildDirectUri('prepareinvoice', [id=>$order->id,preinvoice=>1])}
			{/if}
			<a href="{$link}"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {GW::ln('/m/INVOICE')}</a>

		</td>

	</tr>

	{if $citems}
		<tr class="itmsrow {if $smarty.get.id==$order->id}alert-warning{/if}">
			<td colspan="5">

				<ul class="u-alert-list g-mt-10">
					{foreach $citems as $citem}
						{$obj=$citem->obj}

						<li>{GW::ln("/g/CART_ITM_{$citem->obj_type}")} - {if $obj->context_short}<i>{$obj->context_short}</i> - {/if} {$obj->title} {$citem->qty}x{$citem->unit_price} Eur 
							{if !$order->payment_status!=7 && $item->can_remove}
								<a href="{$m->buildUri(false, [act=>doCartItemRemove,id=>$citem->id])}"><i class="fa fa-times"></i></a>
								{/if}

							{if $citem->expirable  && $order->payment_status!=7}
								{if $citem->expires_secs > 0}
									<span class="countdown" data-expires="{$citem->expires_secs}">{GW_Math_Helper::uptime($citem->expires_secs)}</span>
								{else}
									{GW::ln('/m/EXPIRED')}
								{/if}
							{/if}
						</li>
						{* 
						<li>{$pteam->partic1->title} + {$pteam->partic2->title} - {$pteam->payment_amount} Eur</li>
						*}
					{/foreach}
				</ul> 
				
				{if $smarty.get.orderid}
					{call orderactions version=md}
				{/if}						

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