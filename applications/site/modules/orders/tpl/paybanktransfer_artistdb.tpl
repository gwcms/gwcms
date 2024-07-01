{include file="default_open.tpl"}
{include file="inputs/inputs.tpl"}	

<br>
                                                 
<center>
<h4>{GW::ln('/m/PAYMENT_DETAILS')} </h4>

<hr>

{if $smarty.get.invoiceview}
	{include file="`$smarty.current_dir`/invoiceview.tpl" preinvoice=1}
        <br>
   <form id='banktransferconfirm_form' method="post" action="{$smarty.server.REQUEST_URI}" {if $item->get('extra/bt_confirm_cnt')} style='display:none'{/if}>
			<input name="act" type="hidden" value="doSaveBankTransferConfirm"/>
                        {call input type=hidden field="pay_user_msg" value="{GW::ln('/m/I_JUST_TOOK_INVOICE')}"}
			<button class="btn btn-primary"><i class='fa fa-envelope-o'></i> {GW::ln('/m/I_WILL_PAY')}</button>	
		</form>
{elseif $smarty.get.preinvoice}
	{include file="`$smarty.current_dir`/prepareinvoice_0.tpl" preinvoice=1}
        
{else}
	<a class="btn btn-warning" href='{$app->buildUri(false, $smarty.get+[preinvoice=>1])}'>{GW::ln('/M/orders/NEED_PREINVOICE')}</a>
{/if}


<hr>
</center>
<div class='g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30'>
<div class="row">
	<div class="col-md-6">
            <center><h4>{GW::ln('/M/orders/BANKU_REKVIZITAI')}</h4></center>
		<table class="contactsTable">
		<tr><th>{GW::ln('/M/orders/PAYMENT_RECEIVER')}</th><td>{GW::ln('/g/CONTACTS_COMPANY_NAME')}</td></tr>
		<tr><th>{GW::ln('/M/orders/COMPANY_ID')}</th><td> {GW::ln('/g/CONTACTS_COMPANY_ID')}</td></tr>
                <tr><th>{GW::ln('/m/PAYMENT_BANKTRANSFER_DETAILS')}</th><td> {GW::ln('/g/PAYMENT_BANKTRANSFER_DETAILS_PREFIX')}{$item->id} </td></tr>
		<tr><th>{GW::ln('/m/PAYMENT_PAY_PRICE')}</th><td> {$item->amount_total} Eur </td></tr>
		{*<tr><th>{GW::ln('/M/orders/COMPANY_ADDRESS')}</th><td> {GW::ln('/g/CONTACTS_ADDRESS')}</td></tr>*}
		{if GW::ln('/g/CONTACTS_IBAN') !='&nbsp;'}<tr><th>{GW::ln('/M/orders/IBAN')}</th><td> 
					{GW::ln('/g/CONTACTS_IBAN')} 
				</td></tr>
		{else}
			<tr><td colspan="2">
                               <br>{GW::ln("/G/paymethods/description/banktransfer")}	
			</td></tr>
		{/if}
				</table>
	</div>
	
	<div class="col-md-6">
		<h4>{GW::ln('/m/BANK_TRANSFER_CONFIRM_FORM_TITLE')} </h4>
                <br>
		{if $item->get('extra/bt_confirm_cnt')}
			<p class="text-primary">{GW::ln('/m/BANK_TRANSFER_CONFIRM_ALREADY_SENT')}: {$item->get('extra/bt_confirm')} </p>
                        <form id='banktransferconfirm_form' method="post" action="{$smarty.server.REQUEST_URI}">
			<input name="act" type="hidden" value="doSaveBankTransferConfirm"/>
                        {call input type=hidden field="pay_user_msg" value="as ikeliau pavedimo kopija"}
			<button class="btn btn-primary"><i class='fa fa-envelope-o'></i> {GW::ln('/m/I_WILL_PAY')}</button>	
		</form>
			<button class='btn btn-primary' onclick="$(this).hide();$('#banktransferconfirm_form').fadeIn();">
				<i class="fa fa-repeat"></i>
			</button>
                         
		{/if}
		
		<form id='banktransferconfirm_form' method="post" action="{$smarty.server.REQUEST_URI}" {if $item->get('extra/bt_confirm_cnt')} style='display:none'{/if}>
			<input name="act" type="hidden" value="doSaveBankTransferConfirm"/>
			
					{input field="banktransfer_confirm" type="image" endpoint="orders/orders" allowpdf=1}
				                                				
					{call input type=textarea field="pay_user_msg" required=1}
			
				
			<button class="btn btn-primary"><i class='fa fa-envelope-o'></i> {GW::ln('/m/SAVE_BANK_TRANSFER_CONFIRM')}</button>	
		</form>
	</div>
</div>
	</div>
	
<style>
	.contactsTable td, .contactsTable th{ padding: 2px 5px 2px 5px; border-bottom: 1px solid silver; }	
	.contactsTable th{ text-align: right;padding-right: 30px; }	
</style>
<div style='text-align:right'>
  <form id='banktransferconfirm_form' method="post" action="{$smarty.server.REQUEST_URI}">
			<input name="act" type="hidden" value="doSaveBankTransferConfirm"/>
                        {call input type=hidden field="pay_user_msg" value="{GW::ln('/m/I_JUST_TOOK_PAYMENT_DETAILS')}"}
			<button class="btn btn-warning"> {GW::ln('/m/I_WILL_PAY')}</button>	
		</form>
                        <br>
                        </div>
{include file="default_close.tpl"}