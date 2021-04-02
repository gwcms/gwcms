{include file="default_form_open.tpl" form_width="1000px" changes_track=1}

{*$nowrap=1*}


{call e field=itax_mt_endpoint}



{if $options.department_id}
	{call e field=itax_default_client_department_id type=select options=$options.department_id empty_option=1}
{else}
	{call e field=itax_default_client_department_id type=text note="! Service not reachable"}
{/if}


{if $options.project_id}
	{call e field=itax_default_client_project_id type=select options=$options.project_id empty_option=1}
{else}
	{call e field=itax_default_client_project_id type=text note="! Service not reachable"}
{/if}


{if $options.client_groups}
	{call e field=itax_client_fiziniai_group type=select options=$options.client_groups empty_option=1}
	{call e field=itax_client_juridiniai_group type=select options=$options.client_groups empty_option=1}
{else}
	{call e field=itax_client_fiziniai_group type=text note="! Service not reachable"}
	{call e field=itax_client_juridiniai_group type=text note="! Service not reachable"}
{/if}



{if $options.location_id}
	{call e field=itax_default_location_id type=select options=$options.location_id empty_option=1}
{else}
	{call e field=itax_default_location_id type=text note="! Service not reachable"}
{/if}


{call e field=itax_tags 
	type=multiselect_ajax 
	options=[]
	value=json_decode($item->itax_tags, true)
	maximumSelectionLength=3
	preload=1
	datasource=$app->buildUri('shop/config/itaxtagsearch') 
}


{if $options.tax_id}
	{call e field=itax_default_taxid type=select options=$options.tax_id empty_option=1}
{else}
	{call e field=itax_default_taxid type=text default=16442}
{/if}

{call e 
	field="itax_product_id"
	type=select_ajax 
	maximumSelectionLength=1
	options=[]
	preload=1
	datasource=$app->buildUri('shop/config/itaxproductsearch') 
	tabs=[payment]
}


{call e field=itax_default_taxid_admin_note type=text}


{include file="default_form_close.tpl" submit_buttons=[save]}