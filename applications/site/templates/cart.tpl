{if $app->user}
	{$citems = $cart->items}
	{if $citems}
		{*{$cartlink="{$ln}/direct/orders/orders?id={$cart->id}"} old*}
		{$cartlink="{$ln}/direct/orders/orders/cart"}
<!-- Basket -->
<div class="u-basket d-inline-block g-z-index-3">
  <div class="g-py-10 g-px-6">
    <a href="" id="basket-bar-invoker" class="u-icon-v1 g-color-white-opacity-0_8 g-color-white--hover g-font-size-17 g-text-underline--none--hover"
       aria-controls="basket-bar"
       aria-haspopup="true"
       aria-expanded="false"
       data-dropdown-event="hover"
       data-dropdown-target="#basket-bar"
       data-dropdown-type="css-animation"
       data-dropdown-duration="300"
       data-dropdown-hide-on-scroll="false"
       data-dropdown-animation-in="fadeIn"
       data-dropdown-animation-out="fadeOut">
      <span class="u-badge-v1--sm g-color-primary g-bg-white g-font-size-11 g-line-height-1_4 g-rounded-50x g-pa-4" style="top: 7px !important; right: 3px !important;">{count($citems)}</span>
      <i class="icon-hotel-restaurant-105 u-line-icon-pro"></i>
    </a>
  </div>



  <div id="basket-bar" class="u-basket__bar u-dropdown--css-animation u-dropdown--hidden g-text-transform-none g-bg-white g-brd-around g-brd-gray-light-v4"
       aria-labelledby="basket-bar-invoker">
    <div class="g-brd-bottom g-brd-gray-light-v4 g-pa-15 g-mb-10">
      <span class="d-block h6 text-center text-uppercase mb-0">{GW::ln('/g/SHOPPING_CART')}</span>
    </div>
    <div class="js-scrollbar g-height-200">
      
	    
      <span style='display:none' id="expiredtxt">{GW::ln('/M/orders/EXPIRED')}</span>
	{foreach $citems as $citem}
			{$obj=$citem->obj}
	   
	      
	      <!-- Product -->
      <div class="u-basket__product g-brd-none g-px-20">
	<div class="row no-gutters g-pb-5">
	  <div class="col-4 pr-3">
	    <a class="u-basket__product-img" href="{$citem->link}">
		    {*<img src="https://serv1.menuturas.lt/natosltclone/tools/img_resize?file=HL00295914&fetch=NATOS2&size=150x150&method=crop">*}
		    {if $obj->image}
			 {$img = $obj->image}
			 <img src="{$app_base}tools/img/{$img->key}&v={$img->v}&size=150x150&method=crop">
	         {else}
		    <center>{GW::ln("/g/CART_ITM_{$citem->obj_type}")}</center>
		 {/if}
	    </a>
	  </div>

	  <div class="col-8">
	    <h6 class="g-font-weight-400 g-font-size-default">
	      <a class="g-color-black g-color-primary--hover g-text-underline--none--hover" href="{$ln}/direct/orders/orders?id={$cart->id}">
		     {if $obj->context_short}<i>{$obj->context_short}</i> - {/if} {$obj->title} 
	      </a>
	    </h6>
	      <small class="g-color-primary g-font-size-12">
		      {if $obj->oldprice}<s class="g-color-gray-dark-v4">{$citem->qty}x{$obj->oldprice} &euro;</s><br/>{/if}
		      <span class="{if $obj->oldprice}g-color-red{/if}">{$citem->qty}x{$citem->unit_price} &euro;</spab>
	      </small>
	      
		{if $citem->expirable}
			{if $citem->expires_secs > 0}
				<span class="countdown" data-expires="{$citem->expires_secs}">{GW_Math_Helper::uptime($citem->expires_secs)}</span>
			{else}
				{GW::ln('/M/orders/EXPIRED')}
			{/if}  
		{/if}	      
	  </div>
	</div>
	 
	<a href="{$ln}/direct/orders/orders?id={$cart->id}&ciid={$citem->id}&act=doCartItemRemove" class="u-basket__product-remove" >&times;</a>
	
	{*<a href="{$ln}/direct/orders/orders?id={$citem->id}&act=doCartItemRemove" class="u-basket__product-remove" >&times;</a>*}
	{*<button type="button" class="u-basket__product-remove" onclick="window.location='{$ln}/direct/orders/orders/?id={$citem->id}&act=doCartItemRemove'; return false">&times;</button>*}
	
      </div>
      <!-- End Product -->
      <br />
     
      {/foreach}

     
    </div>

    <div class="clearfix g-px-15">
      <div class="row align-items-center text-center g-brd-y g-brd-gray-light-v4 g-font-size-default">
	<div class="col g-brd-right g-brd-gray-light-v4">
	  <strong class="d-block g-py-10 text-uppercase g-color-main g-font-weight-500 g-py-10">{GW::ln('/g/CART_TOTAL')}</strong>
	</div>
	<div class="col">
	  <strong class="d-block g-py-10 g-color-main g-font-weight-500 g-py-10">{$cart->amount_total} &euro;</strong>
	</div>
      </div>
    </div>

    <div class="g-pa-20">
      <div class="text-center g-mb-15">
	<a class="text-uppercase g-color-primary g-color-main--hover g-font-weight-400 g-font-size-13 g-text-underline--none--hover" href="{$ln}/direct/orders/orders/cart">
	  {GW::ln('/g/VIEW_CART')}
	  <i class="ml-2 icon-finance-100 u-line-icon-pro"></i>
	</a>
      </div>
	{if $cart->deliverable}
		{$link="{$ln}/direct/orders/orders/cart"}
	{else}
		{$link="{$ln}/direct/orders/orders?id={$cart->id}&act=doOrderPay"}
	{/if}  
      <a class="btn btn-block u-btn-black g-brd-primary--hover g-bg-primary--hover g-font-size-12 text-uppercase rounded g-py-10" href="{$link}">{GW::ln('/g/PROCEED_CHECKOUT')}</a>
    </div>
  </div>
</div>
<!-- End Basket -->
    {/if}
{/if}
