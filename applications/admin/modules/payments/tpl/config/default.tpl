{include file="common.tpl"}
		


{if $m->feat(itax)}
	{function name=do_toolbar_buttons_configtabs}
		{toolbar_button title=GW::l('/A/VIEWS/itax') iconclass='gwico-Create-New' href=$m->buildUri(itax)}
	{/function}
	

	{$do_toolbar_buttons[] = configtabs}
	{*{$do_toolbar_buttons[]=hidden}*}
{/if}





{include "default_form_open.tpl" form_width="100%"} 
{$width_title="150px"}




{call e field="confirm_email_tpl" type=select_ajax modpath="emails/email_templates" preload=1  options=[]  source_args=[byid=>1] after_input_f="editadd"}
{call e field="statuschange_email_tpl" type=select_ajax modpath="emails/email_templates" preload=1  options=[]  source_args=[byid=>1] after_input_f="editadd"}

{call e field="confirm_email_bcc" type=text}
{call e field="default_currency_code"}


{call e field="pay_types" type=multiselect_ajax sorting=1 options=GW::l('/m/OPTIONS/pay_type') value_format=json1}
{call e field="features" type=multiselect options=GW::l('/m/OPTIONS/features') value_format=json1}


{call e field="delivery_algo" type=select options=GW::l('/m/OPTIONS/delivery_algo') empty_option=1}

{call e field=testpay_user_id type=select_ajax modpath="users/usr"  preload=1 options=[]}



{if $m->feat(rivile)}
	{capture assign=tmp}
		<table class="gwTable" style="width:100%">
			{call e field=rivile_monthlyreport_email}
			{call e field=rivile_service_id_map type=textarea hidden_note="obj_type|shop_subscribers|5000 - 1 kintamojo tipoas palyginimui 2 - kintamojo verte siuo atveju - abonentu modulio eilute, 3 - kodas perduodamas rivilei, 4 - komentaras sau kintamojo tipai - obj_type,obj_id "}
			{call e field=rivile_default_service_code}
			{call e field=rivile_balans_sask_id hidden_note="RS: 271 TF:2714"}
		</table>

	{/capture}
	{call e field=rivile type=read value=$tmp layout=wide}
	
	
{/if}


{if $m->feat(sabis)}
	{capture assign=tmp}
		<table class="gwTable" style="width:100%">
			{call e field=sabis_clientid_secret hidden_note='clientid|secret'}
			{call e field=sabis_test type=bool}
			{call e field=sabis_test_clientid_secret hidden_note='clientid|secret'}

			{call e field=sabis_supplier type=code height="290px" codelang=xml}
		</table>

	{/capture}
	{call e field=sabis type=read value=$tmp layout=wide}
{/if}


{include "default_form_close.tpl"}
