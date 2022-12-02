{if !$m->args.clean}
	{include "default_open.tpl"}
{/if}


<div class="container {if !GW::s('ECOMMERCE_ISOLATION')} g-pt-70 g-pb-30{/if}">
        <div class="row">
		{include "user_menu_incontainer.tpl"}
		<div class="col-lg-9 g-mb-50">
			{if $smarty.get.canceled || $canceled_count}
	
			<!-- Links -->
			<ul class="list-inline g-brd-bottom--sm g-brd-gray-light-v3 mb-5">
				{$active="g-brd-bottom g-brd-2 g-brd-primary g-color-main g-color-black g-font-weight-600 g-text-underline--none--hover g-px-10 g-pb-13"}
				{$passive="g-brd-bottom g-brd-2 g-brd-transparent g-color-main g-color-gray-dark-v4 g-color-primary--hover g-text-underline--none--hover g-px-10 g-pb-13"}

				<li class="list-inline-item g-pb-10 g-pr-10 g-mb-20 g-mb-0--sm">
					<a class="gwUrlMod {if !$smarty.get.canceled}{$active}{else}{$passive}{/if}"  href="#calnceledOrders" data-args='{json_encode([canceled=>null,summary=>null])}'>{GW::ln('/m/VIEWS/orders')}</a>
				</li>
				{*
				<li class="list-inline-item g-pb-10 g-px-10 g-mb-20 g-mb-0--sm">
				<a class="g-brd-bottom g-brd-2 g-brd-transparent g-color-main g-color-gray-dark-v4 g-color-primary--hover g-text-underline--none--hover g-px-10 g-pb-13" href="page-open-orders-1.html">{GW::ln('/m/A')}</a>
				</li>
				*}
				<li class="list-inline-item g-pb-10 g-pl-10 g-mb-20 g-mb-0--sm">
					<a class="gwUrlMod {if $smarty.get.canceled}{$active}{else}{$passive}{/if}" href="#calnceledOrders" data-args='{json_encode([canceled=>1,summary=>null])}'>{GW::ln('/m/CANCELED_ORDERS')} {if $canceled_count}({$canceled_count}){/if}</a>
				</li>
				
				
				{if $m->feat('ordersummary')}
				<li class="list-inline-item g-pb-10 g-pr-10 g-mb-20 g-mb-0--sm">
					<a class="gwUrlMod {if $smarty.get.summary}{$active}{else}{$passive}{/if}"  href="#summaryOrders" data-args='{json_encode([summary=>1,canceled=>null])}'>{GW::ln('/m/VIEWS/orderssummary')}</a>
				</li>
				{/if}
			</ul>
			<!-- End Links -->

			{else}
				{if !GW::s('ECOMMERCE_ISOLATION')}
				<h2>{GW::ln('/m/YOUR_ORDERS')} {if $smarty.get.canceled}<small>{GW::ln('/m/CANCELED')}</small>{/if}</h2> 
				<br/>	
				{/if}
			{/if}
			
			
			{include "`$m->tpl_dir`/orders.tpl"}
			
			{if !GW::s('ECOMMERCE_ISOLATION')}<br/><br/><br/>{/if}
			
			</div>
		</div>
</div>


			
{if !$m->args.clean}
	{include "default_close.tpl"}
{/if}