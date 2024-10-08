{include "default_open.tpl"}

{include file="inputs/inputs.tpl"}	



{function "cart_item_img_or_category"}
	{if $obj->image}
		{$img = $obj->image}
		<img class="{$class}" src="{$app_base}tools/img/{$img->key}&v={$img->v}&size={$imsize}&method=crop">
			
	{elseif $obj->image_url}
	 	  <img src="{$obj->image_url}&size={$imsize}&method=crop">		
	{else}
	   <center>{GW::ln("/g/CART_ITM_{$item->obj_type}")}</center>
	{/if}
{/function}

{function "cart_item_expirable_prop"}
	{if $item->expirable}
		{if $addli}<li>{/if}
			<span title="{GW::ln('/m/FIELDS/expires')}: {$item->expires}">
			<i class="fa fa-clock-o"></i>
			{if $item->expires_secs > 3600}
				{GW_Math_Helper::uptime($item->expires_secs, 1)}
			{elseif $item->expires_secs > 0}
				<span class="countdown" data-expires="{$item->expires_secs}">{GW_Math_Helper::uptime($item->expires_secs)}</span>
			{else}
				{GW::ln('/M/orders/EXPIRED')}
			{/if}	
			</span>
		{if $addli}</li>{/if}
	{/if}
{/function}
	
	


{capture append=footer_hidden}
	<link rel="stylesheet" href="{$assets}../assets/vendor/chosen/chosen.css">
   <!-- JS Global Compulsory -->
    <script src="{$assets}../assets/vendor/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="{$assets}../assets/vendor/chosen/chosen.jquery.js"></script>
    <script src="{$assets}../assets/vendor/image-select/src/ImageSelect.jquery.js"></script>

    <script src="{$assets}../assets/js/components/hs.select.js"></script>
    <script src="{$assets}../assets/js/components/hs.count-qty.js"></script>
    <script src="{$assets}../assets/js/components/hs.step-form.js"></script>
    <script src="{$assets}../assets/js/components/hs.validation.js"></script>	
{/capture}
{capture append=doc_ready_js}
        // initialization of form validation
        $.HSCore.components.HSValidation.init('.js-validate');

        // initialization of custom select
        $.HSCore.components.HSSelect.init('.js-custom-select');

        // initialization of quantity counter
        $.HSCore.components.HSCountQty.init('.js-quantity');
{/capture}

{*
	{if $app->user && $app->user->isRoot()}<i class="fa fa-question-circle-o" onclick="alert('kiti variantai tabu: http://totoriai.gw.lt/html/unify/html/unify-main/shortcodes/tabs/shortcode-base-tabs-8-2.html')"></i>{/if}
*}

	{$step=$smarty.get.step|default:1}
	{$step=intval($step)}
     <!-- Checkout Form -->
      <div class="container g-pt-100 g-pb-70">
        <form id="cartList" method="post" action="{$smarty.server.REQUEST_URI}"> 
		
          <div class="g-mb-100">
            <!-- Step Titles -->
	    
	    
{*{$nav_class1="u-nav-v8__icon u-icon-v3 u-icon-size--lg g-rounded-50x g-brd-around g-brd-4 g-brd-white"}*}
{$nav_class1="inactiveico u-nav-v8__icon u-icon-v3 u-icon-size--lg g-rounded-50x g-brd-around g-brd-4"}	 


{*{$nav_class0="nav text-center justify-content-center u-nav-v8-2"}*}
{$nav_class0="nav nav-fill u-nav-v8-2 u-nav-light"}


