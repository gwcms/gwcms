{include "default_open.tpl"}

{include "inputs/inputs.tpl"}

{capture append=footer_hidden}
<!-- JS Implementing Plugins -->
<script  src="{$assets}../assets/vendor/appear.js"></script>

<!-- JS Unify -->
<script  src="{$assets}../assets/js/components/hs.counter.js"></script>

<!-- JS Plugins Init. -->
<script >
  $(document).on('ready', function () {
    // initialization of counters
    var counters = $.HSCore.components.HSCounter.init('[class*="js-counter"]');
  });
</script>
{/capture}

{$counters=$app->subProcessPath('products/products/noview',[act=>doInitNumbers])}


<section class="container  g-pt-20 g-pb-20">
        <div class="row">
		<div class="col-lg-7 order-lg-2">
			
            <div class="text-center  mb-30">
              <p class="g-color-gray-dark-v5 mb-0">{GW::ln('/m/ALREADY_HAVE_ACCOUNT_Q')}
                <a class="g-font-weight-600" href="{$ln}/direct/users/users/login">{GW::ln('/M/USERS/VIEWS/login')}</a></p>
            </div>
		<br />	    
			
            <div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-py-50 mb-4">
              <header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">{GW::ln('/m/REGISTER_FORM_HEADING')}</h1>
              </header>

              <!-- Form -->
              <form id="regForm" class="g-py-15" action="{$smarty.server.REQUEST_URI}" method="post">
		<input type="hidden" name="act" value="do:register" />		      
		
                
		<div class="row">      
			<div class="col-md-6">
				{input field="email" type=email required=1 note=GW::ln('/M/USERS/REGISTER_EMAIL_NOTE') help=GW::ln('/M/USERS/REGISTER_EMAIL_HELP')}
				{input field="pass_new" required=1 type=password}
				{input field="pass_new_repeat" required=1 type=password}
				{input field="name" required=1}	
									
			</div>
			<div class="col-md-6">
				{input field="surname" required=1}
				{input field="phone" required=1}	
				{input field="company_name" required=0}	
				
				{input field="country" required=1 type=select empty_option=1 options=$countries}
				{*input field="company_name" required=0*}	
				
				{capture assign=tmp}
					{GW::ln("/m/I_ACCEPT")} &nbsp; <a href="#" onclick="$('#termsandconds').toggle();return false">{GW::ln("/m/TERMS_AND_CONDS")}</a> &nbsp;
				{/capture}
				
				{input field=agreetc type=checkbox required=1 title=$tmp}	
				
				
				
				{input field=newsletter type=checkbox title=GW::ln("/m/SUBSCRIBE_TO_NEWSLETTER")}
				
				
				
			    	{if $smarty.session.3rdAuthUser}
							      
					
					{capture assign="titletmp"}
						{$tmptype=strtoupper($smarty.session.3rdAuthUser->type)}
						  {GW::ln('/m/LINK_WITH_X',[v=>[type=>$tmptype]])} <b class="ml-1"> {$smarty.session.3rdAuthUser->title}</b>
					{/capture}
					{call "input" field=3rdAuthUserlink required=0 type=checkbox title=$titletmp value=1}
					 
				{/if}	
				
				
	
								
				
			</div>
		</div>
				
				{if $recapPublicKey}
					<div class="row g-mb-10">
					      <div class="col-md-6">
					   <div class="g-recaptcha" data-sitekey="{$recapPublicKey}" style="margin-bottom:5px;"></div>
						   </div>	  

					   <script src='https://www.google.com/recaptcha/api.js' async defer></script>
					   <script>
					       $('#regForm').submit(function(event) {

						   var response = grecaptcha.getResponse();

						   if(response.length == 0){
							   event.preventDefault();
							   alert('{GW::ln('/G/validation/RECAPTCHA_FAILED')|escape:'javascript'}');
						   }
					     });
					   </script>
				      </div>				
			      {/if}					
				
		<iframe id="termsandconds" src="/{$ln}/sys/termsandconds?clean=1" style="width:100%;height:400px;display:none"></iframe>	
				
		      
                <div class="row justify-content-md-center">		
                  <div class="col-md-6">
                <button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25 mb-4" type="button" onclick="$('#regForm').submit()">{GW::ln('/m/REGISTER')}</button>
		</div>
		</div>
		
				
		{if !$smarty.session.3rdAuthUser}
                <div class="d-flex justify-content-center text-center g-mb-30">
                  <div class="d-inline-block align-self-center g-width-50 g-height-1 g-bg-gray-light-v1"></div>
                  <span class="align-self-center g-color-gray-dark-v5 mx-4">{GW::ln('/m/OR_REGISTER_WITH_3RD_PARTY')}</span>
                  <div class="d-inline-block align-self-center g-width-50 g-height-1 g-bg-gray-light-v1"></div>
                </div>

		  
                <div class="row justify-content-md-center">		
                  <div class="col-6">
                    <a href="{$app->buildURI('direct/users/fblogin/redirect')}" class="btn btn-block u-btn-facebook g-font-size-12 text-uppercase g-py-12 g-px-25 mr-2" type="button" >
                      <i class="mr-1 fa fa-facebook"></i>
                      Facebook
                    </a>
                  </div>
                  <div class="col-6">
                    <a href="{$app->buildURI('direct/users/gglogin/redirect')}" class="btn btn-block u-btn-lightred g-font-size-12 text-uppercase g-py-12 g-px-25 ml-2" type="button">
                      <i class="mr-1 fa fa-google"></i>
                      Google
                    </a>
                  </div>		      
			{*
                  <div class="col-6">
                    <button class="btn btn-block u-btn-twitter g-font-size-12 text-uppercase g-py-12 g-px-25 ml-2" type="button">
                      <i class="mr-1 fa fa-twitter"></i>
                      Twitter
                    </button>
                  </div>
		  *}
                </div>
		
		{/if}			
			
			
			
		
              </form>
              <!-- End Form -->
            </div>

            <div class="text-center">
              <p class="g-color-gray-dark-v5 mb-0">{GW::ln('/m/ALREADY_HAVE_ACCOUNT_Q')}
                <a class="g-font-weight-600" href="{$ln}/direct/users/users/login">{GW::ln('/M/USERS/VIEWS/login')}</a></p>
            </div>
			</div>
	    
			
		<div class="col-lg-5 order-lg-1 g-mb-80  g-hidden-sm-down">
            <div class="g-pr-20--lg">
              <div class="mb-5">
                <h2 class="h1 g-font-weight-100 mb-3">{GW::ln('/m/SIGNUP_H_TEXT')}</h2>
                <p class="g-color-gray-dark-v4">{GW::ln('/m/SIGNUP_INVITE_TEXT')}</p>
              </div>

              <div class="row text-center mb-5">
		      
		   {foreach $counters.array as $id => $val}
			<div class="col-lg-6 g-mb-10">
			  <!-- Counters -->
			  <div class="g-bg-gray-light-v5 g-pa-20">
			    <div class="js-counter g-color-gray-dark-v5 g-font-weight-300 g-font-size-25 g-line-height-1">{$val}</div>
			    <div class="d-inline-block g-width-10 g-height-2 g-bg-gray-dark-v5 mb-1"></div>
			    <h4 class="g-color-gray-dark-v4 g-font-size-12 text-uppercase">{GW::ln("/M/products/COUNTERS/`$id`")}</h4>
			  </div>
			  <!-- End Counters -->
			</div>			      
		{/foreach}
		      


                
              </div>

              <div class="text-center">
                <h2 class="h4 g-font-weight-100 mb-4">{GW::ln('/M/PRODUCTS/NUM_PRODUCT_1')}
                  <span class="g-color-primary">{$counters.product}</span> {GW::ln('/M/PRODUCTS/NUM_PRODUCT_2')}</h2>
                <img class="img-fluid g-opacity-0_6" src="{$assets}assets/img/maps/map.png" alt="Image Description">
              </div>
            </div>
          </div>			
			
          </div>



      </section>

