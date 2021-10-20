

{function "pay_select_order"}
	
	{for $i=1; $i<=4; $i++}
    <a class="gwUrlMod dropdown-item" href="#!" data-args='{ "act":"doPay", "id":"{$item->id}", "gw":"{$i}" }'>
	   <img src="/applications/site/assets/img/pay{$i}.png" alt="{GW::ln('/m/PAY_METHOD_{$i}')}" title="{GW::ln("/m/PAY_METHOD_{$i}")}" style="height:60px"> 
	   <br />
	   <small style="word-wrap: break-word;max-width: 300px;word-break: break-all;  overflow-wrap: break-word; white-space: normal;">{GW::ln("/G/paymethods/description/{$i}")}</small>
    </a>		
	{/for}

   
{/function}

{function "pay_select_cart"}
	
	{*<div class="text-right">*}
	<table class='paytbl'>
	{for $i=1; $i<=4; $i++}
		<tr >
			<td style='padding-right:25px;'>	
				<a class="gwUrlMod" type="button" data-args='{ "act":"doPay", "gw":"{$i}" }'>	   
					<img src="/applications/site/assets/img/pay{$i}.png" alt="{GW::ln("/m/PAY_METHOD_{$i}")}" title="{GW::ln("/m/PAY_METHOD_{$i}")}" 
				     style="width:200px"> 
				
			      </a>
			</td>
			<td>
				<a class="gwUrlMod" type="button" data-args='{ "act":"doPay", "gw":"{$i}" }'>	
				{GW::ln("/G/paymethods/description/{$i}")}
				</a>
			</td>
		</tr>
	{/for}
	</table>

	<style>
		.paytbl td{ padding-bottom: 25px;  }
	</style>

	{*</div>*}

{/function}