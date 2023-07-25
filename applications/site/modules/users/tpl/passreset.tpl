{include "default_open.tpl"}

<section class="container  g-pt-20 g-pb-80">
				

<div class="row justify-content-md-center">		
                  <div class="col-md-6">
			  
<div class="center-block logig-form">
	<div class="panel panel-primary">
		<div class="panel-heading">{GW::ln('/m/SEND_PASS_RESET')}</div>
		<br />
		<div class="panel-body">
			<form role="form" action="{$smarty.server.REQUEST_URI}" method="post">
				<input type="hidden" name="act" value="do:passchange" />

				<div class="form-group">
					<div class="input-group login-input {if $smarty.get.error}has-error has-feedback{/if}">
						<span class="input-group-addon"><i class="fa fa-user"></i></span>
						{$email=$smarty.post.email|default:$smarty.get.email}
						<input name="email"  value="{$email|escape}" type="text" class="form-control" placeholder="{GW::ln('/m/USERNAME_EMAIL')}" required="1">
					</div>
					<br>
					
					<button type="submit" class="btn btn-ar btn-primary pull-right">{GW::ln('/m/PASS_RESET_GO')}</button>
					
					<div class="clearfix"></div>
				</div>
			</form>
		</div>
	</div>
</div>
					
</div>
					
</div></div>
</section>
					

{include "default_close.tpl"}