{include file="inputs/inputs.tpl"}

<form method="post" action="{$app->buildUri(false,$smarty.get+['invoiceview'=>1])}">
	<div class="">


	<input name="act" type="hidden" value="doSaveBankTransferDetails"/>



	<div class='g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30'>

		{if $preinvoice}{$tmp="_PRE"}{else}{$tmp=""}{/if}
		<h2>{GW::ln("/m/PROVIDE_DETAILS_IF_NEEDED{$tmp}")}</h2>
		
		<br/>
		
		
		{if $m->feat('sabis')}
			<div class='row'>
				<div class="col-md-12">
					{if $item->get("keyval/btransfer_or_banklink")}{$tmp=[readonly=>1]}{else}{$tmp=[]}{/if}
					
					{call input field="keyval/btransfer_or_banklink" type=radio 
					options=[1=>GW::ln('/m/PICK_SYSTEM_PAY'), 2=>GW::ln('/m/PICK_BANKTRANSFER')]
					params_expand=$tmp
					}
				</div>				

			</div>		
		{/if}	
		<hr>
		
		

	<div class='row'>
		<div class="col-md-4">
			{call input field="email" type=email required=1 default=$app->user->email}
		</div>

		<div class="col-md-4">
			{call input field="name" default=$app->user->name}	
		</div>
		<div class="col-md-4">
			{call input field="surname" default=$app->user->surname}
		</div>	


		<div class="col-md-4">
			{call input field="phone"  default=$app->user->phone type=intphone}	
		</div>



		<div class="col-md-4 deliveryAddress">
			{call input field="city" default=$app->user->city}	
		</div>		

	</div>	
	<hr>
		{GW::ln('/m/LEAVE_EMPTY_IF_NO_COMPANY')}
		<br/><br/>
	<div class='row'>
		<div class="col-md-3">
			{call input field="company" default=$app->user->company}
		</div>	

		<div class="col-md-3 ">
			{call input field="company_code" default=$app->user->company_code}
		</div>							
		<div class="col-md-3 ">
			{call input field="vat_code" default=$app->user->vat_code}
		</div>							
		<div class="col-md-3 ">
			{call input field="company_addr" default=$app->user->company_addr}
		</div>			
	</div>
		{if $m->feat('sabis')}
	<div class='row'>
		<div class="col-md-3">
			{call input field="keyval/sabis" type=checkbox}
		</div>				
		
	</div>		
		{/if}

		<button class="btn btn-primary"><i class='fa fa-floppy-o'></i> {GW::ln('/g/UPDATE')}</button>



	</div>	
	</div>	
</form>