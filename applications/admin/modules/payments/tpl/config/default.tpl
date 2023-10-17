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
{call e field="default_currency_code"}



{call e field="pay_types" type=multiselect_ajax sorting=1 options=GW::l('/m/OPTIONS/pay_type') value_format=json1}
{call e field="features" type=multiselect options=GW::l('/m/OPTIONS/features') value_format=json1}


{call e field="delivery_algo" type=select options=GW::l('/m/OPTIONS/delivery_algo') empty_option=1}

{call e field=testpay_user_id type=select_ajax modpath="users/usr"  preload=1 options=[]}

{include "default_form_close.tpl"}
