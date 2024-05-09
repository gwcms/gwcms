{include "default_open.tpl"}

{function user_login_form}

	
	<div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-py-50 mb-4">
              <header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">{GW::ln('/m/SEND_SMS_CODE_TO_START')}</h1>
              </header>

              <!-- Form -->
              <form id="smsCodeForm" class="g-py-15" role="form" action="{$smarty.server.REQUEST_URI}" method="post">
		      <input type="hidden" name="act" value="do:sendSmsCode" />
                <div class="mb-4">
                  <div class="input-group g-rounded-left-3">
                    <span class="input-group-prepend g-width-45">
                      <span class="input-group-text justify-content-center w-100 g-bg-transparent g-brd-gray-light-v3 g-color-gray-dark-v5">
                        <span class="material-symbols-outlined">phone_android</span>
                      </span>
                    </span>
                    <input name="phone" 
			   class="form-control g-color-black g-bg-white g-bg-white--focus g-brd-gray-light-v3 g-rounded-left-0 g-rounded-right-3 g-py-15 g-px-15" 
			   type="phone" 
			   placeholder="{GW::ln('/m/YOUR_PHONE_NUMBER')}" 
			   value="{$smarty.get.phone}"  required="1">
                  </div>
                </div>
		  
                  

               
               <div class="mb-5">
                  <button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25" type="button" onclick="$('#smsCodeForm').submit()">{GW::ln('/m/SEND_SMS_CODE')}</button>
                </div>
{/function}



	
	      <!-- Login -->
      <section class="container g-pt-100 g-pb-20">
        <div class="row justify-content-between">
          <div class="col-md-6 col-lg-5 order-lg-2 g-mb-80" style="float:none;margin:auto;">
            {call user_login_form}
          </div>
        </div>
      </section>



{include "default_close.tpl"}