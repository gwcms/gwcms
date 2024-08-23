{include "default_open.tpl"}
{include "inputs/inputs.tpl"}


{function user_login_form}

	
	<div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-py-50 mb-4">
              <header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">{GW::ln('/m/SEND_SMS_CODE_TO_START')}</h1>
              </header>

              <!-- Form -->
              <form id="smsCodeForm" class="g-py-15" role="form" action="{$smarty.server.REQUEST_URI}" method="post"  onsubmit="if(!numberisvalid)return false;" >
		      <input type="hidden" name="act" value="do:sendSmsCode" />
                <div class="mb-4">

			{$limit_country = json_decode(mb_strtolower($m->cfg->phone_limit_country))}
			{$geoipcountry = strtolower(geoip_country_code_by_name($smarty.server.REMOTE_ADDR))}
			
			  {if $app->ln == 'lt'}
				  {$prefered_country = 'lt'}
			  {else}
				 
				 {if in_array($geoipcountry, $limit_country)}
					{$prefered_country = $geoipcountry} 
				 {/if}
				 
			  {/if}
			  
			  <!-- cbyip: {$geoipcountry} limit: {json_encode($limit_country)} pref: {$prefered_country} -->
			  
		{input type=intphone field="phone" value=$smarty.get.phone  input_name_pattern="%s" limit_country=$limit_country
			validactions='if(valid){ numberisvalid=true; $("#submitbutton").removeAttr("disabled"); }else{ numberisvalid=false; $("#submitbutton").attr("disabled","disabled"); }'}

                  </div>
                </div>
		  
                  

               
               <div class="mb-5">
                  <button id="submitbutton" disabled='disabled' class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25" type="button" onclick="$('#smsCodeForm').submit()">{GW::ln('/m/SEND_SMS_CODE')}</button>
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