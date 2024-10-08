	
{function "product_list"}
	{foreach $item->getOrderedItems() as $product}

		<div class="col-md-2 mb-2">
			{capture assign=alt}{$product->title}{/capture}
			<a href="{$ln}/direct/products/products/p/?id={$product->id}" title="{$product->title|escape}">
			{call name="product_image" product=$product size="100x100" alt=$alt}
			</a>
		</div>

	{/foreach}		    
{/function}


{function orderactions}
	
	{if $order->payment_status!=7 && $order->amount_total && $order->active}
		
		{if count($pay_methods) > 1 || $m->feat('mergepaymethods')}
			{$args=[id=>$order->id,orderid=>$order->id,payselect=>1]}
		{else}
			{$args=[act=>doOrderPay,id=>$order->id]}
		{/if}
		
			<a href="{if $order->open}{$m->buildUri(cart)}{else}{$m->buildUri(false, $args)}{/if}" class="btn u-btn-brown btn-md rounded-0">
				<i class="fa fa-credit-card g-mr-2"></i>

				{if $item->status==3} {*bank transfer confirm sent*}
					{GW::ln('/m/PROCEED_PAYMENT')}
				{else}
					{GW::ln('/m/PROCEED_DIFFERENT_PAYMENT')}
				{/if}				
				
			</a>
					

		{/if}
					
					
					
					
		{if $order->payment_status!=7 && $m->feat('otherpayee')}	
			
			<a href="{$m->buildUri('otherpayee', [id=>$order->id])}" class="btn u-btn-indigo btn-{$version} rounded-0 g-mt-5">
				<i class="fa fa-credit-card g-mr-2"></i>
				{GW::ln('/m/OTHERPAYEE')}
				
			</a>	
		{/if}
				
		{*
		{if $order->banktransfer_allow}
			<a href="{$m->buildUri('paybanktransfer', [id=>$order->id])}" class="btn u-btn-orange btn-{$version} rounded-0">
				<i class="fa fa-credit-card g-mr-2"></i>
				{GW::ln('/g/PROCEED_PAYMENT_BANKTRANSFER')}
			</a>					
		{/if}
		*}


	{if $order->downloadable && ($order->payment_status==7 || $order->amount_total==0)}
		<a class="gwUrlMod btn u-btn-primary btn-md rounded-0" href="#!" data-args='{ "act":"doDownload", "id":"{$order->id}", "key": "{$order->secret}" }'><i class="fa fa-download"></i> {GW::ln('/m/DOWNLOAD')}</a>
	{/if}
	

	
{/function}


