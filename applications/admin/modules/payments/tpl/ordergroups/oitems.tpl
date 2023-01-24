{$domain=GW::s('SITE_URL')}


{if $export}
	<html>
		<head>
			<style>
				*{ font-family: DejaVu Sans; } 
				body{ font-size:12px }			
			</style>
			<meta charset="UTF-8">
			<link rel="stylesheet" href="{GW::s("ASSETS_URL_BASE")}../assets/vendor/icon-awesome/css/font-awesome.min.css">
		</head>
	<body>
{else}
	{include "default_open.tpl"}
	{$admin=1}
{/if}


{$GLOBALS.product_modification_display_mode=1}

{function "ordereditemimage"}
	{$obj=$item->obj}
	
	
	{$imurl=""}
	
	{if $obj->image}
		{$img = $obj->image}
		{$imurl="/tools/img/{$img->key}&v={$img->v}&size=100x100"}
	{elseif $obj->image_url}
		{$imurl="{$obj->image_url}&size=100x100"}
	{/if}
	{if $imurl}
		{if strpos($imurl,'http')===false}
			{$imurl="{GW::s("SITE_URL")}{$imurl}"}
		{/if}
		<a href="{$citem->link}"><img src="{$imurl}"></a>
	{/if}
	{if !$imurl}
		-
	{/if}
{/function}

<div class="row">
	<div class="col-md-6">
		<table class='details'>
			<tr><th>{GW::ln('/M/orders/ORDER_PLACED')}</th><td>{$order->placed_time}</td></tr>
			<tr><th>{GW::ln('/M/orders/FIELDS/pay_time')}</th><td>{$order->pay_time}</td></tr>
			
			{if $oder->deliverable}
			<tr><th>{GW::ln('/M/orders/SHIPPING_TO')}</th><td>{$order->name} {$order->surname}</td></tr>
			
			<tr><th>{GW::ln('/M/orders/FIELDS/email')}</th><td>{$order->email}</td></tr>
			<tr><th>{GW::ln('/M/orders/FIELDS/phone')}</th><td>{$order->phone}</td></tr>
			{else}
				<tr><th>{GW::ln('/M/orders/FIELDS/ordered_by')}</th><td>{$order->user->name} {$order->user->surname}</td></tr>

				<tr><th>{GW::ln('/M/orders/FIELDS/email')}</th><td>{$order->user->email}</td></tr>
				<tr><th>{GW::ln('/M/orders/FIELDS/phone')}</th><td>{$order->user->phone}</td></tr>			
			{/if}
			
			{if $order->delivery_opt==1}
				<tr><th>{GW::ln('/M/orders/FIELDS/city')}</th><td>{$order->city}</td></tr>
				<tr><th>{GW::ln('/M/orders/FIELDS/country')}</th><td>{GW_Country::singleton()->getCountryByCode($order->country, $ln)}</td></tr>
				{if $order->region}<tr><th>{GW::ln('/M/products/FIELDS/region')}</th><td>{$order->region}</td></tr>{/if}
				<tr><th>{GW::ln('/M/orders/FIELDS/address_l1')}</th><td>{$order->address_l1}</td></tr>
			{/if}
			
			{if $order->delivery_opt==1}
				<tr><th>{GW::ln('/M/orders/SUBTOTAL')}</th><td>{$order->amount_items} &euro;</td></tr>
				<tr><th>{GW::ln('/M/orders/SHIPPING')}</th><td>{$order->amount_shipping} &euro;</td></tr>
			{/if}
			
			{if $order->amount_discount}
				<tr><th>{GW::ln('/M/orders/DISCOUNT')}</th><td>-{$order->amount_discount} &euro;</td></tr>
				<tr><th>{GW::ln('/M/orders/DISCOUNT_CODE')}</th><td>{$order->discountcode->code} </td></tr>
			{/if}
			
			{if $order->amount_coupon}
				<tr><th>{GW::ln('/M/orders/COUPON')}</th><td>-{$order->amount_coupon} &euro;</td></tr>
				<tr><th>{GW::ln('/M/orders/FIELDS/code')}</th><td>{$order->discountcode->code} </td></tr>
			{/if}
			
			<tr><th>{GW::ln('/M/orders/ORDER_TOTAL')}</th><td>{$order->amount_total} &euro;</td></tr>
			
			{if $oder->deliverable}
				<tr><th>{GW::ln('/M/orders/FIELDS/delivery_type')}</th><td>{GW::ln("/M/orders/DELIVERY_{$order->delivery_opt}")}</td></tr>
			{/if}
			
			
			{if $order->pay_type}
				<tr><th>{GW::ln('/M/orders/PAY_METHOD')}</th><td>
					{if $order->pay_subtype}
						{$order->pay_subtype_human}
					{else}
						{GW::ln("/M/orders/PAY_METHOD_{$order->pay_type|strtoupper}")}
					{/if}
						
								{if $order->pay_details}
									{if $order->pay_type==3}
										<i class="fa fa-credit-card"></i> {$order->pay_details->number_start}...
									{/if}
								{/if}</td></tr>
				
			{/if}
			
						
			<tr><th></th><td></td></tr>
		</table>		
	</div>
	<div class="col-md-6">
		{GW::ln('/M/orders/ORDERED_ITEMS')}:
<table>
{foreach $order->items as $item}
	<tr>
		<td>
			
			{call name="ordereditemimage" size="100x100" alt=$alt}
		</td>
		<td>
			
			{$item->invoice_line} <br>
			{$item->qty} x {$item->unit_price} &euro;<br>
			{if $item->discount}<small>{GW::ln('/M/orders/DISCOUNT')}:</small> -{$item->discount*$item->qty} &euro;<br>{/if}
			{if $item->obj->remote_id}<a target="_blank" href="{GW::s('SITE_URL')}{$item->link}">{$item->obj->remote_id}</a><br>{*natos*}{/if}
			{if $m->feat(vat) && $item->vat_group}
				<small>{GW::ln('/M/orders/VAT_TARIFF')}</small>: {$item->vat_title},  <small>{GW::ln('/M/orders/VAT_PART')}</small>: {$item->vat_part} &euro; <br>
			{/if}			
		</td>
	</tr>

	
	
	

	
	
	{capture assign=alt}{$item->title}{/capture}

	
{/foreach}
</table>
	</div>
</div>

<style>
	#container { background-color: white }
	table td { padding: 2px; color: black}
	.details th{ text-align:right;padding-right:5px }
</style>
{if $export}
	</body>
	</html>
{else}
	{include "default_close.tpl"}
{/if}