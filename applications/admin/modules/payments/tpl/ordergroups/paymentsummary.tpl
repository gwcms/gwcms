
{include "default_open.tpl"}
{$totaltotal=0}

<form action="{$smarty.server.REQUEST_URI}">
	<table class="gwTable">
		<tr>

			<td>
				Data nuo
			</td>
			<td>
				{include file="elements/inputs/date.tpl" input_name="date_from" value=$date_from}
			</td>
			<td>
				Data iki
			</td>	
			<td>
				{include file="elements/inputs/date.tpl" input_name="date_to" value=$date_to}
			</td>			
			
			<td>
				<input type="submit" class="btn btn-primary">
			</td>
			
			{if $smarty.get.date_to}
			<td>
				
	{$url=$app->buildUri("payments/orderitems",[
		pay_interval=>"{$smarty.get.date_from},{$smarty.get.date_to}",orderflds=>1,pay_test=>0,
		flds=>"group_id,user_title,pay_time,type,invoice_line,qty,unit_price,total",ord=>'payment_status DESC,pay_time DESC',noactions=>1])}
	{*iconclass="fa fa-globe"*}
				<a href="{$url}" class="btn btn-primary">Nesugrupuotas</a>
			</td>
			{/if}
			
			{if $app->user->isRoot() }
			<td>
				Nuasmeninti {include file="elements/inputs/bool.tpl" input_name="nuasmenintas" value=$smarty.get.nuasmenintas}
			</td>
			{/if}			
		</tr>
	</table>
</form>



	<table class="gwTable gwActiveTable">

	{foreach $list as $item}
		{$total=0}
		{$date=explode(' ',$item->insert_time)}
		{$pdate=explode(' ',$item->pay_time)}
		<tr>
		<th>{$item->id}</th>
		<th><span title="apmokėjimo laikas | krepš sukūrimo laikas: {$date[0]}">{$pdate[0]}</span></th>	
		<th>
			{if $smarty.get.nuasmenintas}
				userid:{$item->user->id}
			{else}
				{if $item->company}{$item->company}{if $item->company_code} | {$item->company_code} <small>({$item->user->title})</small> {/if}{else}
					{if $item->user}
					{$item->user->title}
					{else}
						{$order->name} {$order->surname} {$order->email}
					{/if}
				{/if}
			{/if}
		</th>
		<th>
			
			{$item->pay_type}
			
			{if $item->pay_type==paysera}
				{$item->pay_confirm->title}
			{/if}
		</th>
		<th>{$item->amount_total} {$totaltotal=$totaltotal+$item->amount_total}</th>		
		
		</tr>
		<tr>
			<td colspan='3'>
				<table class='gwTable'>
				{foreach $orderitems[$item->id] as $oi}
					{$total=$total + $oi->total}
					<tr>
						<td>{$oi->type}</td>
						<td>{$oi->invoice_line}</td>
						<td>{$oi->qty} x {$oi->unit_price} Eur</td>
						<td>{$oi->total}</td>
						<td>
							{foreach $app->pay_confirm_list as $payitm}
								{$payitm->title}{if !$payitm@last},{/if}
							{/foreach}
						</td>						
					</tr>
				{/foreach}



				</table>

			</td>
		</tr>
	{/foreach}
	</table>

	<hr>
	<b>{GW::l('/m/FIELDS/amount_total')}</b>: {$totaltotal} EUR
	
{include "default_close.tpl"}