{function "display_order_items"}
		{foreach $citems as $citem}
			{$obj=$citem->obj}

			{if $obj->composite_map.image && $obj->image}
				{$img = $obj->image}
				{$imurl="{$app_base}tools/img/{$img->key}&v={$img->v}&size=100x100"}
			{elseif $obj->cart_item_image}
				{$img = $obj->cart_item_image}
				{$imurl="{$app_base}tools/img/{$img->key}&v={$img->v}&size=100x100"}
			{elseif $obj->image_url}
				{$imurl="{$obj->image_url}&size=100x100"}
			{/if}
			{if $imurl}
				<a href="{$citem->link}"><img src="{$imurl}&size=100x100"></a>
			{/if}
		{/foreach}


		<ul class="u-alert-list g-mt-10 no-bullets">
			{foreach $citems as $citem}
				{$obj=$citem->obj}


				<li>

						{if $m->feat('multitype')}
							{GW::ln("/g/CART_ITM_{$citem->obj_type}")} - 
						{/if}
						{if $citem->link}<a href="{$citem->link}">{/if}

							{if $obj->context_short}<i>{$obj->context_short}</i> - {/if} 
							{$citem->invoice_line2}
						{if $citem->link}</a>{/if}

						{$citem->qty}x{$citem->unit_price}&nbsp;Eur 
					{if $citem->discount}
						<span class="g-color-lightred"><small>{GW::ln('/m/DISCOUNT')}:</small> -{$citem->discount*$citem->qty} &euro;</span>				
					{/if}
					{if !$order->payment_status!=7 && $item->can_remove}
						<a href="{$m->buildUri(false, [act=>doCartItemRemove,id=>$item->item,ciid=>$citem->id])}"><i class="fa fa-times"></i></a>
						{/if}

					{if $citem->expirable  && $order->payment_status!=7}
						<span title="{GW::ln('/m/FIELDS/expires')}: {$citem->expires}">
							<i class="fa fa-clock-o"></i>
							{if $citem->expires_secs > 3600}
								{GW_Math_Helper::uptime($citem->expires_secs, 1)}
							{elseif $citem->expires_secs > 0}

								<span class="countdown" data-expires="{$citem->expires_secs}">{GW_Math_Helper::uptime($citem->expires_secs)}</span>
							{else}
								{GW::ln('/M/orders/EXPIRED')}
							{/if}  
						</span>
					{/if}


				</li>
				{* 
				<li>{$pteam->partic1->title} + {$pteam->partic2->title} - {$pteam->payment_amount} Eur</li>
				*}
			{/foreach}
		</ul> 

		{if $order->adm_message}
			<div style='margin-top:5px'>
			<i class="fa fa-info-circle"></i> {$order->adm_message}
			</div>
		{/if}	
		{if $order->pay_type==banktransfer && $order->status==3 && $order->get('extra/bt_confirm') && !$smarty.get.payselect}
			<hr>
			<h6>{GW::ln('/m/BANK_TRANSFER_CONFIRM')} </h6>
			{$msg = $order->pay_user_msg}
			{$img = $order->banktransfer_confirm}
			<p><b>{GW::ln('/m/FIELDS/pay_user_submit_time')}:</b> {$order->get('extra/bt_confirm')}</p>
			{if $msg}
				<p><b>{GW::ln('/m/FIELDS/pay_user_details')}:</b> {$msg|escape}</p>
			{/if}				
			{if $img}
				<a href="{$app_base}tools/img/{$img->key}" target="_blank"><img alt="bank transfer confirm" src='{$app_base}tools/img/{$img->key}?size=150x150' style=''></a>
			{/if}					
		{/if}
		
		{if $order->payment_status!=7 && $smarty.get.paywait && $smarty.get.id}
			<div style='margin-top:5px'>
				<i class="fa fa-info-circle"></i> {GW::ln('/m/PAYMENT_PROCESSING')} <i class="fa fa-spinner fa-pulse fa-fw"></i>
			</div>		
			<script>setTimeout(function(){ location.href=location.href }, 8000)</script>
		{/if}
{/function}

