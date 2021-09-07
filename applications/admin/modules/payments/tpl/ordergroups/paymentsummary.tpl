
{include "default_open.tpl"}

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
		</tr>
	</table>
</form>



	<table class="gwTable gwActiveTable">

	{foreach $list as $item}
		{$total=0}
		{$date=explode(' ',$item->insert_time)}
		<tr>
		
		<th>{$date[0]}</th>	
		<th>{$item->user->title}</th>
		<th>
			
			{$item->pay_type}
			{if $item->pay_type==paysera}
				{$item->pay_confirm->title}
			{/if}
		</th>
		<th>{$item->amount_total} EUR</th>		
		
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
						<td>{$oi->total} Eur</td>
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

{include "default_close.tpl"}