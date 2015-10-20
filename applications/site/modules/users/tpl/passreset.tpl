{include "default_open.tpl"}


				

					
<div class="center-block logig-form">
	<div class="panel panel-primary">
		<div class="panel-heading">{GW::ln('/m/SEND_PASS_RESET')}</div>
		<div class="panel-body">
			<form role="form" action="{$smarty.server.REQUEST_URI}" method="post">
				<input type="hidden" name="act" value="do:passchange" />

				<div class="form-group">
					<div class="input-group login-input {if $smarty.get.error}has-error has-feedback{/if}">
						<span class="input-group-addon"><i class="fa fa-user"></i></span>
						<input name="email"  value="{$smarty.post.email|escape}" type="text" class="form-control" placeholder="{GW::ln('/m/USERNAME_EMAIL')}" required="1">
					</div>
					<br>
					
					<button type="submit" class="btn btn-ar btn-primary pull-right">{GW::ln('/m/PASS_RESET_GO')}</button>
					
					<div class="clearfix"></div>
				</div>
			</form>
		</div>
	</div>
</div>

					

{include "default_close.tpl"}