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




{call e field="itaxtest/products" type="multiselect_ajax" source_args=[group=>products] value_format=json1}
{call e field="itaxtest/suplier_groups" type=select_ajax source_args=[group=>suplier_groups]}
{call e field="itaxtest/supliers" type=select_ajax source_args=[group=>supliers]}
{call e field="itaxtest/tags" type=select_ajax source_args=[group=>tags]}
{call e field="itaxtest/sales_taxes" type=select_ajax source_args=[group=>sales_taxes]}
{call e field="itaxtest/departments" type=select_ajax source_args=[group=>departments]}
{call e field="itaxtest/product_groups" type=select_ajax source_args=[group=>product_groups]}
{call e field="itaxtest/projects" type=select_ajax source_args=[group=>projects]}

{call e field="itaxtest/vat_business_group" type=select_ajax source_args=[group=>vat_business_groups]}
{call e field="itaxtest/country_codes" type=select_ajax source_args=[group=>countries]}





</table>


<p>Purpose of this page to have test inputs</p>
<a href="https://www.itax.lt/helps/37-api">Api info</a>
<table>


{include file="default_form_close.tpl" submit_buttons=[save]}