<ul class="{$nav_class0}" role="tablist" data-target="nav-8-2-accordion-primary-hor-center" data-tabs-mobile-type="accordion" data-btn-classes="btn btn-md btn-block u-btn-outline-primary g-mb-20">
  <li class="nav-item">
	  {if $step==1}{$active=1}{else}{$active=0}{/if}
    <a class="nav-link {if $active}active{/if} gwUrlMod"  {if $step > 1}data-args='{ "step":1 }' {/if} href="#">
      <span class="{$nav_class1}{if $active} g-brd-white{else} g-brd-silver{/if}" style="border: 1px solid #eee;">
        <i class="fa fa-search" aria-hidden="true"></i>
      </span>
      <strong class="text-uppercase u-nav-v8__title">{GW::ln('/m/SHOPPING_CART')}</strong>
      <em class="u-nav-v8__description">{GW::ln('/m/SHOPPING_CART_EXPLAIN')}</em>
    </a>
  </li>
  {if $m->order->deliverable || $m->auser}
  <li class="nav-item">
	  {if $step==2}{$active=1}{else}{$active=0}{/if}
    <a class="nav-link {if $step==2}active{/if} {if $m->order}gwUrlMod" href="#" data-args='{ "step":2 }'{else}"{/if}  >
      <span class="{$nav_class1}{if $active} g-brd-white{else} g-brd-silver{/if}">
        <i class="fa fa-truck" aria-hidden="true"></i>
      </span>
      <strong class="text-uppercase u-nav-v8__title">{GW::ln('/m/SHIPPING')}</strong>
      <em class="u-nav-v8__description">{GW::ln('/m/SHIPPING_EXPLAIN')}</em>
    </a>
  </li>
  {/if}
  <li class="nav-item">
	  {if $step==3}{$active=1}{else}{$active=0}{/if}
    <a class="nav-link {if $active}active{/if} {if $m->order && $m->order->delivery_opt}gwUrlMod" href="#" data-args='{ "step":3 }'{else}"{/if}  >
      <span class="{$nav_class1}{if $active} g-brd-white{else} g-brd-silver{/if}">
        <i class="fa fa-credit-card" aria-hidden="true"></i>
      </span>
      <strong class="text-uppercase u-nav-v8__title">{GW::ln('/m/PAYMENT_REVIEW')}</strong>
      <em class="u-nav-v8__description">{GW::ln('/m/PAYMENT_EXPLAIN')}</em>
    </a>
  </li>
</ul>	    
	    
	    
   {*
<style>
	.u-nav-v8-2{ text-shadow: 0px 0px 4px #fff; }
	.active{ text-shadow: 0px 0px 0px #fff; }
</style>
    *}
            
            <!-- End Step Titles -->
          </div>

          <div id="stepFormSteps">
		  
		  
	
		{include "{$m->tpl_dir}cart{$step|default:1}.tpl"}
          </div>
		
		<input type="hidden" id="step" name="step" value="{$step}">
        </form>
      </div>
      <!-- End Checkout Form -->
      
<script>
		function cartChanged(itemid)
		{
			//$('#item_qty_control_'+itemid).;
			
			$('#proceedCheckout').hide();
			$('#updateCart').fadeIn();
			$('.cart_total').hide();
		}
		
		$('.jss-plus').click(function(){
			var inp = $(this).parents('.js-quantity').find('.qty_input');
			
			inp.val( (inp.val()-0)+1 );
			inp.change();
			
		})
		$('.jss-minus').click(function(){
			var inp = $(this).parents('.js-quantity').find('.qty_input');
						
			inp.val( (inp.val()-0)-1 );
			inp.change();
			
		})
		
		$('.qty_input').change(function(){
			
			var max = $(this).data('max')-0;
			var min = $(this).data('min')-0;
			var val = this.value-0;
			
			
			if(val > max){
				this.value = max;
			}
			
			if(val < min){
				this.value = min;
			}
			
			if(this.value==min){
				$(this).parent().find('.jss-minus').addClass('text-muted');
			}else{
				$(this).parent().find('.jss-minus').removeClass('text-muted');
			}
			
			if(this.value==max){
				$(this).parent().find('.jss-plus').addClass('text-muted');
			}else{
				$(this).parent().find('.jss-plus').removeClass('text-muted');
			}
			
		})
		
		$(function(){
			$('#updateCart').click(function(){
				$('#step').val(1);
				$('#cartList').submit()
			})
			$('#proceedCheckout').click(function(){
				$('#step').val($(this).data('step'));
				$('#cartList').submit()
			})		
		})
</script>

{include "default_close.tpl"}