{*
<div id="cart">
{if $smarty.get.success}
	{$m->lang.USER_REGISTER_SUCCESS}
	<a href="">{$m->lang.LOGIN}</a>
{else}

{$page->getContent('top_text')}


<small><em>{$lang.ASTERISK_REQUIRED_FIELDS}</em></small>

{function input}
	<tr>
		<td {if $m->error_fields.$field}class="error_cell"{/if}>{$m->lang.FIELDS.$field} {if $required}*{/if}</td>
		<td><input {if $type=="password"}type="password"{else}type="text"{/if} name="item[{$field}]" value="{$item->$field|escape}"></td>
		<td class="error_cell">
			{if $m->error_fields.$field}
				{GW_Error_Message::read($m->error_fields.$field)}
			{/if}
		</td>
	</tr>
{/function}


<form action="{$smarty.server.REQUEST_URI}" method="post" class="user_register">
<input type="hidden" name="act" value="do:register" />

<table>
	{input field="email" required=1}
	{input field="pass_new" required=1 type="password"}
	{input field="pass_new_repeat" required=1 type="password"}	
	{input field="first_name" required=1}	
	{input field="second_name" required=1}	
	{input field="phone" required=1}	
	{input field="company_name" required=0}


	<tr><td></td><td><input id="buylink" type="submit" value="Siųsti"></td></tr>

</table>

</form>

{/if}
</div>

<br /><br />
*}

{include "default_close.tpl"}