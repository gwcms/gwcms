
{$input_name_pattern="order[%s]"}
{$select_newuser_or_existing =  $app->user || $order->status>1}
{include file="inputs/inputs.tpl"}	


<input name="act" type="hidden" value="doSaveDelivery"/>

<!-- Shipping -->
 <div id="step2" class="active">
   <div class="row">
     <div class="col-md-8 g-mb-30">
       <!-- Shipping details -->

       	
<div  class="row" id="notLoggedInOptions" style="{if $select_newuser_or_existing}display:none{/if}">
	<div class="col-md-6">
		<button class="btn btn-block u-btn-orange g-font-size-13 text-uppercase g-py-15 mb-4" type="button" data-next-step="#step2" onclick="location.href='{$app->buildUri('direct/users/users/login',['RETURN_TO'=>$smarty.server.REQUEST_URI])}'">{GW::ln('/m/I_HAVE_ACCOUNT')}</button>
	</div>
	<div class="col-md-6">
		<button class="btn btn-block u-btn-indigo g-font-size-13 text-uppercase g-py-15 mb-4" type="button" data-next-step="#step2" onclick="console.log('test');iamNewUser()">{GW::ln('/m/I_AM_NEW')}</button>
	</div>
</div>
		
  <div id="deliveryForm" style="{if !$select_newuser_or_existing}display:none{/if}">

      
	<div class="row">			
		<div class="col-lg-9">
			{if $order->delivery_opt==3}
				{input field="delivery_opt" required=1 type=select options=[3=>GW::ln('/m/DELIVERY_3')] readonly=1}
			{else}
				{input field="delivery_opt" required=1 type=select options=['1'=>GW::ln('/m/DELIVERY_1'),'2'=>GW::ln('/m/DELIVERY_2')] empty_option=1 addclass="deliverySelect"}
			{/if}
		   </div>
		 
	</div>

		<div class="row" {if $order->delivery_opt!=3}style="display:none"{/if} id="orderDetails">      
						
				
			<div class="col-md-12">
				{call input field="email" type=email required=1 note=GW::ln('/M/USERS/REGISTER_EMAIL_NOTE')}
			</div>

			<div class="col-md-6">
				{call input field="name" required=1}	
			</div>
			<div class="col-md-6">
				{call input field="surname" required=1}
			</div>	


			<div class="col-md-6">
				{call input field="phone" required=1}	
			</div>
			<div class="col-md-6">
				{call input field="company" required=0}
			</div>	



			{if $m->config->international_delivery}
			<div class="col-md-6 deliveryAddress">
				{call input field="country" required=1 type=select options=$options.country empty_option=1}
			</div>
			{/if}
			<div class="col-md-6 deliveryAddress">
				{call input field="region" required=0}
			</div>				
			<div class="col-md-6 deliveryAddress">
				{call input field="city" required=1}	
			</div>
			<div class="col-md-6 deliveryAddress">
				{call input field="address_l1" required=1 note=GW::ln('/M/USERS/address_l1_NOTE')}
			</div>				
			{*
			<div class="col-md-6 deliveryAddress">
				{call input field="address_l2" required=0 note=GW::ln('/M/USERS/address_l2_NOTE')}	
			</div>
			*}
			<div class="col-md-6 deliveryAddress">
				{call input field="postcode" required=1}
			</div>				
				
			<div class="col-md-6">
				
				
				
				{if !$app->user}
					{call input field="reuse_addr" note=GW::ln('/m/ONLY_FOR_USERS') type=checkbox required=0 disabled=1}
					
					{if $app->user && $app->user->isRoot()}
						{call input field="create_user" type=checkbox hidden_note="Galėsite matyti buvusius užsakymus, pageidavimų sąrašą, atidėti mokėjimus ir daug daugiau..."}
					{/if}
				{else}
					{call input field="reuse_addr" type=checkbox required=0}
				{/if}
			</div>	
			
			<div class="col-md-6">
				{call input field="need_invoice" type=checkbox required=0 addclass="neeedinvTrig"}
			</div>	
			<div class="col-md-6 invoiceDetails">
				{call input field="company_code" }
			</div>							
			<div class="col-md-6 invoiceDetails">
				{call input field="vat_code"}
			</div>							
			<div class="col-md-6 invoiceDetails">
				{call input field="company_addr"}
			</div>			
			
			<button id="proceedCheckout" class="btn btn-block u-btn-primary g-font-size-13 text-uppercase g-py-15 mb-4" type="button">{GW::ln('/m/PROCEED2PAYMENT')}</button>
		

		</div>		
		



     
			
			
	
			
    </div>       
       
       
			
       
       
       <!-- Shipping details -->
     </div>

				
	{include "`$m->tpl_dir`/summary.tpl" }
	
   </div>
 </div>
   
   <script>
	   function iamNewUser()
	   {
		$('#deliveryForm').fadeIn();
		$('#notLoggedInOptions').hide();		
	   }
	   
	   $(function(){
		   $('.deliveryAddress').hide();
		   
		   $('#proceedPayment').click(function(){
				$('#step').val(3);
				$('#cartList').submit()
		   })
		   
		   $('.deliverySelect').change(function(){
	   
			if($(this).val() == '1'){
				$('.deliveryAddress').fadeIn();

			}else{
				$('.deliveryAddress').hide();
			}
			if($(this).val()){
				$('#orderDetails').fadeIn();
			}else{
				$('#orderDetails').hide();
			}
			
	   			   
		   }).change();
		   
		   
		   $('.neeedinvTrig').change(function(){
			   //console.log($(this).val());
			   if($(this).prop('checked')){
				 $('.invoiceDetails').fadeIn();
			   }else{
				 $('.invoiceDetails').hide();
			   }
		   }).change();
		})

	 </script>
 <!-- End Shipping -->
