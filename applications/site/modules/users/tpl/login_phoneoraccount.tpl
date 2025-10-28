{include "default_open.tpl"}
{include "inputs/inputs.tpl"}


{function user_login_form_2}
	 
	
	
	<div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-py-50 mb-4">

	  <div class="text-center">
              <p class="g-color-gray-dark-v5 mb-0">{GW::ln('/m/DONT_HAVE_ACCOUNT')}
                <a class="g-font-weight-600" rel="nofollow" href="{$ln}/direct/users/users/register">{GW::ln('/m/VIEWS/register')}</a></p>
            </div>  
	    <br><br>		
		
              <header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">{GW::ln('/m/LOGIN_TO_YOUR_ACCOUNT')}</h1>
              </header>

              <!-- Form -->
              <form id="loginform" class="g-py-15" role="form" action="{$smarty.server.REQUEST_URI}" method="post">
		      <input type="hidden" name="act" value="do:login" />
                <div class="mb-4">
                  <div class="input-group g-rounded-left-3">
                    <span class="input-group-prepend g-width-45">
                      <span class="input-group-text justify-content-center w-100 g-bg-transparent g-brd-gray-light-v3 g-color-gray-dark-v5">
                        <i class="icon-finance-067 u-line-icon-pro"></i>
                      </span>
                    </span>
                    <input name="login[0]" 
			   class="form-control g-color-black g-bg-white g-bg-white--focus g-brd-gray-light-v3 g-rounded-left-0 g-rounded-right-3 g-py-15 g-px-15" type="email" placeholder="{GW::ln('/m/USERNAME_EMAIL')}" 
			   value="{$login->username|escape}"  required="1">
                  </div>
                </div>

                <div class="mb-4">
                  <div class="input-group g-rounded-left-3 mb-4">
                    <span class="input-group-prepend g-width-45">
                      <span class="input-group-text justify-content-center w-100 g-bg-transparent g-brd-gray-light-v3 g-color-gray-dark-v5">
                        <i class="icon-media-094 u-line-icon-pro"></i>
                      </span>
                    </span>
                    <input class="form-control g-color-black g-bg-white g-bg-white--focus g-brd-gray-light-v3 g-rounded-left-0 g-rounded-right-3 g-py-15 g-px-15" type="password"
			   name="login[1]" placeholder="{GW::ln('/m/PASSWORD')}" required="1">
                  </div>
                </div>
		  
					{if $smarty.session.3rdAuthUser}
						
						
                <div class="row justify-content-between mb-4">
			<div class="col align-self-center">
                    <label class="form-check-inline u-check g-color-gray-dark-v5 g-font-size-13 g-pl-25 mb-0">
                      <input class="g-hidden-xs-up g-pos-abs g-top-0 g-left-0" type="checkbox" name="link3rdAuthUser" checked="checked" >
                      <span class="d-block u-check-icon-checkbox-v6 g-absolute-centered--y g-left-0">
                        <i class="fa" data-check-icon="&#xf00c"></i>
                      </span>
		      {$tmptype=strtoupper($smarty.session.3rdAuthUser->type)}
		      
		     {if $smarty.session.3rdAuthUser->picture}
			     <img src="{$smarty.session.3rdAuthUser->picture}" style="border-radius: 50%;height:30px;" class="mr-1" />
		     {/if}
                     {GW::ln('/m/LINK_WITH_X',[v=>[type=>$tmptype]])} <b class="ml-1"> {$smarty.session.3rdAuthUser->title}</b>
		     <a href="{$app->buildUri(false,[act=>doUnset3rdAuthUser])}">[x]</a>
                    </label>
                  </div>
                </div>						
						
					{/if}					
		  

                <div class="row justify-content-between mb-5">
                  <div class="col align-self-center">
                    <label class="form-check-inline u-check g-color-gray-dark-v5 g-font-size-13 g-pl-25 mb-0">
                      <input class="g-hidden-xs-up g-pos-abs g-top-0 g-left-0" type="checkbox" name="login_auto" >
                      <span class="d-block u-check-icon-checkbox-v6 g-absolute-centered--y g-left-0">
                        <i class="fa" data-check-icon="&#xf00c"></i>
                      </span>
                      {GW::ln('/m/KEEP_SIGNED_IN')}
                    </label>
                  </div>
                  <div class="col align-self-center text-right">
                    <a class="g-font-size-13" rel="nofollow" href="{$ln}/direct/users/users/passreset">{GW::ln('/m/FORGOT_PASSWORD')}</a>
                  </div>
                </div>

                <div class="mb-5">
                  <button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25" type="button" onclick="$('#loginform').submit()">{GW::ln('/m/LOGIN')}</button>
                </div>

		{if !$smarty.session.3rdAuthUser && !$m->cfg->get(disable3rdAuthGateways)}
                <div class="d-flex justify-content-center text-center g-mb-30">
                  <div class="d-inline-block align-self-center g-width-50 g-height-1 g-bg-gray-light-v1"></div>
                  <span class="align-self-center g-color-gray-dark-v5 mx-4">{GW::ln('/m/OR_LOGIN_WITH_3RD_PARTY')}</span>
                  <div class="d-inline-block align-self-center g-width-50 g-height-1 g-bg-gray-light-v1"></div>
                </div>

                <div class="row  justify-content-md-center">
				
		{if $m->cfg->get(login_with_fb)}
                  <div class="col-md-6">
                    <a href="{$app->buildURI('direct/users/fblogin/redirect')}" class="btn btn-block u-btn-facebook g-font-size-12 text-uppercase g-py-12 g-px-25 mr-2" type="button" >
                      <i class="mr-1 fa fa-facebook"></i>
                      Facebook
                    </a>
                  </div>
		  {/if}
		
                  <div class="col-md-6">
                    <a href="{$app->buildURI('direct/users/gglogin/redirect')}" class="btn btn-block u-btn-lightred g-font-size-12 text-uppercase g-py-12 g-px-25 ml-2" type="button">
                      <i class="mr-1 fa fa-google"></i>
                      Google
                    </a>
                  </div>
		  
                </div>
		{/if}
              </form>
              <!-- End Form -->
            </div>

            
{/function}



{function user_login_form}

	
	<div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-py-30 mb-4">
              <header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">{GW::ln('/m/SEND_SMS_CODE_TO_START')}</h1>
              </header>

              <!-- Form -->
              <form id="smsCodeForm" class="g-py-15" role="form" action="{$smarty.server.REQUEST_URI}" method="post"  onsubmit="if(!numberisvalid)return false;" >
		      <input type="hidden" name="act" value="do:sendSmsCode" />
                

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

			
		<br><br>	
		<button id="submitbutton" disabled='disabled' class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25" type="button" onclick="$('#smsCodeForm').submit()">{GW::ln('/m/SEND_SMS_CODE')}</button>
                </form>  
	</div>
{/function}



	
	      <!-- Login -->
      <section class="container g-pt-100 g-pb-20">
        <div class="row justify-content-between">
          <div class="col-md-6 col-lg-5 order-lg-2 g-mb-80" style="float:none;margin:auto;">
		  
		  <header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">{GW::ln('/m/AUTHORIZE_TO_START')}</h1>
              </header>	
		  
            {call user_login_form}
	    <br><center>--- {GW::ln('/m/OR')} ---</center><br>
	    {call user_login_form_2}
          </div>
        </div>
      </section>



{include "default_close.tpl"}