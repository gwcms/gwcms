<!-- Payment & Review -->
{if $app->user && $app->user->isRoot()}
	<a href="/admin/{$ln}/payments/config" target="_blank">[config]</a>
{/if}

<div id="step3">
	<div class="row">
		<div class="col-md-8 g-mb-30">
			<div class="g-brd-bottom g-brd-gray-light-v3 g-pb-30 g-mb-30">
				
				
		
	

				{if count($pay_methods) > 1}
					{include "`$m->tpl_dir`payselect.tpl"}
					<p>
						{GW::ln('/m/PAY_METHOD_SELECT')}:
					</p>
					<div class="row">
						
						{call "pay_select_cart"}
					</div>

				{else}
					<center>
						{$order=$GLOBALS.site_cart}
				    <a href="{$m->buildUri('direct/orders/orders', [act=>doOrderPay,id=>$order->id,type=>$pay_methods.0])}" class="btn btn-primary">
					<i class="fa fa-credit-card g-mr-2"></i>
					{GW::ln('/m/PROCEED_PAYMENT')} {$order->amount_total} &euro;
				      </a>
				      </center>
				{/if}

				
				
			</div>	
		</div>


		{include "`$m->tpl_dir`/summary.tpl"}

	</div>
</div>
<!-- End Payment & Review -->