{include file="common.tpl"}
		
{*
{function name=do_toolbar_buttons_configtabs}
	{toolbar_button title=GW::l('/A/VIEWS/itax') iconclass='gwico-Create-New' href=$m->buildUri(itax)}
{/function}
*}
{*
{function name=do_toolbar_buttons_cronruns} 
	{toolbar_button title="doCronRun&every=2" href=$m->buildUri(false,[act=>doCronRun,every=>2])}
	{toolbar_button title="doCronRun&every=5" href=$m->buildUri(false,[act=>doCronRun,every=>5])}
	{toolbar_button title="doCronRun&every=60" href=$m->buildUri(false,[act=>doCronRun,every=>60])}
{/function}		
*}
{*
{$do_toolbar_buttons_hidden=[cronruns,rtlog]}		
{$do_toolbar_buttons[] = configtabs}
{$do_toolbar_buttons[]=hidden}
*}


{include "default_form_open.tpl" form_width="100%"} 
{$width_title="150px"}




{call e field="confirm_email_tpl" type=select_ajax modpath="emails/email_templates" preload=1  options=[]  source_args=[byid=>1] }
{call e field="default_currency_code"}



{call e field="pay_types" type=multiselect_ajax sorting=1 options=GW::l('/m/OPTIONS/pay_type') value_format=json1}
{call e field="features" type=multiselect options=GW::l('/m/OPTIONS/features') value_format=json1}


{include "default_form_close.tpl"}
