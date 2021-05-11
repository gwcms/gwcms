{include "default_open.tpl"}

{include file="inputs/inputs.tpl"}

<br />
<div class="row">

	<div class="col-md-4">
		{$page->getContent('text')}
	</div>
	<div class="col-md-8">

		{if $smarty.get.success}
			<p class="alert alert-border alert-success">
				{$page->getContent('submit_text')|nl2br}
			</p>
		{else}
			<form role="form" id="supportForm" action="{$smarty.server.REQUEST_URI}" method="post">
				<input type="hidden" name="act" type="hidden" value="do:message" />
				<div class="row" style="max-width: 600px">
					<div class="col-md-12">

						<div class="panel panel-primary animated fadeInDown">
							<div class="panel-heading">{gw::ln('/m/SUPPORT_FORM')}</div>

							<div class="panel-body">
								<form role="form">

									{if !$app->user}
										<div class="row">
											<div class="col-md-6">
												{call "input" field=email required=1 type=email}
											</div>		

											<div class="col-md-6">
												{call "input" field=name required=1 type=text}
											</div>								
										</div>
			  {if $m->cfg->recapPublicKey}
				  {$recapPublicKey=$m->cfg->recapPublicKey}
				  <div class="row">
					<div class="col-md-6">
				     <div class="g-recaptcha" data-sitekey="{$recapPublicKey}" style="margin-bottom:5px;"></div>
					     </div>	  

				     <script src='https://www.google.com/recaptcha/api.js' async defer></script>
				     <script>
					 $('#supportForm').submit(function(event) {

					     var response = grecaptcha.getResponse();

					     if(response.length == 0){
						     event.preventDefault();
						     alert('{GW::ln('/G/validation/RECAPTCHA_FAILED')|escape:'javascript'}');
					     }
				       });
				     </script>
				</div>				
			{/if}
									{/if}

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												{call "input" field=message required=1 type=textarea rows=8 default=$smarty.get.message}
											</div>
										</div>
									</div>				    

									<div class="row">
										<div class="col-md-6">
											<button type="submit" class="btn btn-ar btn-primary pull-left">{gw::ln('/m/SEND')}</button>
										</div>
									</div>

							</div>
						</div>
					</div>
				</div>


			</form>
		{/if}

	</div>	
</div>

<br ><br >



{include "default_close.tpl"}