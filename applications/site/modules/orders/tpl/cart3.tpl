<!-- Payment & Review -->
{if $app->user && $app->user->isRoot()}
	<a href="/admin/{$ln}/payments/config" target="_blank">[config]</a>
{/if}

<div id="step3">
	<div class="row">
		<div class="col-md-8 g-mb-30">
			<div class="g-brd-bottom g-brd-gray-light-v3 g-pb-30 g-mb-30">
				
				
				{if $m->feat(discountcode) && $order->amount_total==0}
					<center>
					<a href="{$app->buildUri('direct/orders/orders', [act=>doOrderPay,id=>$order->id,type=>'zeroprice'])}" class="btn u-btn-indigo btn-{$version} rounded-0">
							
							{GW::ln('/m/CONFIRM_ORDER')}
					</a>
					</center>
				{else}

					{if count($pay_methods) > 1 || $m->feat('mergepaymethods')}
						{include "`$m->tpl_dir`payselect.tpl"}
						<p>
							{GW::ln('/m/PAY_METHOD_SELECT')}:
						</p>
						<div class="row">

							{call "pay_select_cart"}
						</div>

					{else}
						<center>
							{$order=GW::$globals.site_cart}
					    <a href="{$m->buildUri('direct/orders/orders', [act=>doOrderPay,id=>$order->id,type=>$pay_methods.0])}" class="btn btn-primary">
						<i class="fa fa-credit-card g-mr-2"></i>
						{GW::ln('/m/PROCEED_PAYMENT')} {$order->amount_total} &euro;
					      </a>



						{if $m->feat('otherpayee')}
							<br><br>
							<a href="{$app->buildUri('direct/orders/orders/otherpayee', [id=>$order->id])}" class="btn u-btn-indigo btn-{$version} rounded-0">
								<i class="fa fa-credit-card g-mr-2"></i>
								{GW::ln('/m/OTHERPAYEE')}

							</a>	
						{/if}	
						</center>					
					{/if}

				{/if}
				
				
			</div>	
		</div>


		{include "`$m->tpl_dir`/summary.tpl"}

	</div>
</div>
<!-- End Payment & Review -->