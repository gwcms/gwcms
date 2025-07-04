{$img = $item->image}
{$og_image="{$app_base}tools/img/{$img->key}&v={$img->v}"}
{include "default_open.tpl"}




{function dl_specif_row}
	{if $item->$field}
		<li class="g-brd-bottom--dashed g-brd-gray-light-v3 pt-1 mb-3">
			
			{if $field!="keyval/description"}
				<span>{if $title}{$title}{else}{GW::ln("/M/SHOP/FIELDS/`$field`")}{/if}:</span>
			{/if}
			
			<span class="g-color-black {if $type==textarea}{else}float-right{/if}">
				{if $type==textarea}<br><br>{/if}
				{if $type=="opts"}
					{call "dl_options" field=$field}
				{elseif $fieldcfg}
					{$value=$m->getVal($field, $item->$field, $fieldcfg)}
				{else}
					{$value=$item->$field} 
				{/if}
				
				{if $nl2br && strpos($value,'<p>')===false && strpos($value,'<br>')===false && strpos($value,'<br/>')===false && strpos($value,'<br />')===false}
					{$value=$value|nl2br}
				{/if}
				
				{if $type==textarea}
					{$value}
				{else}
					{$value}
				{/if}
			</span>
		</li>
	{/if}
{/function}

{function action_buttons}
	<!-- Buttons -->
	<form action="{$smarty.server.REQUEST_URI}" method="post">
		<input name="act" value="doAdd2Cart" type="hidden">
		<input name="item[id]" value="{$item->id}" type="hidden">
		
		
	<div class="row g-mx-minus-5 g-mb-20 g-mt-10">
		
		{if ($modifications && !$smarty.get.modid)  || ($modifications_subs && !$smarty.get.smodid)}
				<div class="col g-px-5 g-mb-10">

					<button class="btn btn-block u-btn-darkgray g-font-size-12 text-uppercase g-py-15 g-px-25 " disabled="disabled" style="cursor: not-allowed">
						{if $item->free}
							{GW::ln('/m/TAKE_FOR_FREE')}
						{else}
							{GW::ln('/M/SHOP/ADD_TO')} {GW::ln('/M/SHOP/CART',[l=>gal,c=>1])} <i class="align-middle ml-2 icon-finance-100 u-line-icon-pro"></i>
						{/if}
					</button>
				</div>	
					<br/>
				<span class="text-error"><i class="fa fa-info-circle"></i> {GW::ln('/m/SELECT_MOD')}</span>
					
			
		{else}

			{if $item->qty > 1 && $m->feat('selectqty')}
			<div class="col g-px-5 g-mb-10" style="text-align:right">
				{GW::ln('/m/QUANTITY')}  <i class="btn fa fa-plus-circle" style="width:20px;padding:0" 
				   onclick="if(($('#qty').val()-0) <  ($('#qty').attr('max')-0))$('#qty').val($('#qty').val()-0+1)"></i> 

				<br><input name="item[qty]" id="qty" type="number" style="width:70px;" max="{$item->qty}" min="1" value="1">
			</div>
			{/if}

			{if $item->qty ==0}
				<div class="col g-px-5 g-mb-10 text-error" style="border:1px solid red;border-radius:5px" >
					{GW::ln('/m/SOLDOUT')}
				</div>
			{else}

				<div class="col g-px-5 g-mb-10">
					
					<button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-15 g-px-25">
						{if $item->free}
							{GW::ln('/m/TAKE_FOR_FREE')}
						{else}
							{GW::ln('/M/SHOP/ADD_TO')} {GW::ln('/M/SHOP/CART',[l=>gal,c=>1])} <i class="align-middle ml-2 icon-finance-100 u-line-icon-pro"></i>
						{/if}
					</button>
				</div>
			{/if}
				{if $m->feat('wishlist')}
			<div class="col g-px-5 g-mb-10">
				<button class="btn btn-block u-btn-outline-black g-brd-gray-dark-v5 g-brd-black--hover g-color-gray-dark-v4 g-color-white--hover g-font-size-12 text-uppercase g-py-15 g-px-25 gwUrlMod" type="button" 
					data-args='{json_encode([act=>doAdd2WishList,id=>$item->id])}' data-ajax="1" data-refresh="1" data-loading="1" data-auth="1">
					{GW::ln('/M/SHOP/ADD_TO')} {GW::ln('/M/SHOP/WISHLIST',[l=>gal,c=>1])} <i class="align-middle ml-2 icon-medical-022 u-line-icon-pro"></i>
				</button>
			</div>
				{/if}
			
		{/if}
	</div>
	</form>
	<!-- End Buttons -->
{/function}

