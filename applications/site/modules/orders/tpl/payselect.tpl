





{function "pay_select_order"}
	
	{foreach $methods as $method}
    <a class="gwUrlMod dropdown-item" href="#!" data-args='{ "act":"doOrderPay", "type":"{$method}", "id": "{$order->id}" }'>
	   <img src="/applications/site/assets/img/pay_{$method}.png" alt="{GW::ln('/m/PAY_METHOD_{strtoupper($method)}')}" title="{GW::ln("/m/PAY_METHOD_{strtoupper($method)}")}" style="height:60px"> 
	   <br />
	   <small style="word-wrap: break-word;max-width: 300px;word-break: break-all;  overflow-wrap: break-word; white-space: normal;">{GW::ln("/G/paymethods/description/{$method}")}</small>
    </a>		
	{/foreach}

   
{/function}

{function "pay_select_cart"}
	{if $m->feat('mergepaymethods')}
		{$mergepay = $m->prepareMergedPay($order)}
		
		<div class="col-md-12">
		<select onchange="gw_navigator.jump(location.href,{ paycountry: this.value })">
			{html_options options=$mergepay.country_opt selected=$mergepay.country}
		</select>
		</div>
		<br /><br />
		
		<table class='paytbl'>
		{foreach $mergepay.methods as $method}
			{$link=$app->buildUri('direct/orders/orders', [id=>$order->id,act=>doOrderPay,type=>$method->gateway,method=>$method->key], ['carry_params'=>1])}
			<tr >
				<td style='padding-right:25px;text-align:right'>	
					<a  type="button" href="{$link}">	   
						<img src="{$method->logo}" alt="{$method->title_tr|escape}" title="{$method->title_tr|escape}"  
					  style="max-height: 45px">
				      </a>
				</td>
				<td>
					<a type="button" href="{$link}">
					{$method->title_tr|escape}
					{*
					{$method->priority|escape}
					{$method->gateway|escape}
					{$method->group|escape}*}
					</a>
				</td>
			</tr>
		{/foreach}		
		</table>
	{else}
	
	
	{$methods=json_decode($m->config->pay_types)}
	
	{*<div class="text-right">*}

	<table class='paytbl'>
	{foreach $methods as $method}
		<tr >
			<td style='padding-right:25px;'>	
				<a class="gwUrlMod" type="button" data-args='{ "act":"doOrderPay", "type":"{$method}", "id": "{$order->id}" }'>	   
					<img  
						src="/applications/site/assets/img/pay_{$method}.png" alt="{GW::ln("/m/PAY_METHOD_{strtoupper($method)}")}" title="{GW::ln("/m/PAY_METHOD_{strtoupper($method)}")}" 
				     style="width:200px;"> 
				
			      </a>
			</td>
			<td>
				<a class="gwUrlMod" type="button" data-args='{ "act":"doOrderPay", "type":"{$method}", "id": "{$order->id}" }'>	
				{GW::ln("/G/paymethods/description/{$method}")}
				
				</a>
			</td>
		</tr>
	{/foreach}
	</table>



	{*</div>*}
	{/if}
	
	<style>
		.paytbl td{ padding-bottom: 25px;  }
	</style>
{/function}

