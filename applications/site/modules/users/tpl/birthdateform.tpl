{include "default_open.tpl"}



{include "inputs/inputs.tpl"}

<h3>{GW::ln('/m/PLEASE_ADD_BDATE')}</h3>

<form id="usrForm" class="g-py-15" action="{$smarty.server.REQUEST_URI}" method="post">
	<input type="hidden" name="act" value="do:saveBirthdate" />	
	
	<div class="row">
		<div class="col-md-6">	
		{input field="birthdate" type=birthdate}
		</div>
	</div>

	<br />
	
	<div class="row">
		<div class="col-md-4">
	
	<button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25 mb-4" type="button" onclick="$('#usrForm').submit()">{GW::ln('/g/SUBMIT')}</button>
		</div>
	</div>
</form>



{include "default_close.tpl"}