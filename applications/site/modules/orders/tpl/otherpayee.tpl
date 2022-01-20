{include file="default_open.tpl"}
{include file="inputs/inputs.tpl"}	

<br><br>
                                                 





	{if !$smarty.get.invoiceview }

		<h1>{GW::ln('/g/STEP')} 1 - {GW::ln('/g/INVOICE_DETAILS')}</h1>
		{include file="`$smarty.current_dir`/prepareinvoice_0.tpl" preinvoice=1}
	{elseif $smarty.get.invoiceview == 1}

		
		<h1>{GW::ln('/g/STEP')} 2 - {GW::ln('/m/SEND_INVOICE_AND_PAYMENT_LINK')}</h1>

		<form id='sendform' method="post" action="{$smarty.server.REQUEST_URI}">



			<input name="act" type="hidden" value="doSendToOtherPayee"/>



			<div class='g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30'>

				{if $preinvoice}{$tmp="_PRE"}{else}{$tmp=""}{/if}
				<h2>{GW::ln("/m/PROVIDE_DETAILS_IF_NEEDED{$tmp}")}</h2>

				<br/>

			<div class='row'>
				<div class="col-md-4">
					{call input field="keyval/otherpayee_email" type=email required=1}
				</div>
				<div class="col-md-8">
					{call input field="keyval/otherpayee_msg" type=textarea}
				</div>

				<div class="col-md-12">
					<div class="form-group ">
						<label class="control-label" for="item_message_">Prisegama sąskaita</label>		
						{include file="`$smarty.current_dir`/invoiceview.tpl" preinvoice=1 nodownload=1}
					</div>
				</div>			

			</div>	
			</div>

			
		


		{$backargs=$smarty.get}
		{gw_unassign var=$backargs.invoiceview}
		<a href='{$app->buildUri(false, $backargs)}' class='btn btn-warning'>&laquo; Atgal - taisyti sąskaitos duomenis</a>
		<button  class='btn btn-primary float-right'> &raquo;  Pirmyn - siųsti el. laišką</button>
		<br><br>

		</form>	


	{/if}







<br/><br/>


     
{include file="default_close.tpl"}