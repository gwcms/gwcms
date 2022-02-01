{include "default_open.tpl"}

{function user_login_form}
	<div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-py-50 mb-4">
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
                     {GW::ln('/m/LINK_WITH_X',[v=>[type=>$tmptype]])} <b class="ml-1"> {$smarty.session.3rdAuthUser->title}</b>
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
                    <a class="g-font-size-13" href="{$ln}/direct/users/users/passreset">{GW::ln('/m/FORGOT_PASSWORD')}</a>
                  </div>
                </div>

                <div class="mb-5">
                  <button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25" type="button" onclick="$('#loginform').submit()">{GW::ln('/m/LOGIN')}</button>
                </div>

		{if !$smarty.session.3rdAuthUser}
                <div class="d-flex justify-content-center text-center g-mb-30">
                  <div class="d-inline-block align-self-center g-width-50 g-height-1 g-bg-gray-light-v1"></div>
                  <span class="align-self-center g-color-gray-dark-v5 mx-4">{GW::ln('/m/OR_LOGIN_WITH_3RD_PARTY')}</span>
                  <div class="d-inline-block align-self-center g-width-50 g-height-1 g-bg-gray-light-v1"></div>
                </div>

                <div class="row no-gutters">
					
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
		  
                </div>
		{/if}
              </form>
              <!-- End Form -->
            </div>

            <div class="text-center">
              <p class="g-color-gray-dark-v5 mb-0">{GW::ln('/m/DONT_HAVE_ACCOUNT')}
                <a class="g-font-weight-600" href="{$ln}/direct/users/users/register">{GW::ln('/m/VIEWS/register')}</a></p>
            </div>
{/function}


{capture assign=infoblock}{strip}{include "user_login_infoblock.tpl"}{/strip}{/capture}

{if $infoblock}
      <!-- Login -->
      <section class="container g-pt-100 g-pb-20">
        <div class="row justify-content-between">
          <div class="col-md-6 col-lg-5 order-lg-2 g-mb-80">
            {call user_login_form}
          </div>

          <div class="col-md-6 order-lg-1 g-mb-80">
		{$infoblock}
          </div>
        </div>
      </section>
{else}
	{call user_login_form}
{/if}
      <!-- End Login -->

{*	
<div class="center-block logig-form">
	<div class="panel panel-primary">
		<div class="panel-heading">{GW::ln('/m/LOGIN_FORM')}</div>
		<div class="panel-body">
			<form role="form" action="{$smarty.server.REQUEST_URI}" method="post">
				<input type="hidden" name="act" value="do:login" />

				<div class="form-group">
					<div class="input-group login-input {if $smarty.get.error}has-error has-feedback{/if}">
						<span class="input-group-addon"><i class="fa fa-user"></i></span>
						<input  name="login[0]"  value="{$login->username|escape}" type="text" class="form-control"  required="1">
					</div>
					<br>
					<div class="input-group login-input {if $smarty.get.error}has-error has-feedback{/if}">
						<span class="input-group-addon"><i class="fa fa-lock"></i></span>
						<input  name="login[1]" type="password" class="form-control" placeholder="{GW::ln('/m/PASSWORD')}" required="1">
					</div>
					<div class="checkbox">
						<input type="checkbox" id="checkbox_remember" name="login_auto" {if $login->auto}checked="checked"{/if}>
						<label for="checkbox_remember">{GW::ln('/m/REMEMBER_ME')}</label>
					</div>
					<button type="submit" class="btn btn-ar btn-primary pull-right">{GW::ln('/m/LOGIN')}</button>
					<a href="#" class="social-icon-ar sm twitter animated fadeInDown animation-delay-2"><i class="fa fa-twitter"></i></a>
					<a href="#" class="social-icon-ar sm google-plus animated fadeInDown animation-delay-3"><i class="fa fa-google-plus"></i></a>
					<a href="#" class="social-icon-ar sm facebook animated fadeInDown animation-delay-4"><i class="fa fa-facebook"></i></a>
					<hr class="dotted margin-10">
					<a href="{$app->buildURI(GW::s('SITE/PATH_REGISTER'))}" class="btn btn-ar btn-success pull-right">{GW::ln('/m/CREATE_ACCOUNT')}</a>
					<a href="{$app->buildURI(GW::s('SITE/PATH_PASSCHANGE'))}" class="btn btn-ar btn-warning">{GW::ln('/m/PASSWORD_RECOVERY')}</a>
					<div class="clearfix"></div>
				</div>
			</form>
		</div>
	</div>
</div>

*}
{include "default_close.tpl"}