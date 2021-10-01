{include file="common.tpl"}


{function name=do_toolbar_buttons_configtabs}
	{*
	{toolbar_button title=GW::l('/A/VIEWS/itax') iconclass='gwico-Create-New' href=$m->buildUri(itax)}
	*}
{/function}



{function name=do_toolbar_buttons_cronruns} 
	{toolbar_button title="doCronRun&every=2" href=$m->buildUri(false,[act=>doCronRun,every=>2])}
	{toolbar_button title="doCronRun&every=5" href=$m->buildUri(false,[act=>doCronRun,every=>5])}
	{toolbar_button title="doCronRun&every=60" href=$m->buildUri(false,[act=>doCronRun,every=>60])}
{/function}		

{$do_toolbar_buttons_hidden=[cronruns,rtlog]}		
{$do_toolbar_buttons[] = configtabs}
{$do_toolbar_buttons[]=hidden}



{include "default_form_open.tpl" form_width="100%"} 
{$width_title="150px"}


{call e field="delivery_no"}
{call e field="delivery_lt"}
{call e field="delivery_eu"}
{call e field="delivery_in"}

{call e field="tasks_5min" hidden_note="separator ;"}
{call e field="tasks_2min" hidden_note="separator ;"}
{call e field="tasks_60min" hidden_note="separator ;"}
{call e field="tasks_10080min" hidden_note="every 7 night / separator ;"}


{call e field="notes_about_config" type=textarea layout=wide height="100px"}


{call e field="default_currency_code"}

{$opts=[vars_hint=>'/M/PRODUCTS/FIELDS_HELP/invoice',format_texts_ro=>1,vals=>[format_texts=>2]]}
{$owner=['owner_type'=>'shop/config','owner_field'=>'invoice']}
{include file="elements/input_select_mailtemplate.tpl" field=order_accept_mail default_vals=[admin_title=>GW::l('/m/FIELDS/order_accept_mail'),idname=>order_accept_mail]}



{$opts=[vars_hint=>'/M/COMPETITIONS/FIELDS_HELP/invoice',format_texts_ro=>1,vals=>[format_texts=>2]]}
{$owner=['owner_type'=>'competitions/config','owner_field'=>'invoice']}

{include file="elements/input_select_mailtemplate.tpl" field=proforma_invoice_default}
{include file="elements/input_select_mailtemplate.tpl" field=post_pay_invoice_default}
{include file="elements/input_select_mailtemplate.tpl" field=post_pay_mail_default default_vals=[admin_title=>GW::l('/m/FIELDS/post_pay_mail_default'),idname=>post_pay_mail_default]}



{call e field="wishlist_enabled" type=bool}



{call e field="modules" type=multiselect options=[doublef,modifications,quantities] value_format=json1 options_fix=1}



{if $m->features.modifications}
	{call e field="modification_display" type=select options=[select,list] options_fix=1 empty_option=1}
{/if}




{include "default_form_close.tpl"}
