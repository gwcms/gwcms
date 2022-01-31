{include "head.tpl"}

{if $success}
	<br />
	<div style="color:green">{GW::l('/g/SESSION_EXTEND_SUCCESS')}</div>
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
		                    <input name="login[0]" type="text" class="form-control" placeholder="{GW::l('/g/USER')}" autofocus value="{if isset($smarty.cookies.login_0)}{$smarty.cookies.login_0}{/if}">
		                </div>
		                <div class="form-group {if $smarty.get.login_fail}has-error{/if}">
		                    <input name="login[1]" type="password" class="form-control" placeholder="{GW::l('/g/PASS')}">
		                </div>

				
				{if $app->sess(temp_link_with3rd)}
				<div class="checkbox pad-btm text-left">
					<input checked="checked" class="magic-checkbox" id="linkwith3rdcb" type="checkbox" value="{$app->sess('temp_link_with3rd')}" name="link_with_3rd">
					<label for="linkwith3rdcb">Link with 
						{$tmp = explode('|',$app->sess('temp_link_with3rd'))}
						{if $tmp.0==fg}
							<i class="fa fa-facebook" aria-hidden="true" style="color:#3b5998"></i> 
						{elseif $tmp.0==gg}
							<i class="fa fa-google" aria-hidden="true" style="color:#DB4437"></i> 
						{/if}
						
					</label>		
				</div>	
				{else}
					<div class="checkbox pad-btm text-left">
					    <input id="demo-form-checkbox" class="magic-checkbox" type="checkbox"  name="login_auto">
					    <label for="demo-form-checkbox">{GW::l('/g/AUTOLOGIN')}</label>
					</div>
				{/if}
					
				<div class="row">
					<div class="col-xs-9">
		                <button class="btn btn-primary btn-lg btn-block" type="submit">{GW::l('/g/DOLOGIN')}</button>
					</div>
					
					
					
					<div class="col-xs-3">
						{if $app->sess('temp_link_withfb')}
							<img src="https://graph.facebook.com/{$app->sess('temp_link_withfb')}/picture?type=small" style="border-radius: 50%;height:42px;" class="mx-1">
						{else}
							<a class="btn btn-primary btn-sm" href="{$m->buildUri(false,[act=>doAuthWith3rd,gw=>fb])}" style="color:white;background-color:#3b5998">
								 <i class="fa fa-facebook" aria-hidden="true"></i>
							</a>
							<a class="btn btn-primary btn-sm" href="{$m->buildUri(false,[act=>doAuthWith3rd,gw=>gg])}" style="color:white;background-color:#DB4437">
								 <i class="fa fa-google" aria-hidden="true"></i>
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