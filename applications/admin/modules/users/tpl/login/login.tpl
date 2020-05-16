{include "head.tpl"}

{if $success}
	<br />
	<div style="color:green">{$lang.SESSION_EXTEND_SUCCESS}</div>
	<script type="text/javascript">
		window.parent.gw_session.login_dialog_close();
	</script>
{else}
	
<body>
	<div id="container" class="cls-container">
		
		
		{if !$dialog}
		<!-- BACKGROUND IMAGE -->
		<!--===================================================-->
		<div id="bg-overlay"></div>
		
		<!-- BACKGROUND IMAGE -->
		<!--===================================================-->
		<div id="bg-overlay" class="bg-img" style="background-image: url({$app_root}static/img/bg-img/bg-img-{rand(1,7)}.jpg)"></div>	
		{/if}
		
			
		<!-- LOGIN FORM -->
		<!--===================================================-->
		<div class="cls-content">
		    <div class="cls-content-sm panel">
		        <div class="panel-body">
		            <div class="mar-ver pad-btm">
		                <h3 class="h4 mar-no">{GW::l('/g/ACCOUNT_LOGIN')}</h3>
		                <p class="text-muted">{GW::l('/g/SIGN_IN_TO_YOUR_ACCOUNT')}</p>
		            </div>
					
					{include "messages.tpl"}
					
		            <form action="{$app->uri}" method="post">
						<input type="hidden" name="act" value="do_login" />
		                <div class="form-group {if $smarty.get.login_fail}has-error{/if}">
		                    <input name="login[0]" type="text" class="form-control" placeholder="{$lang.USER}" autofocus value="{if isset($smarty.cookies.login_0)}{$smarty.cookies.login_0}{/if}">
		                </div>
		                <div class="form-group {if $smarty.get.login_fail}has-error{/if}">
		                    <input name="login[1]" type="password" class="form-control" placeholder="{$lang.PASS}">
		                </div>
		                <div class="checkbox pad-btm text-left">
		                    <input id="demo-form-checkbox" class="magic-checkbox" type="checkbox"  name="login_auto">
		                    <label for="demo-form-checkbox">{$lang.AUTOLOGIN}</label>
		                </div>
				
				{if $app->sess('temp_link_withfb')}
				<div class="checkbox pad-btm text-left">
					<input class="magic-checkbox" id="linkwithfbcb" type="checkbox" value="{$app->sess('temp_link_withfb')}" name="link_with_fb">
					<label for="linkwithfbcb">Link with <i class="fa fa-facebook" aria-hidden="true"></i> </label>		
				</div>		
				{/if}
					
				<div class="row">
					<div class="col-xs-10">
		                <button class="btn btn-primary btn-lg btn-block" type="submit">{$lang.DOLOGIN}</button>
					</div>
					
					
					
					<div class="col-xs-2">
						{if $app->sess('temp_link_withfb')}
							<img src="https://graph.facebook.com/{$app->sess('temp_link_withfb')}/picture?type=small" style="border-radius: 50%;height:30px;" class="mx-1">
						{else}
							<a class="btn btn-primary btn-lg" href="{$m->buildUri(false,[act=>doAuthWithFb])}" style="color:white;background-color:#3b5998">
								 <i class="fa fa-facebook" aria-hidden="true"></i>
							</a>							
						{/if}

					</div>
		            </form>
			    
	    
		        </div>
		
					{*
		        <div class="pad-all">
		            <a href="pages-password-reminder.html" class="btn-link mar-rgt">Forgot password ?</a>
		            <a href="pages-register.html" class="btn-link mar-lft">Create a new account</a>
		
		            <div class="media pad-top bord-top">
		                <div class="pull-right">
		                    <a href="#" class="pad-rgt"><i class="demo-psi-facebook icon-lg text-primary"></i></a>
		                    <a href="#" class="pad-rgt"><i class="demo-psi-twitter icon-lg text-info"></i></a>
		                    <a href="#" class="pad-rgt"><i class="demo-psi-google-plus icon-lg text-danger"></i></a>
		                </div>
		                <div class="media-body text-left">
		                    Login with
		                </div>
		            </div>
		        </div>
				*}
		    </div>
		</div>

		
		

		
		
		
	</div>
	<!--===================================================-->
	<!-- END OF CONTAINER -->


		</body>
		
		
{capture append="footer_hidden"}
    <link href="{$app_root}static/vendor/magic-check/css/magic-check.min.css" rel="stylesheet">	
{/capture}


{include "default_close_clean.tpl"}

{/if}