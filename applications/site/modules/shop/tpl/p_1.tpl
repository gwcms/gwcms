{include "default_open.tpl"}




{function dl_specif_row}
	{if $item->$field}
		<li class="g-brd-bottom--dashed g-brd-gray-light-v3 pt-1 mb-3">
			<span>{GW::ln("/M/PRODUCTS/FIELDS/`$field`")}:</span>
			<span class="float-right g-color-black">
				{if $type=="opts"}
					{call "dl_options" field=$field}
				{else}
					{$item->$field}
				{/if}
			</span>
		</li>
	{/if}
{/function}

{include "product_display.tpl"}

{if $app->user && $app->user->isRoot()}
	{d::ldump($item->content_base, ['hidden'=>1])}
{/if}

<!-- Product Description -->
<div class="container g-pt-50 g-pb-100">
        <div class="row">
		<div class="col-md-4">
			
			{call "product_image" product=$item imclass="mb-1"}
			
			<!-- end of product image-->

		

			
		</div>





<div class="col-lg-6">
<div class="g-px-40--lg">
	<!-- Product Info -->
	<div class="g-mb-30">
		<h1 class="g-font-weight-300 mb-4">{$item->title}{if $item->subtitle}<br>{$item->subtite}{/if}</h1>
			{*<p>Description</p>*}
	</div>
	<!-- End Product Info -->


	

	<div class="g-mb-30">
		<!-- List -->
		<ul class="list-unstyled g-color-text">
			
			{if $modifications}
				{$opts = [''=>$oitem->modif_title]}
				{foreach $modifications as $mod}
					{$opts[$mod->id]=$mod->modif_title}
				{/foreach}
				<li> {include "inputs/input_select.tpl" name="modif" value=$smarty.get.modid options=$opts onchange="gw_navigator.jump(false,{ modid:this.value })"}</li>
			{/if}
			

			{call "dl_specif_row" field=ean type=text}
			{call "dl_specif_row" field=isbn type=text}
			{call "dl_specif_row" field=ismn type=text}
			{call "dl_specif_row" field="keyval/description" type=text}

		</ul>
			
		<!-- End List -->
	</div>	   
			
	<!-- Price -->
	<div class="g-mb-30">
		<h2 class="g-color-gray-dark-v5 g-font-weight-400 g-font-size-12 text-uppercase mb-2">{GW::ln('/M/PRODUCTS/PRICE')}</h2>

		<span class="g-color-black g-font-weight-500 g-font-size-30 mr-2">{$item->price} &euro;</span>
		{if $item->discount}
			<s class="g-color-gray-dark-v4 g-font-weight-500 g-font-size-16">{$item->prediscount_price} &euro;</s>
			{/if}
		
		
		
		
		{if $item->price_scheme}
			{$scheme=$item->getPriceScheme()}
			<div style="background-color:#eee">
				<ul>
				{foreach $scheme as $qty => $price}
	
				<li>Buy {$qty} times, unit price: {$price}&euro; <small>(savings from: {($item->price-$price)*$qty}&euro;)</small></li>
				{/foreach}
				</ul>
			</div>
		{/if}		
	</div>
	<!-- End Price -->			
	<div style="clear:both"></div>

	<!-- Buttons -->
	<form action="{$smarty.server.REQUEST_URI}" method="post">
		<input name="act" value="doAdd2Cart" type="hidden">
		<input name="item[id]" value="{$item->id}" type="hidden">
		
	<div class="row g-mx-minus-5 g-mb-20 g-mt-10">
		<div class="col g-px-5 g-mb-10" style="text-align:right">
			Quantity  <i class="btn fa fa-plus-circle" style="width:20px;padding:0" onclick="$('#qty').val($('#qty').val()-0+100)"></i> 
			<br><input name="item[qty]" id="qty" type="number" style="width:70px;">
		</div>
		<div class="col g-px-5 g-mb-10">

			<button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-15 g-px-25">
				{GW::ln('/M/PRODUCTS/ADD_TO')} {GW::ln('/M/PRODUCTS/CART',[l=>gal,c=>1])} <i class="align-middle ml-2 icon-finance-100 u-line-icon-pro"></i>
			</button>
		</div>
			{if isset($products.wishlist)}
		<div class="col g-px-5 g-mb-10">
			<button class="btn btn-block u-btn-outline-black g-brd-gray-dark-v5 g-brd-black--hover g-color-gray-dark-v4 g-color-white--hover g-font-size-12 text-uppercase g-py-15 g-px-25 gwUrlMod" type="button" 
				data-args='{json_encode([act=>doAdd2WishList,id=>$item->id])}' data-ajax="1" data-refresh="1" data-loading="1" data-auth="1">
				{GW::ln('/M/PRODUCTS/ADD_TO')} {GW::ln('/M/PRODUCTS/WISHLIST',[l=>gal,c=>1])} <i class="align-middle ml-2 icon-medical-022 u-line-icon-pro"></i>
			</button>
		</div>
			{/if}
	</div>
	<!-- End Buttons -->
	
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
	


</div>
		</div>
        </div>
        <!-- End Products -->
</div>
<!-- End Products -->

{*$app->processPath('products/products/subscribeblock',['current'=>$item->id])*}

{$m->processView('inproductHistory')}

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
{/capture} 

{include "default_close.tpl"}
