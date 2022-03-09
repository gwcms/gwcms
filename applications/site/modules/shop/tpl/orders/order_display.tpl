{include "`$smarty.current_dir`/payselect.tpl"}

{function "product_list"}
	{foreach $item->getOrderedItems() as $oitem}
		{$product = $products_list[$oitem.prod_id]}

		<div class="col-md-2 mb-2">
			{capture assign=alt}{$product->title}{/capture}
			<a href="{$ln}/direct/shop/shop/p/?id={$product->id}" title="{$product->title|escape}">
			{call name="product_image" product=$product size="100x100" alt=$alt}
			</a>
		</div>

	{/foreach}		    
{/function}
			
{function "display_order"}
<div class="g-brd-around rounded g-mb-30 {if $item->id==$smarty.get.id}g-brd-blue{else}g-brd-gray-light-v4{/if}">
	<header class="g-bg-gray-light-v5 g-pa-20">
		<div class="row">
			<div class="col-sm-3 col-md-2 g-mb-20 g-mb-0--sm">
				<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">{GW::ln('/m/ORDER_PLACED')}</h4>
				<span class="g-color-black g-font-weight-300 g-font-size-13">{date('Y-m-d', strtotime($item->placed_time))}</span>
			</div>

			<div class="col-sm-3 col-md-1 g-mb-20 g-mb-0--sm">
				<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">{GW::ln('/m/TOTAL')}</h4>
				<span class="g-color-black g-font-weight-300 g-font-size-13">{$item->amount_total} &euro;</span>
			</div>


			<div class="col-sm-3 col-md-2 g-mb-20 g-mb-0--sm">
				<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">
					{if $item->delivery_opt==1} 
						{GW::ln('/m/SHIPPING_TO')} 
					{elseif $item->delivery_opt==3} 
						{GW::ln('/m/ORDER_PERSON')} 
					{else}
						{GW::ln('/m/ORDER_TAKES')} 
					{/if}
				</h4>
				<span class="g-color-black g-font-weight-300 g-font-size-13">{$item->name} {$item->surname}</span>
			</div>

			<div class="col-sm-3 col-md-2 g-mb-20 g-mb-0--sm">
				<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">
					 {GW::ln('/m/STATUS')}
				</h4>
				<span class="g-color-black g-font-weight-300 g-font-size-13">
					
					{if $item->pay_status==5}
						{GW::ln("/m/pay_status/5")}
					{else}
						{GW::ln("/m/status/`$item->status`")}
					{/if}
				</span>
			</div>

			{if $item->pay_type}
			<div class="col-sm-3 col-md-2 g-mb-20 g-mb-0--sm">
				<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">
					 {GW::ln('/m/PAY_METHOD')}
				</h4>
				<span class="g-color-black g-font-weight-300 g-font-size-13">
					{GW::ln("/m/PAY_METHOD_{$item->pay_type}")}
					{if $item->pay_details}
						{if $item->pay_type==3}
							<i class="fa fa-credit-card"></i> {$item->pay_details->number_start}...
						{/if}
					{/if}
				</span>
			</div>			
			{/if}




			<div class="col-sm-3 col-md-2 ml-auto text-sm-right">
				<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">{GW::ln('/m/ORDER')}<br/> # {$item->id}</h4>
				<a class="g-font-weight-300 g-font-size-13" target="_blank" href="{$app->buildUri('direct/shop/orders',[act=>doPrint,id=>$item->id,viewable=>1])}"><i class="fa fa-print"></i></a>
			</div>
		</div>
	</header>

<!-- Order Content -->
<div class="g-pa-20">



			{*
			<div class="mb-4">
			<h3 class="h5 mb-1">Delivered Yesterday</h3>
			<p class="g-color-gray-dark-v4 g-font-size-13">Your package was delivered per the instructions.</p>
			</div>
			*}
			<div class="row">

				{if !$no_buttons}
					{$no_buttons=($item->pay_status==7 || $item->status==6) && !$item->enatos}
				{/if}

				{if $no_buttons}
					{call "product_list"}
				{else}
					<div class="col-md-8">
						<div class="row">
						{call "product_list"}
						</div>
					</div>

					<div class="col-md-4">

					{if $item->pay_status==7 && $item->enatos}	
						<a class="gwUrlMod btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25 mb-4" href="#!" data-args='{ "act":"doDownloadPdfs", "id":"{$item->id}" }'><i class="fa fa-download"></i> {GW::ln('/m/DOWNLOAD_PDFS')}</a>
					{else}


						<div class="btn-group g-mr-10 g-mb-15">
	<button type="button" class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	  {if $item->pay_status==5 || $item->status==3}{GW::ln('/m/CHANGE_PAYMENT')}{else}{GW::ln('/m/MAKE_PAYMENT')}{/if}
	</button>
	<div class="dropdown-menu">
		<center>
			{call "pay_select_order"}
		  </center>
	{*
	  <div class="dropdown-divider"></div>
	  <a class="dropdown-item" href="#">More info about payment methods</a>
	*}    
	</div>
	</div>


						<a class="gwUrlMod btn btn-block g-brd-around g-brd-gray-light-v3 g-color-gray-dark-v3 g-bg-gray-light-v5 g-bg-gray-light-v4--hover g-font-size-12 text-uppercase g-py-12 g-px-25" href="#!"  data-args='{ "act":"doCancelOrder", "id":"{$item->id}" }'>{GW::ln('/m/CANCEL_ORDER')}</a>	


					{/if}										

					</div>
				{/if}

			</div>

		<!-- End Order Content -->
</div></div>
<!-- End Order Block -->
{/function}