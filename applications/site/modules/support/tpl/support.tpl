{include "default_open.tpl"}

{include file="inputs/inputs.tpl"}


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
			<form role="form" action="{$smarty.server.REQUEST_URI}" method="post">
				<input type="hidden" name="act" type="hidden" value="do:message" />
				<div class="row" style="max-width: 600px">
					<div class="col-md-12">

						<div class="panel panel-primary animated fadeInDown">
							<div class="panel-heading">{gw::l('/m/SUPPORT_FORM')}</div>

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
									{/if}

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												{call "input" field=message required=1 type=textarea rows=8}
											</div>
										</div>
									</div>				    

									<div class="row">
										<div class="col-md-6">
											<button type="submit" class="btn btn-ar btn-primary pull-left">{gw::l('/m/SEND')}</button>
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




{include "default_close.tpl"}