{include "product_display.tpl"}

{if $app->user && $app->user->isRoot()}
	{d::ldump($item->content_base, ['hidden'=>1])}
	
{/if}
{if $app->user && ($app->user->isRoot() || $app->user->username==admin)}
<span style='color:orange'>ADM {include "gw/ajaxdropdown.tpl" modpath="shop/products"}</span>
{/if}

<!-- Product Description -->
<div class="container g-pt-20 g-pb-100">
        <div class="row">
		<div class="col-md-4">
			
			{call "product_image" product=$item imclass="mb-1"}
			
			<!-- end of product image-->

		

			
		</div>





<div class="col-lg-6">
<div class="g-px-40--lg">
	<!-- Product Info -->
	<div class="g-mb-30">
		<h1 class="g-font-weight-300 mb-4">{$item->title} 			
			{if $item->subtitle}<br>{$item->subtite}{/if}</h1>
			{*<p>Description</p>*}
	</div>
	<!-- End Product Info -->

	

	<div class="g-mb-30">
		<!-- List -->
		<ul class="list-unstyled g-color-text">
			
			{if $modifications}
				
				{$opts = [''=>GW::ln('/m/SELECT_MOD')]}
				{$disabled=[]}
				{foreach $modifications as $mod}
					{$opts[$mod->id]="{$mod->modif_title} {if !$m->feat(infinite_qty)}| {GW::ln('/m/QTY_REMAIN')}: {$mod->qty}{/if}"}
					{if !$mod->qty}
						{$disabled[$mod->id]=$mod->id}
					{/if}
				{/foreach}
				
				
					{$moddisplay=$item->moddisplay}
					{if !$moddisplay}
						{$moddisplay=$m->config->modification_display}
					{/if}
					
					{if $moddisplay==list}
						{foreach $modifications as $mod}
							<li>
								<a 
									 {$addclass=[]}
									 
							           {if $smarty.get.modid==$mod->id}
									   {$addclass[]="u-btn-primary"}{*"modification_selected"*}
								   {/if}
									 
								   {if $mod->qty > 0 || $m->canSeeOrders()}
									    href="#" 
									onclick="gw_navigator.jump(false,{ modid:{$mod->id}, smodid:null });return false"
								   {else}
									   {$addclass[]="text-muted"}
								   {/if}
								   class="modification_select {implode(' ',$addclass)}"
								   >
									{$mod->modif_title} {if !$m->feat(infinite_qty)} | {GW::ln('/m/QTY_REMAIN')}: {$mod->qty}{/if}
								</a>
							</li>
						{/foreach}
							
							
					{elseif $moddisplay==select}	

						{include "inputs/input_select.tpl" name="modif" value=$smarty.get.modid options=$opts  
						onchange="gw_navigator.jump(false,{ modid:this.value })"}
					{elseif $moddisplay==icons}	
						{foreach $modifications as $mod}
						
						
							 <a 
									 {$addclass=[]}
									 
							           {if $smarty.get.modid==$mod->id}
									   {$addclass[]="u-btn-primary"}{*"modification_selected"*}
								   {/if}
									 
								   {if $mod->qty > 0 || $m->canSeeOrders()}
									    href="#" 
									onclick="gw_navigator.jump(false,{ modid:{$mod->id}, smodid:null });return false"
								   {else}
									   {$addclass[]="text-muted"}
								   {/if}
								   class="modification_select {implode(' ',$addclass)}"
								   >
									{$img = $mod->image}
									{if $img}
										<img src="{$app_base}tools/img/{$img->key}&v={$img->v}" style="width:50px">
										{$mod->modif_title}
									{else}
										{$mod->modif_title} 
									{/if}
								
								</a>
						{/foreach}
						
						
					{/if}
					
					
					
				
				
				{if $modifications_subs}
					<br>
					{foreach $modifications_subs as $smod}
						<a  
									 {$addclass=[]}
									 
							           {if $smarty.get.smodid==$smod->id}
									   {$addclass[]="u-btn-primary"}{*"modification_selected"*}
								   {/if}
									 
								   {if $smod->qty > 0 || $m->canSeeOrders()}
									    href="#" 
									onclick="gw_navigator.jump(false,{ smodid:{$smod->id} });return false"
								   {else}
									   {$addclass[]="text-muted"}
								   {/if}
								   class="smodification_select {implode(' ',$addclass)}"
								   >
									{$smod->modif_title} {if !$m->feat(infinite_qty)} | {GW::ln('/m/QTY_REMAIN')}: {$smod->qty}{/if}
								</a>
					{/foreach}
				{/if}				
				
			{/if}
			
			{if $m->feat('add2cart_top')}
				{call action_buttons}
			{/if}
			

			{foreach $m->mod_fields as $field}
				
				{call "dl_specif_row" field=$field->fieldname type=$field->inp_type title=$field->title fieldcfg=$field}
			{/foreach}

	
			{call "dl_specif_row" field="keyval/description" type=textarea nl2br=1}
			
		</ul>
			
		<!-- End List -->
	</div>	   
			
	<!-- Price -->
	<div class="g-mb-30">
		<h2 class="g-color-gray-dark-v5 g-font-weight-400 g-font-size-12 text-uppercase mb-2">{GW::ln('/M/SHOP/PRICE')}</h2>

		
		{if ($item->mod_count && !$active_mod) || ($modifications_subs && !$smarty.get.smodid)}

				<span class="g-color-black">
				{if $item->free}
					{GW::ln('/m/FREE')}
				{elseif $modifications_pricerange.0 != $modifications_pricerange.1} 
					{if GW::s('PROJECT_NAME') == 'drpaulclayton'}
						{GW::ln('/m/TO_SEE_PRICE_PICK_MOD')}
					{else}
						{$modifications_pricerange.0} &#8212; {$modifications_pricerange.1} &euro;
					{/if}
				{else}
					{$modifications_pricerange.0}  &euro;
				{/if}				
				</span>
		{else}
			{if $item->free}
				<span class="g-color-red g-font-weight-500 g-font-size-30 mr-2">{GW::ln('/m/FREE')}</span>
			{else}
				{if $item->oldprice > 0}
					<s class="g-color-gray-dark-v4 g-font-weight-500 g-font-size-16">{$item->oldprice} &euro;</s>
				{/if}
				<span class="{if $item->oldprice > 0}g-color-red{else}g-color-black{/if} g-font-weight-500 g-font-size-30 mr-2">{$item->price} &euro;</span>
			{/if}




			{if $item->price_scheme}
				{$scheme=$item->getPriceScheme()}
				<div style="background-color:#eee">
					<ul>
					{foreach $scheme as $qty => $price}
						{$savings=($item->price-$price)*$qty}
						{*Buy $qty times, unit price: $price€ <small>(savings from: $savings€)</small>*}
					<li>{GW::ln('/m/PRICESCHEME_BUY', [v=>[qty=>$qty, price=>$price, savings=>$savings]])}</li>
					{/foreach}
					</ul>
				</div>
			{/if}	
		{/if}
	</div>
	<!-- End Price -->			
	<div style="clear:both"></div>

	{call action_buttons}
{*	
	<ul class="nav d-flex justify-content-between g-font-size-12 text-uppercase" role="tablist" data-target="nav-1-1-default-hor-left">
                <li class="nav-item g-brd-bottom g-brd-gray-dark-v4">
                  <a class="nav-link g-color-primary--parent-active g-pa-0 g-pb-1 active" data-toggle="tab" href="#nav-1-1-default-hor-left--3" role="tab" aria-selected="true">Returns</a>
                </li>
                <li class="nav-item g-brd-bottom g-brd-gray-dark-v4">
                  <a class="nav-link g-color-primary--parent-active g-pa-0 g-pb-1" data-toggle="tab" href="#nav-1-1-default-hor-left--1" role="tab" aria-selected="false">View Size Guide</a>
                </li>
                <li class="nav-item g-brd-bottom g-brd-gray-dark-v4">
                  <a class="nav-link g-color-primary--parent-active g-pa-0 g-pb-1" data-toggle="tab" href="#nav-1-1-default-hor-left--2" role="tab" aria-selected="false">Delivery</a>
                </li>
              </ul>

<div id="nav-1-1-default-hor-left" class="tab-content">
                <div class="tab-pane fade g-pt-30 active show" id="nav-1-1-default-hor-left--3" role="tabpanel">
                  <p class="g-color-gray-dark-v4 g-font-size-13 mb-0">You can return/exchange your orders in Unify E-commerce. For more information, read our
                    <a href="#">FAQ</a>.</p>
                </div>

                <div class="tab-pane fade g-pt-30" id="nav-1-1-default-hor-left--1" role="tabpanel">
                  <h4 class="g-font-size-15 mb-3">General Clothing Size Guide</h4>

                  <!-- Size -->
                  <table>
                    <tbody>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-width-150 g-py-5">Unify Size (UK)</td>
                        <td class="align-top g-width-50 g-py-5">S</td>
                        <td class="align-top g-width-50 g-py-5">M</td>
                        <td class="align-top g-width-50 g-py-5">L</td>
                        <td class="align-top g-width-50 g-py-5">XL</td>
                        <td class="align-top g-width-50 g-py-5">XXL</td>
                      </tr>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-width-150 g-py-5">UK</td>
                        <td class="align-top g-width-50 g-py-5">6</td>
                        <td class="align-top g-width-50 g-py-5">8</td>
                        <td class="align-top g-width-50 g-py-5">10</td>
                        <td class="align-top g-width-50 g-py-5">12</td>
                        <td class="align-top g-width-50 g-py-5">14</td>
                      </tr>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-width-150 g-py-5">Europe</td>
                        <td class="align-top g-width-50 g-py-5">32</td>
                        <td class="align-top g-width-50 g-py-5">34</td>
                        <td class="align-top g-width-50 g-py-5">36</td>
                        <td class="align-top g-width-50 g-py-5">38</td>
                        <td class="align-top g-width-50 g-py-5">40</td>
                      </tr>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-width-150 g-py-5">US</td>
                        <td class="align-top g-width-50 g-py-5">2</td>
                        <td class="align-top g-width-50 g-py-5">4</td>
                        <td class="align-top g-width-50 g-py-5">6</td>
                        <td class="align-top g-width-50 g-py-5">8</td>
                        <td class="align-top g-width-50 g-py-5">10</td>
                      </tr>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-width-150 g-py-5">Australia</td>
                        <td class="align-top g-width-50 g-py-5">6</td>
                        <td class="align-top g-width-50 g-py-5">8</td>
                        <td class="align-top g-width-50 g-py-5">10</td>
                        <td class="align-top g-width-50 g-py-5">12</td>
                        <td class="align-top g-width-50 g-py-5">14</td>
                      </tr>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-width-150 g-py-5">Japan</td>
                        <td class="align-top g-width-50 g-py-5">7</td>
                        <td class="align-top g-width-50 g-py-5">9</td>
                        <td class="align-top g-width-50 g-py-5">11</td>
                        <td class="align-top g-width-50 g-py-5">13</td>
                        <td class="align-top g-width-50 g-py-5">15</td>
                      </tr>
                    </tbody>
                  </table>
                  <!-- End Size -->
                </div>

                <div class="tab-pane fade g-pt-30" id="nav-1-1-default-hor-left--2" role="tabpanel">
                  <!-- Shipping Mehtod -->
                  <table>
                    <thead class="h6 g-brd-bottom g-brd-gray-light-v3 g-color-gray-dark-v3 g-font-size-13">
                      <tr>
                        <th class="g-width-100 g-font-weight-500 g-pa-0 g-pb-10">Destination</th>
                        <th class="g-width-140 g-font-weight-500 g-pa-0 g-pb-10">Delivery type</th>
                        <th class="g-width-150 g-font-weight-500 g-pa-0 g-pb-10">Delivery time</th>
                        <th class="g-font-weight-500 text-right g-pa-0 g-pb-10">Cost</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-py-10">UK</td>
                        <td class="align-top g-py-10">Standard delivery</td>
                        <td class="align-top g-font-size-11 g-py-10">2-3 Working days</td>
                        <td class="align-top text-right g-py-10">$5.5</td>
                      </tr>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-py-10"></td>
                        <td class="align-top g-py-10">Next day</td>
                        <td class="align-top g-font-size-11 g-py-10">Order before 12pm monday - thursday and receive it the next day</td>
                        <td class="align-top text-right g-py-10">$9.5</td>
                      </tr>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-py-10"></td>
                        <td class="align-top g-py-10">Saturday delivery</td>
                        <td class="align-top g-font-size-11 g-py-10">Saturday delivery for orders placed before 12pm on friday</td>
                        <td class="align-top text-right g-py-10">$12.00</td>
                      </tr>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-py-10">Europe</td>
                        <td class="align-top g-py-10">Standard delivery</td>
                        <td class="align-top g-font-size-11 g-py-10">3-9 Working days</td>
                        <td class="align-top text-right g-py-10">$20.00</td>
                      </tr>
                      <tr class="g-color-gray-dark-v4 g-font-size-12">
                        <td class="align-top g-py-10">America</td>
                        <td class="align-top g-py-10">Standard delivery</td>
                        <td class="align-top g-font-size-11 g-py-10">3-9 Working days</td>
                        <td class="align-top text-right g-py-10">$25.00</td>
                      </tr>
                    </tbody>
                  </table>
                  <!-- End Shipping Mehtod -->
                </div>
              </div>	
	
*}

