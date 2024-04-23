{include "product_display.tpl"}
{*include "inputs/inputs.tpl"*}

{$order=$m->order}

<div id="summary" class="col-md-4 g-mb-30" >
  <!-- Summary -->
  <div class="g-bg-gray-light-v5 g-pa-20 g-pb-50 mb-4">
    <h4 class="h6 text-uppercase mb-3">{GW::ln('/m/SUMMARY')}</h4>

    {if $step==1 && $m->order->deliverable}
    <!-- Accordion -->
    <div id="accordion-01" class="mb-4" role="tablist" aria-multiselectable="true">
      <div id="accordion-01-heading-01" class="g-brd-y g-brd-gray-light-v2 py-3" role="tab">
	<h5 class="g-font-weight-400 g-font-size-default mb-0">
	  <a class="g-color-gray-dark-v4 g-text-underline--none--hover" href="#accordion-01-body-01" data-toggle="collapse" data-parent="#accordion-01" aria-expanded="false" aria-controls="accordion-01-body-01">{GW::ln('/m/ESTIMATE_SHIPPING')}
	    <span class="ml-3 fa fa-angle-down"></span></a>
	</h5>
      </div>
      <div id="accordion-01-body-01" class="collapse" role="tabpanel" aria-labelledby="accordion-01-heading-01">
	<div class="g-py-10">
		
		{*call input field="delivery_type" options=$delivery_opts type=select empty_option=1 value=$order->delivery_type}*}
		
		{GW::ln('/m/DELIVERY_PRICES_SHORT')}
	</div>
      </div>
    </div>
    <!-- End Accordion -->
    {/if}

    {if $order->amount_total != $order->amount_items}
	<div class="d-flex justify-content-between mb-2">
	  <span class="g-color-black">{GW::ln('/m/SUBTOTAL')}</span>
	  <span class="g-color-black g-font-weight-300">{$order->amount_items} &euro;</span>
	</div>
    {/if}
    
    {if $m->order->deliverable}
	<div class="d-flex justify-content-between">
	  <span class="g-color-black">{GW::ln('/m/SHIPPING')}</span>
	  <span class="g-color-black g-font-weight-300">{if $step > 2 || $order->amount_shipping}{$order->amount_shipping} &euro;{else}?{/if}</span>
	</div>	
    {/if}
    
    
    {if $order->amount_discount}
    <div class="d-flex justify-content-between mb-2 ">
      <span class="g-color-lightred">{GW::ln('/m/DISCOUNT')}</span>
      <span class="g-color-lightred g-font-weight-300">-{$order->amount_discount} &euro;</span>
    </div>
    {/if} 
    
    {if $order->amount_coupon}
    <div class="d-flex justify-content-between mb-2 g-color-lightred">
      <span class="g-color-lightred">{GW::ln('/m/COUPON')}</span>
      <span class="g-color-lightred g-font-weight-300">-{$order->amount_coupon} &euro;</span>
    </div>
    {/if}      
    
    
  
	<div class="d-flex justify-content-between">
	  <span class="g-color-black">{GW::ln('/m/ORDER_TOTAL')}</span>
	  <span class="g-color-black g-font-weight-300">{$order->amount_total} &euro;</span>
	 </div>

	
	

    
  </div>
  <!-- End Summary -->

  
  
	{if $step==1}
	<button style="display:none" id="updateCart" class="btn btn-block u-btn-outline-black g-brd-gray-light-v1 g-bg-black--hover g-font-size-13 text-uppercase g-py-15 mb-4" type="button">{GW::ln('/m/UPDATE_SHOPPING_CART')}</button>

		<button id="proceedCheckout" 
				class="btn btn-block u-btn-primary g-font-size-13 text-uppercase g-py-15 mb-4" type="button"
		{if $m->order->deliverable || $m->auser}
			data-next-step="#step2" data-step="2">
			{GW::ln('/m/PROCEED_DELIVERY')}
		{else}
			data-next-step="#step3" data-step="3">
					{GW::ln('/m/PROCEED_PAYMENT')}
		{/if}
		</button>
	{elseif $step==2}
		
	{/if}
  {*
  <a class="d-inline-block g-color-black g-color-primary--hover g-text-underline--none--hover mb-3" href="#!">
    <i class="mr-2 fa fa-info-circle"></i>{GW::ln('/m/DELIVERY')}
  </a>
*}
{*  
  <!-- Accordion -->
  <div id="accordion-02" role="tablist" aria-multiselectable="true">
    <div id="accordion-02-heading-02" role="tab">
      <h5 class="g-font-weight-400 g-font-size-default mb-0">
	<a class="g-color-black g-text-underline--none--hover" href="#accordion-02-body-02" data-toggle="collapse" data-parent="#accordion-02" aria-expanded="false" aria-controls="accordion-02-body-02">{GW::ln('/m/APPLY_DISCOUNT_CODE')}
	  <span class="ml-3 fa fa-angle-down"></span></a>
      </h5>
    </div>
    <div id="accordion-02-body-02" class="collapse" role="tabpanel" aria-labelledby="accordion-02-heading-02">
      <div class="input-group rounded g-pt-15">
	<input class="form-control g-brd-gray-light-v1 g-brd-right-none g-color-gray-dark-v3 g-placeholder-gray-dark-v3" type="text" placeholder="{GW::ln('/m/ENTER_DISCOUNT_CODE')}">
	<span class="input-group-append g-brd-gray-light-v1 g-bg-white">
	  <button class="btn u-btn-primary" type="submit">{GW::ln('/m/APPLY')}</button>
	</span>
      </div>
    </div>
  </div>
  <!-- End Accordion -->
*}
		{if $step>1}
			
			{$ordered_items = GW::$globals.site_cart->items}


			<!-- Accordion -->
			<div id="accordion-05" class="mb-4" role="tablist" aria-multiselectable="true">
				<div id="accordion-05-heading-05" class="g-brd-y g-brd-gray-light-v2 py-3" role="tab">
					<h5 class="g-font-weight-400 g-font-size-default mb-0">
						<a class="g-color-gray-dark-v4 g-text-underline--none--hover" href="#accordion-05-body-05" data-toggle="collapse" data-parent="#accordion-05" aria-expanded="false" aria-controls="accordion-05-body-05">{GW::ln('/m/PRODUCTS')}: {$totalqty} 
							<span class="ml-3 fa fa-angle-down"></span></a>
					</h5>
				</div>
				<div id="accordion-05-body-05" class="collapse" role="tabpanel" aria-labelledby="accordion-05-heading-05">
					<div class="g-py-15">
						<ul class="list-unstyled mb-3">

						{foreach $ordered_items as $item}
							
							{$obj=$item->obj}
							<!-- Product -->
							<li class="d-flex justify-content-start mb-4">
								
								{call cart_item_img_or_category class="g-width-100 g-height-100 mr-3" imsize="100x100"}
								
								<div class="d-block">
									<h4 class="h6 g-color-black">{$item->title}</h4>
									<ul class="list-unstyled g-color-gray-dark-v4 g-font-size-12 g-line-height-1_4 mb-1">
										<li>{GW::ln('/m/QTY')}: {$item->qty}</li>
									</ul>
									<span class="d-block g-color-black g-font-weight-400">{if $item->qty}{$item->qty} x {/if}{$item->unit_price} &euro;</span>
									
									{if $item->discount}
										<span class="d-block g-color-black g-font-weight-400 g-color-lightred"><small>{GW::ln('/m/DISCOUNT')}:</small> -{$item->discount*$item->qty} &euro;</span>
									{/if}
									{call cart_item_expirable_prop}
								</div>
							</li>
							<!-- End Product -->
							
						{/foreach}
							
						</ul>
					</div>
				</div>
			</div>
			<!-- End Accordion -->
		{/if}
			
		{if $step>2 && $order->deliverable}
			
			{if ($order->delivery_opt==2 || $order->delivery_opt==3)}
				<!-- Ship To -->	
				<div class="mb-5">
					<div class="d-flex justify-content-between g-brd-bottom g-brd-gray-light-v3 g-mb-15">
						<h4 class="h6 text-uppercase mb-3">{GW::ln('/m/CONTACT_INFO')}</h4>
						{if $step < 4}
						<span class="g-color-gray-dark-v4 g-color-black--hover g-cursor-pointer">
							<a href="#" class="gwUrlMod" href="#" data-args='{ "step":2 }'>
							<i class="fa fa-pencil"></i>
							</a>
						</span>
						{/if}
					</div>
					<ul class="list-unstyled g-color-gray-dark-v4 g-font-size-15">
						<li class="g-my-3">{$order->name|escape} {$order->surname|escape}</li>
						<li class="g-my-3">{$order->email|escape}</li>
						<li class="g-my-3">{$order->phone|escape}</li>
					</ul>
				</div>
				<!-- End Ship To -->				
			{else}
				<!-- Ship To -->	
				<div class="mb-5">
					<div class="d-flex justify-content-between g-brd-bottom g-brd-gray-light-v3 g-mb-15">
						<h4 class="h6 text-uppercase mb-3">{GW::ln("/m/DELIVERY_`$order->delivery_opt`")}</h4>
						<a href="#" class="gwUrlMod" href="#" data-args='{ "step":2 }'>
						<span class="g-color-gray-dark-v4 g-color-black--hover g-cursor-pointer">
							<i class="fa fa-pencil"></i>
						</span>
						</a>
					</div>
					<ul class="list-unstyled g-color-gray-dark-v4 g-font-size-15">
						<li class="g-my-3">{$order->name|escape} {$order->surname|escape}</li>
						<li class="g-my-3">{$order->email|escape}</li>
						<li class="g-my-3">{$order->address_l1|escape}</li>
						<li class="g-my-3">{$order->city|escape}</li>
						<li class="g-my-3">{$order->postcode}</li>
						{if $m->config->international_delivery}
							<li class="g-my-3">{GW_Country::singleton()->getCountryByCode($order->country, $ln)|escape}</li>
						{/if}
						<li class="g-my-3">{$order->phone|escape}</li>
					</ul>
				</div>
				<!-- End Ship To -->		

			{/if}
		
		{/if}
		
		{if $step < 2 && $m->feat(discountcode)}

			{if $order->discount_id}
				<div>
					{GW::ln('/m/DISCOUNT_CODE')}: {$order->discountcode->code} <a class="gwUrlMod" data-args='{ "act": "doUnsetDiscount" }'><i class="fa fa-times"></i></a>
				</div>
			{else}
                  <!-- Accordion -->
                  <div id="accordion-02" role="tablist" aria-multiselectable="true">
                    <div id="accordion-02-heading-02" role="tab">
                      <h5 class="g-font-weight-400 g-font-size-default mb-0">
                        <a class="g-color-black g-text-underline--none--hover" href="#accordion-02-body-02" data-toggle="collapse" data-parent="#accordion-02" aria-expanded="false" aria-controls="accordion-02-body-02">{GW::ln('/m/APPLY_DISCOUNT_CODE')}
                          <span class="ml-3 fa fa-angle-down"></span></a>
                      </h5>
                    </div>
                    <div id="accordion-02-body-02" {if !$smarty.get.discountcode}class="collapse"{/if} role="tabpanel" aria-labelledby="accordion-02-heading-02">
                      <div class="input-group rounded g-pt-15">
                        <input id="discountCode" name="discountcode" 
			       class="form-control g-brd-gray-light-v1 g-brd-right-none g-color-gray-dark-v3 g-placeholder-gray-dark-v3" 
			       type="text" placeholder="{GW::ln('/m/ENTER_DISCOUNT_CODE')}" value="{$smarty.get.discountcode}">
                        <span class="input-group-append g-brd-gray-light-v1 g-bg-white">
				<button class="btn u-btn-primary" id="applyDiscount" disabled="disabled">{GW::ln('/m/APPLY')}</button>
                        </span>
                      </div>
                    </div>
                  </div>
			
	<script>

			$(function(){

				$('#applyDiscount').click(function(){
					$('#step').val(1);
					$('#cartList').submit()
				})

				$('#discountCode').keydown(function(){
					console.log('value:'+$(this).val());
					if(this.value){
						console.log('not disabled');
						$('#applyDiscount').attr('disabled',false);
					}else{
						$('#applyDiscount').attr('disabled','disabled');
					}
				})
				.change(function(){  $(this).keydown();  })
				.on('paste', function(){ console.log('paste event detected'); setTimeout(function(){ $('#discountCode').keydown(); },100)  })
			})
	</script>			
			
			
			
                  <!-- End Accordion -->
			{/if}
		{/if}		

</div>