{include "product_display.tpl"}
<input name="act" type="hidden" value="doSaveCart"/>

	{$input_name_pattern="order[%s]"}
	
{function name=dl_options}
	<a class="gwUrlMod d-inline-block g-color-gray-dark-v5 g-font-size-13" href="#" data-args='{json_encode([page=>0,$m->fieldInfo[$field].short=>$item->$field])}' data-path="{$page->path}">
		{$o=$m->options.$field[$item->$field]}
		
		
		{if $o}
			{if $field=="instrumn_id"}
				{$o->get("title_`$ln`")}
			{else}
				{$o->title}
			{/if}
		{else}
			{$field}:{$item->$field}
		{/if}
	</a>
{/function}
{function dl_specif_row}
	{if $item->$field}
		<li>{GW::ln("/M/PRODUCTS/FIELDS/`$field`")}: 
			{if $type=="opts"}
				{call "dl_options" field=$field}
			{else}
				{$item->$field}
			{/if}			
		</li>
	{/if}
{/function}



<!-- Shopping Cart -->
 <div id="step1" class="active">
   <div class="row">
     <div class="col-md-8 g-mb-30">
       <!-- Products Block -->
       <div class="g-overflow-x-scroll g-overflow-x-visible--lg">
	 <table class="text-center w-100">
	   <thead class="h6 g-brd-bottom g-brd-gray-light-v3 g-color-black text-uppercase">
	     <tr>
	       <th class="g-font-weight-400 text-left g-pb-20">{GW::ln('/m/CART_ITEM')}</th>
	       <th class="g-font-weight-400 g-width-130 g-pb-20">{GW::ln('/m/PRICE')}</th>
	       <th class="g-font-weight-400 g-width-50 g-pb-20">{GW::ln('/m/QTY')}</th>
	       <th class="g-font-weight-400 g-width-130 g-pb-20">{GW::ln('/m/TOTAL')}</th>
	       <th></th>
	     </tr>
	   </thead>

	   <tbody>
		   
		  
		   
{foreach GW::$globals.site_cart->items as $item}

     {if $item}
	     {$obj=$item->obj}
	     <!-- Item-->
	     <tr class="g-brd-bottom g-brd-gray-light-v3 cartitem">
	       <td class="text-left g-py-25">
		       <a href="{$item->link}" class="g-text-underline--none--hover">
			       {call cart_item_img_or_category class="d-inline-block g-width-100 mr-4" imsize="150x150"}
			</a>
		 
		 <div class="d-inline-block align-middle">
		   <h4 class="h6 g-color-black"><a href="{$item->link}">{$item->title}</a></h4>
		   <ul class="list-unstyled g-color-gray-dark-v4 g-font-size-12 g-line-height-1_6 mb-0">
		     {call "dl_specif_row" field=composer_id type=opts}
		     

			{call cart_item_expirable_prop addli=1}
		   </ul>
		 </div>
	       </td>
	       <td class="g-color-gray-dark-v2 g-font-size-13">
		       {if $item->obj->oldprice}<s class="g-color-gray-dark-v4">{$item->obj->oldprice} &euro;</s><br/>{/if}
		       <span class="{if $item->obj->oldprice}g-color-red{/if}">{$item->unit_price} &euro;</span>
		       
	       </td>
	       <td>
		 <div class="js-quantity input-group u-quantity-v1 g-width-80 g-brd-primary--focus">
			 
			 <input name="cart[{$item->id}][id]" type="hidden" value="{$item->id}">
			 
			 {$range = explode(';', $item->qty_range)}
			 {if $item->qty_range && $range.0!=$range.1}
				 
				 

				{if GW::s('PROJECT_NAME')=='manonatos.eu' || GW::s('PROJECT_NAME')=='natos.lt'}
					 <input name="cart[{$item->id}][qty]" 
						class="js-result form-control text-center g-font-size-13 rounded-0 g-pa-0" type="text" value="{$item->qty}" 
						readonly onchange="cartChanged()">	
					<div class="input-group-addon d-flex align-items-center g-width-30 g-brd-gray-light-v2 g-bg-white g-font-size-12 rounded-0 g-px-5 g-py-6">
					  <i class="js-plus g-color-gray g-color-primary--hover fa fa-angle-up" onclick="cartChanged()"></i>
					  <i class="js-minus g-color-gray g-color-primary--hover fa fa-angle-down" onclick="cartChanged()"></i>
					</div>				 
				{else}
				 <input id="cart_itm_{$item->id}" name="cart[{$item->id}][qty]" class="js-result form-control text-center g-font-size-13 rounded-0 g-pa-0" type="number" value="{$item->qty}" 
					onchange="if(this.value!=$(this).data('initial'))cartChanged()" onkeyup="$(this).change()" data-initial="{$item->qty}"
					min='{$range.0}' max='{$range.1}' 
					>
				 {/if}
			 {else}
				 {$item->qty}
			 {/if}
			 
			 

		 </div>
	       </td>
	       <td class="text-right g-color-black">
		      <span class="cart_total">{$item->total} &euro;</span>
		 <span class="g-color-gray-dark-v2 g-font-size-13 mr-4"> </span>
		 <span class="g-color-gray-dark-v4 g-color-black--hover g-cursor-pointer" onclick="$(this).parents('.cartitem').remove();cartChanged()">
		   <i class="mt-auto fa fa-trash"></i>
		 </span>
	       </td>
	     </tr>
	     <!-- End Item-->
     {/if}
{/foreach}

	   </tbody>
	 </table>
       </div>
       <!-- End Products Block -->
     </div>

	{include "`$m->tpl_dir`/summary.tpl" }
   </div>
 </div>
 <!-- End Shopping Cart -->