</div>
		</div>
        </div>
        <!-- End Products -->
</div>
<!-- End Products -->


{if $m->canSeeOrders()}
	
	{$list = $m->getOrders($item)}
	{if $list}
		<h2>
			Neapdoroti užsakymai <small class="text-muted">(matote nes esate priskirti į užsakymų grupę)</small>
		</h2>
	<table class="ordertbl">
		<tr>
			<th>Vardas pavarde</th>
			<th>Kiekis</th>
			<th>Kaina</th>
			<th>Mokėjimo statusas</th>
			<th>Apmokėjimo laikas</th>
		</tr>
	{foreach $list as $item}
		<tr>
			<td>{$item->order->user->title}</td>
			<td>{$item->qty}</td>
			<td>{$item->unit_price} &euro;</td>
			<td>{if $item->payment_status==7}TAIP{else}Ne{/if} {if $item->pay_test}TEST{/if}</td>
			<td>{$item->pay_time}</td>
		</tr>
	{/foreach}
	</table>
	<br><br>
	{/if}
	
	<style>
		.ordertbl th, .ordertbl td{ padding: 2px }
		.ordertbl th{ color: silver }
	</style>
{/if}




<p class="text-muted">{GW::ln('/m/BOTTOM_INFO_TEXT')}</p>


{*$app->processPath('shop/products/subscribeblock',['current'=>$item->id])*}

{if $m->feat('prod_visit_history')}
	{$m->processView('inproductHistory')}
{/if}

{capture append=footer_hidden}
	
       <script src="{$assets}../assets/js/components/hs.popup.js"></script>

       <!-- JS Plugins Init. -->
       <script>

                      $(document).on('ready', function () {
			    
                        // initialization of popups
                       // $.HSCore.components.HSPopup.init('.js-fancybox');

                        // initialization of gallery with thumbs
                        $.HSCore.components.HSPopup.init('.js-fancybox', {
                          thumbs: {
                            showOnStart: true
                          }
                        });
                      });
       </script>
       <style>
	       .modification_select{ display:block; padding: 2px 10px; border:1px solid silver; margin-bottom:2px;}
	        .modification_selected{ background-color: darkorchid; color: white }
		.smodification_select{ display:inline-block; padding:2px 10px; border:1px solid silver; margin-bottom:2px;}
	</style>
{/capture} 

{include "default_close.tpl"}