{function "display_order"}
<div class="g-brd-around rounded g-mb-30 {if $order->id==$smarty.get.id}g-brd-blue{else}g-brd-gray-light-v4{/if}">
	<header class="g-bg-gray-light-v5 g-pa-20">
		<div class="row">
			<div class="col-sm-3 col-md-2 g-mb-20 g-mb-0--sm">
				<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">{GW::ln('/m/ORDER_PLACED')}</h4>
				<span class="g-color-black g-font-weight-300 g-font-size-13">{date('Y-m-d', strtotime($order->insert_time))}</span>
			</div>

			{if $order->amount_discount}
			<div class="col-sm-3 col-md-1 g-mb-20 g-mb-0--sm">
				<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">{GW::ln('/m/DISCOUNT')}</h4>
				<span class="g-color-lightred g-font-weight-300 g-font-size-13">-{$order->amount_discount} &euro;</span>
			</div>
			{/if}
			
			<div class="col-sm-3 col-md-1 g-mb-20 g-mb-0--sm">
				<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">{GW::ln('/m/TOTAL')}</h4>
				<span class="g-color-black g-font-weight-300 g-font-size-13">{$order->amount_total} &euro;</span>
			</div>


			{if $order->deliverable}
				<div class="col-sm-3 col-md-2 g-mb-20 g-mb-0--sm">
					<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">
						{if $order->delivery_opt==1} 
							{GW::ln('/m/SHIPPING_TO')} 
						{elseif $order->delivery_opt==3} 
							{GW::ln('/m/ORDER_PERSON')} 
						{else}
							{GW::ln('/m/ORDER_TAKES')} 
						{/if}
					</h4>
					<span class="g-color-black g-font-weight-300 g-font-size-13">{$order->name} {$order->surname}</span>
				</div>


				<div class="col-sm-3 col-md-2 g-mb-20 g-mb-0--sm">
					<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">
						 {GW::ln('/m/STATUS')}
					</h4>
					<span class="g-color-black g-font-weight-300 g-font-size-13">

						{if $item->payment_status==5}
							{GW::ln("/m/pay_status/5")}
						{else}
							{GW::ln("/m/status/`$order->status`")}
						{/if}
					</span>
				</div>
			{/if}
			
			{if $order->seller_id}
				<div class="col-sm-3 col-md-2 g-mb-20 g-mb-0--sm">
					<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">
						
						{GW::ln('/m/SELLER')}
					</h4>
					<span class="g-color-black g-font-weight-300 g-font-size-13">{$order->seller->title}</span>
				</div>				
			{/if}

			{if $order->pay_type}
			<div class="col-sm-3 col-md-2 g-mb-20 g-mb-0--sm">
				<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">
					 {GW::ln('/m/PAY_METHOD')}
				</h4>
				<span class="g-color-black g-font-weight-300 g-font-size-13">
					
					{if $order->pay_subtype}
						{$order->pay_subtype_human}
					{else}
						{GW::ln("/m/PAY_METHOD_{strtoupper($order->pay_type)}")}
					{/if}
				</span>
			</div>			
			{/if}




			<div class="col-sm-3 col-md-3 ml-auto text-sm-right">
				<div style="float:left">
					<h4 class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">{GW::ln('/m/ORDER')}<br/> # {$order->id}</h4>
				</div>
				<div style="float:left">
				
				
				<div class="btn-group">
					<a href="#" class="g-ml-5 text-uppercase dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						{*<i class="fa fa-bars" aria-hidden="true"></i>*}
						
<span class="u-icon-v1">
                <i class="icon-menu"></i>
              </span>			
					
					</a>
					<div class="dropdown-menu pull-right dropdown-menu-right"">



						{if $order->active}
							{if $order->payment_status==7}
								{$link=$m->buildDirectUri('prepareinvoice', [id=>$order->id])}
							{else}
								{$link=$m->buildDirectUri('prepareinvoice', [id=>$order->id,preinvoice=>1])}
							{/if}
							<a class="dropdown-item"  href="{$link}"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {GW::ln('/m/INVOICE')}</a>
						{/if}
			
						
						{*buildDirectUri -  is embed turi buti*}
						<a class="dropdown-item" href="{$m->buildDirectUri(false,[act=>doOrderSummary,id=>$order->id,viewable=>1])}">
							<i class="fa fa-print"></i> {GW::ln('/m/PRINT_ORDER_INFO')}
						</a>

						{if $m->auser}
							{$currentcartid=$m->auser->get('ext/cart_id')}
						{elseif $app->user}
							{$currentcartid = $app->user->get('ext/cart_id')}
						{/if}
						
						{if $order->payment_status!=7 && $currentcartid != $order->id && $order->get('extra/bt_confirm_cnt') < 1}
							<a href="{$m->buildUri(false, [act=>doOpenOrder,id=>$order->id])} " class="dropdown-item" title="{GW::ln('/m/VIEWS/doOpenOrder')}">
								<i class="fa fa-shopping-cart"></i> {GW::ln('/m/VIEWS/doOpenOrder_short')}
							</a>			
						{/if}
						<div class="dropdown-divider"></div>
						{if (($app->user && $app->user->isRoot()) || $m->can_do_test_pay) && $order->payment_status!=7}
						<a href="{$m->buildUri(false, [act=>doOrderPayRoot,id=>$order->id,key=>$order->secret])}" class="dropdown-item">
							<i class="fa fa-credit-card g-mr-2"></i>
							TEST pay!
						</a>
						{/if}
						{if $admin_enabled}
							<a class="dropdown-item" target="_blank" href='/admin/{$ln}/payments/ordergroups/{$order->id}/form'>
								 <i class='fa fa-pencil-square-o text-warning'></i> Go to admin </a>
						{/if}						
						
						{if $order->payment_status!=7}
						<a href="{$m->buildUri(false, [act=>doCancelOrder,id=>$order->id,state=>$order->active])} " class="dropdown-item">
							<i class="fa fa-times"></i> {if $order->active}{GW::ln('/g/CANCEL')}{else}{GW::ln('/g/UNDO_CANCEL')}{/if}
						</a>			
						{/if}
						
					</div>
				</div>				
			</div>
						</div>
		</div>
	</header>

