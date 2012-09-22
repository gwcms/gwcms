{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}

<div class="warp">
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
	{if GW::$user}
	<div class="overskrift">Handlekurv</div>
	{include file="messages.tpl"}<br />
	<div class="content_handlekurv">
	<!-- Overskriften til handlekurv -->
        <table class="content_handlekurv_tekst" width="963" border="0" cellspacing="0" cellpadding="10">
        	<tr>
 				<td colspan="4" align="left">
 				<h1>Produkter</h1>
                </td>
  				<td>
                	<h1>{$m->lang.quantity}</h1>
                </td>
    			<td>
                	<h1>{$m->lang.price_per_unit}</h1>
                </td>
            	<td>
                	<h1>{$m->lang.total}</h1>
                </td>
            </tr>
	{if isset($cartItemList[0])}
		{$sum = 0}
		{$nrProducts = 0}
		{$nrItems = 0}
		{foreach from=$cartItemList item=cartItem}
			{if $cartItem->display == '1'}
				{$nrProducts = $nrProducts + 1}
				{$quantity = $cartItem->quantity}
				{$nrItems = $nrItems + $quantity}
				<!-- Produkt 1 -->
	 			<tr {if $nrProducts%2 == 1}bgcolor="#f7f7f7"{/if}>
	            	<td>
	                	<img src="tools/img.php?id={$cartItem->image->key}&width=80&height=80">
	                </td>
	    			<td align="left">
	                	<h1>{$cartItem->product->category_title} {$cartItem->product->type_title}</h1><p>
	                    • Format: {$cartItem->product->width}x{$cartItem->product->height}<br />
						• Papir tykkelse: {$cartItem->product->paperSize}<br />
						• Papir type: {$cartItem->product->paperType}<br />
						• Fargeprint: {if $cartItem->product->fullcolor == 1}Ja{else}Nei{/if}<br />
						• Sider: {if $cartItem->product->folded == 1}4{else}2{/if}<br />
						• Konvolutt: {if $cartItem->product->envelope == 1}Ja{else}Nei{/if}
	    			</td>
	    			<td align="left">
	                	<a href="{$request->ln}/{$request->path}/{$cartItem->id}/edit" style="color:#007ab9; text-decoration:none;">Endre tekst/bilde/antall</a>
	                </td>
	    			<td align="left">
	                	<a href="{$request->ln}/{$request->path}?act=do:removeItem&item_id={$cartItem->id}" style="color:#F00; text-decoration:none;" onclick="return confirm('{$m->lang.confirm_delete}')">{$m->lang.delete}</a>
	                </td>
	    			<td>
	                	{$quantity}
	                </td>
	    			<td>
	                	{$cartItem->product->price+$cartItem->product->mod_price-$cartItem->product->red_price},-
	                </td>
	    			<td>
	                	{$itemSum = $quantity*($cartItem->product->price+$cartItem->product->mod_price-$cartItem->product->red_price)}{$itemSum},-
	                </td>
	  			</tr>
				{$sum = $sum + $itemSum}
			{/if}
		{/foreach}  			
		{if $nrProducts == 0}
			<tr><td>{$m->lang.shopping_cart_empty}</td></tr>
			</table>
		{else} 
	  			<!-- Totalsum -->           
	  
	  			<tr bgcolor="#e9e9e9">
	    			<td colspan="6" align="right">
	                	<h1>{if $sum < 100}(minstepris 100kr) {$sum = 100}{/if} Totalt:</h1>
	                </td>
	    			<td>
	                	<h1>{$sum},-</h1>
	                </td>
	  			</tr>
	  		</table>
	  		<div class="awesome" style="padding-top:10px; text-align:right;">
	    	<a href="{$request->ln}/kassen">Gå til kassen</a></div>
	   {/if}
 
	{else}
		<tr><td>{$m->lang.shopping_cart_empty}</td></tr>
		</table>
	{/if}
	</div>
{else}
<div class="overskrift">{$m->lang.must_login_to_view_cart}</div>
{include file="messages.tpl"}<div id="shopsign">{include file="login_form.tpl"}</div>
{/if}
		
	</div>
	<div class="contentbg_bot"></div>
</div>

{include file="footer.tpl"}