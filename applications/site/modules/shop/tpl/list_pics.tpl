{$cols=$cols|default:3}
{$celsz=12/$cols}
{*
{$imsize=[4=>"480x700",6=>"200x200"]}
{$imsize=$imsize[$cols]}
*}
{$imsize="480x480"}

<div class="row g-pt-30 g-mb-50">	
	{foreach $list as $item}
		
		{if $item->exitlink}
			{$link=$app->buildUri($item->exitlink,[from_product_id=>$item->id])}
		{else}
			{$link=$app->buildUri("direct/shop/shop/p/{FH::urlStr($item->title)}",[id=>$item->id])}
		{/if}		
		
	  <div class="col-6 col-lg-{$celsz} g-mb-30">
		  <a class="u-link-v5 g-color-black g-color-primary--hover" href="{$link}">
	    <!-- Product -->
	    <figure class="g-pos-rel g-mb-20">
		    {call name="product_image" product=$item size=$imsize crop=1}
	      
	      {if $item->price==0 && $item->mod_count==0}
	<figcaption class="w-100 g-bg-lightred text-center g-pos-abs g-bottom-0 g-transition-0_2 g-py-5">
		<span class="g-color-white g-font-size-11 text-uppercase g-letter-spacing-1">{GW::ln('/M/SHOP/SOLD_OUT')}</a>
	      </figcaption>
	      {elseif $item->oldprice > 0}

		       <span class="u-ribbon-v1 g-width-40 g-height-40 g-color-white g-bg-primary g-font-size-13 text-center text-uppercase g-rounded-50x g-top-10 g-right-minus-10 g-px-2 g-py-10">-{$item->discount_display}</span>
	      {/if}
	    
	      {if $item->komisas}
		      <span class="u-ribbon-v1 g-width-40 g-height-40 g-color-white g-bg-img-hero g-font-size-8 text-center text-uppercase g-rounded-50x g-px-2 g-py-10" 
			    style="right:20px;{if $item->oldprice>0}top:60px;{else}top: 20px;{/if}">Naudota</span>
		      
		      {*
		      	<figcaption class="w-100 g-bg-primary text-center g-pos-abs g-bottom-0 g-transition-0_2 g-py-5" style="opacity:0.6">
				<span class="g-color-white g-font-size-11 text-uppercase g-letter-spacing-1">{GW::ln('/m/USED')}</a>
			 </figcaption>*}
	      {/if}
	    </figure>

	    <div class="media">
	      <!-- Product Info -->
	      <div class="d-flex flex-column">
		      
		 <span class="d-inline-block g-color-primary g-font-size-15">
			{$field=$m->getFirstClass($item)}
			{$m->getClassifVal($item->get($field))}
		</span>
		
		<h4 class="h6 g-color-black mb-1">
		  
		   {$item->title}
		  
		</h4>
		
		
		
		<span class="d-block g-color-black g-font-size-17">
			{if $item->mod_count}
				<span class="g-color-black">
				{if $item->min_price != $item->max_price} 
					{$item->min_price} &#8212; {$item->max_price} &euro;
				{else}
					{$item->min_price}  &euro;
				{/if}				
				</span>
			{else}
			{if $item->oldprice > 0}
				<s class="g-color-gray-dark-v4 g-font-weight-500 g-font-size-15">{$item->oldprice} &euro;</s>
			{/if}
			<span class="{if $item->oldprice > 0}g-color-red{else}g-color-black{/if}">{$item->price} &euro;</span>
			{/if}
		</span>
	      </div>
	      <!-- End Product Info -->

	      <!-- Products Icons -->
	      <ul class="list-inline media-body text-right">
		 
		{if $m->config->site_add2cart_from_list}
		<li class="list-inline-item align-middle mx-0">
		  <a class="add2cart" 
		     href="{$app->buildUri(false, [act=>doAdd2Cart, item=>[id=>$item->id, qty=>1]])}" 
		     data-id="{$item->id}" data-incart="{$m->isItemInCart($item)}" title="{GW::ln('/M/SHOP/ADD_TO')} {GW::ln('/M/SHOP/CART',[l=>gal,c=>1])}"></a>
		</li>
		{/if}
		
		{if $m->feat('wishlist')}
		<li class="list-inline-item align-middle mx-0">
		  <a class="{if $m->isItemInWishlist($item->id)}u-icon-v3 u-icon-size--xs{else} u-icon-v1 u-icon-size--sm g-color-gray-dark-v5{/if} gwUrlMod g-color-primary--hover g-font-size-15 rounded-circle" href="#add2wishlist" data-args='{json_encode([act=>doAdd2WishList,id=>$item->id])}' 
		     data-ajax="1" 
		     data-refresh="1" 
		     data-loading="1"
		     data-auth="1"
		     data-toggle="tooltip"
		     data-placement="top"
		     
		     title="{GW::ln('/M/SHOP/ADD_TO')} {GW::ln('/M/SHOP/WISHLIST',[l=>gal,c=>1])}">
		    <i class="{if $m->isItemInWishlist($item->id)}fa fa-heart{else}icon-medical-022 u-line-icon-pro{/if}"></i>
		  </a>
		</li>
		{/if}
	      </ul>
	      <!-- End Products Icons -->
	    </div>
		  </a>
	    <!-- End Product -->
	  </div>
	{/foreach}

</div>

	
