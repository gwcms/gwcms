{include "default_open.tpl"}


{if $smarty.get.pay==6}
	<style>
		.payinfotbl th{ padding-right: 15px; }
	</style>
	
	<table class="payinfotbl">
		<tr>
			<th>{GW::ln('/m/PAY_REASON')}</th>
			<td>{$request->title}</td>
		</tr>
		<tr>
			<th>{GW::ln('/m/AMOUNT')}</th>
			<td>{$request->amount} EUR</td>
		</tr>
		<tr>
			<th>{GW::ln('/m/CUSTOMER_EMAIL')}</th>
			<td>{$request->customer_email}</td>
		</tr>		
	</table>
		
	<br />
	{if !$request->status}
		<a class='btn btn-primary' href='{$payurl}'>{GW::ln('/m/REPEAT_PAYMENT')}</a>
	{/if}
	<br /><br />
{/if}

{include "default_close.tpl"}