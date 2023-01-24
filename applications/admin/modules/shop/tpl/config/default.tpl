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



{call e field="features" type=multiselect options=GW::l('/m/OPTIONS/features') value_format=json1}



{if $m->features.modifications}
	{call e field="modification_display" type=select options=[select,list] options_fix=1 empty_option=1}
{/if}


{call e field=site_list_types type=multiselect_ajax options=[grid,list,listbigrow] options_fix=1 sorting=1 value_format=json1}
{call e field=site_itemspp type=multiselect_ajax options=[12,24,32,64,100,200,300] options_fix=1  value_format=json1}



{call e field=shop_orders_viewers_group type=select_ajax modpath="users/groups" preload=1 options=[]}

{call e field="vatgroup" type=select_ajax modpath="payments/vatgroups" preload=1 options=[] empty_option=1 hidden_note="default VAT group. in case for specific producs differs use vatgroup feature to enable input in product form" }

{include "default_form_close.tpl"}
