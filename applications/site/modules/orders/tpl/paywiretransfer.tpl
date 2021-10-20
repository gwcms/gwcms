{include "default_open.tpl"}

{include "`$smarty.current_dir`/order_display.tpl"}


{include file="inputs/inputs.tpl"}	

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
<section class="dzsparallaxer auto-init height-is-based-on-content use-loading mode-scroll loaded dzsprx-readyall" data-options="{ direction: 'reverse', settings_mode_oneelement_max_offset: '150' }">
      <!-- Parallax Image -->
      <div style="height: 200%; background-image: url(&quot;{$assets}../assets/img/bg/pattern6-2.png&quot;); transform: translate3d(0px, -114.315px, 0px);" class="divimage dzsparallaxer--target w-100 g-bg-repeat g-bg-gray-light-v4"></div>
      <!-- End Parallax Image -->

      <div class="container g-z-index-1 g-py-100">
        <h1 class="g-font-weight-300 g-letter-spacing-1 g-mb-50">{GW::ln('/m/PAY_CREDIT_CARD')}</h1>

        <div class="lead g-font-weight-400 g-line-height-2 g-letter-spacing-0_5">
          <p class="mb-0">
            <br>These Components can be easily used and customized in any blocks.</p>
        </div>
      </div>
    </section>	
*}

<form id="wiretransferform" method="post" action="{$smarty.server.REQUEST_URI}">

	{$step=$smarty.get.step|default:1}
	{$step=intval($step)}
     <!-- Checkout Form -->
      <div class="container g-pt-30 g-pb-70">
	      
<input name="act" type="hidden" value="doSaveWireTransferConfirm"/>
<input name="order_id" type="hidden"  value="{$item->id}"/>


<!-- Shipping -->
 <div id="step2" class="active">
   <div class="row">
     <div class="col-md-8 g-mb-30">
       <!-- Shipping details -->

       	
       
<div>
	{GW::ln("/G/paymethods/banktransfer")}
</div>

<br><br>

{GW::ln('/m/ORDER_TOTAL')}: <b>{$order->amount_total} &euro;</b><br>
{GW::ln('/m/WIRE_TRANSFER_DETAILS')}: <b>Natos{$order->id}</b>


<br><br>

		
  <div id="deliveryForm" >
		<div class="row" >      
			<div class="col-md-6">
				
				{input field="wiretransfer_userconfirm" required=1 title=GW::ln('/m/BANKTRANSFER_CONFIRM_INPUT_TITLE') type=number}
			</div>
		</div>		
			
		<button onclick="$('#wiretransferform').submit()" class="btn btn-block u-btn-primary g-font-size-13 text-uppercase g-py-15 mb-4 mt-4" type="button">
			{GW::ln('/m/CONFIRM_WIRETRANSFER')}
		</button>
			
    </div>       
       <!-- Shipping details -->
     </div>
			
	{include "`$m->tpl_dir`/summary.tpl" step=4 order=$order prodlist=$products_list}
	
   </div>
			
 </div>
</div>
	
</form>

{include "default_close.tpl"}
