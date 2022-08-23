

            <header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">{GW::ln('/m/REGISTER_FORM_HEADING')}</h1>
              </header>

	      
	      {GW::ln('/m/REGISTER_NOTES')}
	      
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
		
	
		
                
		   
		{include "{$m->tpl_dir}user_form.tpl" register=1}
	
				
		      
		
		<div class="row justify-content-md-center">		
                  <div class="col-md-6">
                <button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25 mb-4 g-mt-20" type="button" onclick="$('#regForm').submit()">{GW::ln('/m/REGISTER')}</button>
		</div>
		</div>		
			
			
		
              </form>
              <!-- End Form -->