<!-- Order Content -->
<div class="g-pa-20">



			{*
			<div class="mb-4">
			<h3 class="h5 mb-1">Delivered Yesterday</h3>
			<p class="g-color-gray-dark-v4 g-font-size-13">Your package was delivered per the instructions.</p>
			</div>
			*}
			<div class="row">

				{$citems=$order->items}
				
				
				{if !$smarty.get.paywait || !$smarty.get.id}
					{$buttons = !$smarty.get.payselect && ($order->payment_status!=7 || $order->downloadable)}
				{/if}
				
				{if !$smarty.get.summary}
				<div class="{if $buttons}col-md-8{else}col-md-12{/if}">
					
					{call display_order_items}
				</div>
				{/if}
				
				{if $buttons}
				<div class="col-md-4">
					{if !$smarty.get.payselect}

						{call orderactions}

					{/if}					
				</div>
				{/if}
				
			</div>
			
				
			<div class="row">
				<div class="col-md-12">
				{if $smarty.get.id && $smarty.get.payselect}

					{include "`$m->tpl_dir`payselect.tpl"}
					<p>
						{GW::ln('/m/PAY_METHOD_SELECT')}:
					</p>
					<center>
						{call "pay_select_cart"}
					</center>
				{elseif $smarty.get.paymentselected}
					{include file="`$smarty.current_dir`/methods/`$smarty.get.paymentselected`.tpl"}
				{/if}					
				</div>
			</div>

				
	

		<!-- End Order Content -->
</div>

</div>
<!-- End Order Block -->
{/function}


{function display_order_old}
	{$citems = $order->items}
		{$items_cnt=count($citems)}
	


	

	<tr class="orderinfo {if $smarty.get.id==$order->id}alert-warning{/if}{if $citems}rowwitms{else}rownoitms{/if}">
		<td>{$order->id}</td>
		<td>{date('Y-m-d H:i',strtotime($order->insert_time))}</td>
		<td>{$items_cnt}</td>
		<td>
			{$order->amount_total} Eur
		</td>
		<td>

		</td>

	</tr>
	

	{if $citems}

		<tr class="itmsrow {if $smarty.get.id==$order->id}alert-warning{/if}">
			<td colspan="5">

				
			</td>
		</tr>
		

	{else}

	{/if}	
{/function}


{if $list}
	{foreach $list as $order}
		{$citems = $order->items}
		
		{if $smarty.get.orderid && $order->id!=$smarty.get.orderid}

		{else}
			{if $citems}
				{call "display_order"}
			{/if}
		{/if}
	{/foreach}
{else}
	<p>{GW::ln('/g/EMPTY_LIST')}</p>
{/if}










<style>
	.orderlist{ border-collapse: collapse; }
	.orderlist td, .orderlist td{ padding: 2px 5px 2px 5px;  }
	.rowwitms td{ border: 1px solid silver; border-bottom:0; }
	.rownoitms td{ border: 1px solid silver; }
	.itmsrow td{ border: 1px solid silver; border-top:0; }
	.orderinfo td { background-color: #eee }
	
	.dropdown-toggle::after{ content:"" !important}
	
	
	
ul.no-bullets {
  list-style-type: none; /* Remove bullets */
  {if !GW::s('ECOMMERCE_ISOLATION')}padding: 0; /* Remove padding */{/if}
  margin: 0; /* Remove margins */
}	
</style>