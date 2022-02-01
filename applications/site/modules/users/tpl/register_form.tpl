{include "inputs/inputs.tpl"}

            <header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">{GW::ln('/m/REGISTER_FORM_HEADING')}</h1>
              </header>

              <!-- Form -->
              <form id="regForm" class="g-py-15" action="{$smarty.server.REQUEST_URI}" method="post">
		<input type="hidden" name="act" value="do:register" />		      
		
		

		
				
		{if !$smarty.session.3rdAuthUser}
			<div class="d-flex justify-content-center text-center g-mb-20">
			  <div class="d-inline-block align-self-center g-width-50 g-height-1 g-bg-gray-light-v1"></div>
			  <span class="align-self-center g-color-gray-dark-v5 mx-4">{GW::ln('/m/REGISTER_WITH_3RD_PARTY')}</span>
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
		
			<div class="d-flex justify-content-center text-center g-mt-40  g-mb-20">
			  <div class="d-inline-block align-self-center g-width-50 g-height-1 g-bg-gray-light-v1"></div>
			  <span class="align-self-center g-color-gray-dark-v5 mx-4">{GW::ln('/m/REGISTER_REGURAL')}</span>
			  <div class="d-inline-block align-self-center g-width-50 g-height-1 g-bg-gray-light-v1"></div>
			</div>		
		
		{/if}			
		
	
		
                
		<div class="row">    
			
			{$fields= $m->getFieldsConfig()}
			
			
			<div class="col-md-6">
				{input field="email" type=email required=1 note=GW::ln('/M/USERS/REGISTER_EMAIL_NOTE') help=GW::ln('/M/USERS/REGISTER_EMAIL_HELP')}
				{input field="pass_new" required=1 type=password}
				{input field="pass_new_repeat" required=1 type=password}
	
				{if $fields.fields.name}						
					{input field="name" required=$fields.required.name}
				{/if}				
									
			</div>
			<div class="col-md-6">
				{if $fields.fields.surname}						
					{input field="surname" required=$fields.required.surname}
				{/if}
				{if $fields.fields.phone}						
					{input field="phone" required=$fields.required.phone}
				{/if}						
				
				{if $fields.fields.company_name}						
					{input field="company_name" required=$fields.required.company_name}
				{/if}									
				{if $fields.fields.country}						
					{input field="country" required=$fields.required.country type=select empty_option=1 options=$countries}
				{/if}
				
				
				{*input field="company_name" required=0*}	
				
				{if $fields.fields.agreetc}	
					{capture assign=tmp}
						{GW::ln("/m/I_ACCEPT")} &nbsp; <a href="#" onclick="$('#termsandconds').toggle();return false">{GW::ln("/m/TERMS_AND_CONDS")}</a> &nbsp;
					{/capture}

					{input field=agreetc type=checkbox required=1 title=$tmp}
				{/if}
				
				
				{if $fields.fields.newsletter}
					{input field=newsletter type=checkbox title=GW::ln("/m/SUBSCRIBE_TO_NEWSLETTER")}
				{/if}
				
				
				
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
				
		<iframe id="termsandconds" src="/{$ln}/f/terms_and_conditions?clean=1" style="width:100%;height:400px;display:none"></iframe>	
				
		      
		
		<div class="row justify-content-md-center">		
                  <div class="col-md-6">
                <button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25 mb-4 g-mt-20" type="button" onclick="$('#regForm').submit()">{GW::ln('/m/REGISTER')}</button>
		</div>
		</div>		
			
			
		
              </form>
              <!-- End Form -->