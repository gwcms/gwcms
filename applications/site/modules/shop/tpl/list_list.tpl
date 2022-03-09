<br />



<table cellpadding="2" class="prodTable">
	<tr>
		<th><i class="fa fa-image"></i></th>
		<th>{GW::ln('/m/FIELDS/title')}</th>
		<th>{GW::ln('/m/FIELDS/price')}</th>
	</tr>
		
	{foreach $list as $item}
		{$link=$app->buildUri("direct/shop/shop/p/{FH::urlStr($item->title)}",[id=>$item->id])}
	<tr>
		<td>
			<a href="{$link}" class="prodLink">{call name="product_image" product=$item size="20x20"}</a>
		</td>
		
	      <td><a href="{$link}" class="prodLink">{$field=$m->getFirstClass($item)}
			{$m->getClassifVal($item->get($field))} {$item->title}</a></td>
	      <td>
			{if $item->oldprice}
				<s class="g-color-gray-dark-v4 g-font-weight-500 g-font-size-15">{$item->oldprice} &euro;</s>
			{/if}
			<span class="{if $item->oldprice}g-color-red{else}g-color-black{/if}">{$item->price} &euro;</span>	      
	      </td>
	      <td class='prodAct'>
		      {if $m->feat(site_add2cart_from_list)}
		      <a class="add2cart" 
			 href="{$m->buildUri(false, [act=>doAdd2Cart, item=>[id=>$item->id, qty=>1]])}" 
			 data-incart="{$m->isItemInCart($item)}" title="{GW::ln('/M/SHOP/ADD_TO')} {GW::ln('/M/SHOP/CART',[l=>gal,c=>1])}">
		      </a>   
		      {/if}
		      {if $m->feat(wishlist_enabled)}
		<a class="gwUrlMod {if $m->isItemInWishlist($item->id)}u-icon-v3 u-icon-size--xs{else} u-icon-v1 u-icon-size--sm g-color-gray-dark-v5{/if} g-color-primary--hover g-font-size-15 rounded-circle" 
		   href="#add2wishlist" data-args='{json_encode([act=>doAdd2WishList,id=>$item->id])}' data-ajax="1" data-refresh="1" data-loading="1"
		     data-toggle="tooltip"
		     data-placement="top"
		     data-auth="1"
		     title="{GW::ln('/M/SHOP/ADD_TO')} {GW::ln('/M/SHOP/WISHLIST',[l=>gal,c=>1])}">
		    <i class="{if $m->isItemInWishlist($item->id)}fa fa-heart{else}icon-medical-022 u-line-icon-pro{/if}"></i>
		  </a>	
		  {/if}
	      </td>
	</tr>
	
	{/foreach}

</table>
	
<style>

.prodTable tr:hover { 
   background: #eee; 
}
.prodLink { 
	display:blockl
}
.prodAct{
	white-space: nowrap;
}
</style>
