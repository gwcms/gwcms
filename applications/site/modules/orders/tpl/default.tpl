{include "default_open.tpl"}

<br/>
<h2>{GW::ln('/m/YOUR_ORDERS')} {if $smarty.get.canceled}<small>{GW::ln('/m/CANCELED')}</small>{/if}</h2> 
<br/>

{function orderactions}
	{if $order->payment_status!=7 && $order->amount_total && $order->active}
		<a href="{$m->buildUri('direct/orders/orders', [act=>doOrderPay,id=>$order->id])}" class="btn u-btn-brown btn-md rounded-0">
						<i class="fa fa-credit-card g-mr-2"></i>
						{GW::ln('/g/PROCEED_PAYMENT')}
					</a>
					
					
					
		{if $m->feat('otherpayee')}		
			<a href="{$m->buildUri('otherpayee', [id=>$order->id])}" class="btn u-btn-indigo btn-{$version} rounded-0">
				<i class="fa fa-credit-card g-mr-2"></i>
				{GW::ln('/g/OTHERPAYEE')}
				
			</a>	
		{/if}
					
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

		<tr><th>#</th><th>{GW::ln('/g/CREATE_DATE')}</th><th>{GW::ln('/m/PARTS')}</th><th>{{GW::ln('/g/CART_TOTAL')}}</th></tr>
	{foreach $list as $order}
			{if $smarty.get.orderid && $order->id!=$smarty.get.orderid}{continue}{/if}

			
		
		
		{$citems = $order->items}
		{$items_cnt=count($citems)}


	{if !$items_cnt}{continue}{/if}

	<tr class="orderinfo {if $smarty.get.id==$order->id}alert-warning{/if}{if $citems}rowwitms{else}rownoitms{/if}">
		<td>{$order->id}</td>
		<td>{date('Y-m-d H:i',strtotime($order->insert_time))}</td>
		<td>{$items_cnt}</td>
		<td>
			{$order->amount_total} Eur
		</td>
		<td>
			{if $order->active}
				{if $order->payment_status==7}
					{$link=$m->buildDirectUri('prepareinvoice', [id=>$order->id])}
				{else}
					{$link=$m->buildDirectUri('prepareinvoice', [id=>$order->id,preinvoice=>1])}
				{/if}
				<a href="{$link}"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {GW::ln('/m/INVOICE')}</a>
			{/if}
			
			{if $admin_enabled}
				<a target="_blank" href='/admin/{$ln}/payments/ordergroups/{$order->id}/form'><i class='fa fa-pencil-square-o text-warning'></i></a>
			{/if}
		</td>

	</tr>

	{if $citems}
		<tr class="itmsrow {if $smarty.get.id==$order->id}alert-warning{/if}">
			<td colspan="5">

				<ul class="u-alert-list g-mt-10">
					{foreach $citems as $citem}
						{$obj=$citem->obj}

						
						<li>
							
								{GW::ln("/g/CART_ITM_{$citem->obj_type}")} - 
								{if $citem->link}<a href="{$citem->link}">{/if}
									{if $obj->context_short}<i>{$obj->context_short}</i> - {/if} {$obj->title}
								{if $citem->link}</a>{/if}
							
							{$citem->qty}x{$citem->unit_price} Eur 
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
				
				{if !$smarty.get.orderid}
					{call orderactions version=xs}
				{else}
					{call orderactions version=md}
				{/if}
					
									
				{if $order->adm_message}
					<div style='margin-top:5px'>
					<i class="fa fa-info-circle"></i> {$order->adm_message}
					</div>
				{/if}
			</td>
		</tr>
		

	{else}

	{/if}
	<tr><td colspan="5" style="border-left:0;border-righ:0;height:5px">&nbsp;</td></tr>
{/foreach}
</table>

{else}
	<p>{GW::ln('/g/EMPTY_LIST')}</p>
{/if}





{if !$smarty.get.canceled && $canceled_count}
	<hr>
	
	{if $smarty.get.orderid}
		<a href="{$app->buildUri(false,$args)}"> {GW::ln('/m/YOUR_ORDERS')} {GW::ln('/m/ALL_ORDERS')} <b>{count($list)}</b></a><br>
	{/if}	
	<a href="{$app->buildUri(false,$smarty.get+[canceled=>1])}"> {GW::ln('/m/YOUR_ORDERS')} {GW::ln('/m/CANCELED')} <b>{$canceled_count}</b></a>
{/if}

{if $smarty.get.canceled}
	{$args=$smarty.get}
	{gw_unassign var=$args.canceled}
	<hr>
	<a href="{$app->buildUri(false,$args)}"> &laquo; {GW::ln('/m/YOUR_ORDERS')}</a>
{/if}


<br/><br/><br/>


<style>
	.orderlist{ border-collapse: collapse; }
	.orderlist td, .orderlist td{ padding: 2px 5px 2px 5px;  }
	.rowwitms td{ border: 1px solid silver; border-bottom:0; }
	.rownoitms td{ border: 1px solid silver; }
	.itmsrow td{ border: 1px solid silver; border-top:0; }
	.orderinfo td { background-color: #eee }
</style>




{include "default_close.tpl"}