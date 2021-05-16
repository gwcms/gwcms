{include "common.tpl"}

{*
{function name=do_toolbar_buttons_configtabs}
	{toolbar_button title=GW::l('/A/VIEWS/itax') iconclass='gwico-Create-New' href=$m->buildUri(itax)}
{/function}


{$do_toolbar_buttons[] = configtabs}

*}


{include file="default_form_open.tpl" changes_track=1}




{$optionsview="optionsremote"}
{$modpath="datasources/itax"}
{$options=[]}
{$preload=1}

{*
https://menuturas.lt/admin/lt/itax/itax/optionsajax?q=a&group=products&page=1
*}


{call e field="ext/products"
	type="multiselect_ajax"
	modpath="datasources/itax"
	optionsview="optionsremote"
	source_args=[group=>products]
	options=[]
	preload=1
	value_format=json1
}


{call e field="ext/suplier_groups" type=select_ajax source_args=[group=>suplier_groups]}
{call e field="ext/supliers" type=select_ajax source_args=[group=>supliers]}
{call e field="ext/tags" type=select_ajax source_args=[group=>tags]}
{call e field="ext/sales_taxes" type=select_ajax source_args=[group=>sales_taxes]}
{call e field="ext/departments" type=select_ajax source_args=[group=>departments]}
{call e field="ext/product_groups" type=select_ajax source_args=[group=>product_groups]}
{call e field="ext/projects" type=select_ajax source_args=[group=>projects]}



{include file="default_form_close.tpl" submit_buttons=[save]}