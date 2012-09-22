{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}

<div class="warp">
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
	<div class="overskrift">Kassen</div>
	{include file="messages.tpl"}
	
		<div class="content_kassen">
		<form method="post" action="{GW::$request->ln}/kassen?act=do:save" name="submit_order_form">
		
		<br />
        <fieldset>
        <legend>Leveringsadresse</legend>
        	<p><label for="name">Fornavn:</label><input class="kassen_tekstfelt" name="item[delivery_first_name]" type="text" value="{GW::$user->first_name}"/></p>
			<p><label for="name">Etternavn:</label><input class="kassen_tekstfelt" name="item[delivery_second_name]" type="text" value="{GW::$user->second_name}"/></p>
			<p><label for="name">Adresse:</label><input class="kassen_tekstfelt" name="item[delivery_address]" type="text" value="{GW::$user->address}"/></p>
			<p><label for="name">Postnr:</label><input class="kassen_tekstfelt" name="item[delivery_post_index]" type="text" value="{GW::$user->post_index}"/></p>
			<p><label for="name">Poststed:</label><input class="kassen_tekstfelt" name="item[delivery_city]" type="text" value="{GW::$user->city}"/></p>
			<p><label for="name">Land:</label><input class="kassen_tekstfelt" name="land" type="text" readonly="readonly" value="Norge" /></p>
		</fieldset>
        <p>
        <fieldset>
        <legend>Betalingsmåte</legend>
         	<h4><input type="radio" checked="yes" name="pay_method" value="paypal"  />Kort/Paypal <font color="red">(Deaktivert i testperioden)</font></h4>
			<h4><input type="radio" name="pay_method" value="faktura" />Faktura (Faktura blir sendt til {GW::$user->email} og varene blir sendt ut etter at denne er betalt) <font color="red">(Deaktivert i testperioden)</font></h4>
			<h4><input type="radio" name="pay_method" value="free" />Gratis (Kun for testing, malene blir ikke sendt til kunden)</h4>
			<h4><input type="radio" name="pay_method" value="none" />Ingen (Kun for testing, malene blir bestilt men ikke betalt)</h4>      	
        </fieldset>
        <p>
      	<fieldset>
        <legend>Ordre</legend>
        
        <table width="900" class="content_handlekurv_tekst">
  			<tr style="font-size:1.2em; font-weight:bold;" align="right"> 
    			<td width="600" align="left">
                Produkt
                </td>
    			<td width="100" align="right">
                Antall
                </td>
    			<td width="100" align="right">
                Pris
                </td>
    			<td width="100" align="right">
                Total
                </td>
  			</tr>
  			{$sum = 0}
			{$nrProducts = 0}
			{$nrItems = 0}
			{foreach from=$cartItemList item=cartItem}
				{if $cartItem->display == '1'}
					{$nrProducts = $nrProducts + 1}
					{$quantity = $cartItem->quantity}
					{$nrItems = $nrItems + $quantity}
					{$price = ($cartItem->product->price+$cartItem->product->mod_price-$cartItem->product->red_price)*$cartItem->quantity}
					{$sum = $sum + $price}
		  			<tr>
		    			<td align="left">
		                {$cartItem->product->type_title}
		                </td>
		    			<td align="right">
		                {$cartItem->quantity}
		                </td>
		    			<td align="right">
		                {$cartItem->product->price+$cartItem->product->mod_price-$cartItem->product->red_price}
		                </td>
		   				<td align="right">
		                {$price} kr
		                </td>
		  			</tr>
		  			{/if}
		  	{/foreach}
		  	{if $sum < 100}
		  		<tr>
	    			<td align="left">
	                Minsteprisen (100kr)
	                </td>
	    			<td align="right">
	                </td>
	    			<td align="right">
	                {100 - $sum}
	                </td>
	   				<td align="right">
	                {100 - $sum} kr
	                </td>
	  			</tr>
	  			{$sum = 100}
		  	{/if}
            <tr style="font-weight:bold;  font-size:1.2em; border-top:solid #CCC 1px;">
            	<td colspan="4" align="right">
               Totalt {$sum} kr
            	</td>
            </tr>
		</table>
      </fieldset>
      </form>
	</div>
        <p>
        <div class="overskrift"><div class="awesome2" style="text-align:right;">Har du fått gavekort? Skriv inn koden her: <input class="kassen_tekstfelt" type="text"/><a>Bruk koden</a></div></div>
        </p>
		<table class="content_kassen_button_table">
    		<tr>
    			<td>
    			<div class="awesome"><a href="{GW::$request->ln}/handlekurv" style="float:left;">Tilbake til handlekurv</a></div>
    			</td>
    			<td>
    			<div class="awesome"><a href="javascript:document.submit_order_form.submit();" style="float:right;">Betal</a></div>
   				</td>
    		</tr>
    	</table>
    	
	</div>
	<div class="contentbg_bot"></div>
</div>

{include file="footer.tpl"}