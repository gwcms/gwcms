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





{call e field="delivery_algo" type=select options=GW::l('/m/OPTIONS/delivery_algo') empty_option=1}


{if $m->algo=='orderprint'}
{/if}



{if $m->algo=='std'}
{/if}


{if $m->algo==natos}
	{capture assign=tmp}
		<table>
	{call e field="delivery_no"}
	{call e field="delivery_lt"}
	{call e field="delivery_eu"}
	{call e field="delivery_in"}
		</table>
	{/capture}

	{call e field=standart type=read value=$tmp}



	{capture assign=tmp}
		<table>
	{call e field="lo_delivery_exceptions" type="multiselect_ajax" modpath="products/items"	options=[] preload=1 value_format=json1}
	{call e field="lo_delivery_no"}
	{call e field="lo_delivery_lt"}
	{call e field="lo_delivery_eu"}
	{call e field="lo_delivery_in"}

		</table>
	{/capture}

	{call e field=lowerprice type=read value=$tmp}
{/if}


{include "default_form_close.tpl"}
