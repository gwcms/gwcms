
{include file="default_form_open.tpl"}






{if $app->user->isRoot()}
	{call e field="features" type=multiselect options=GW::l('/m/OPTIONS/features') value_format=json1}
{/if}


		
{call e field="itax_defaults_department_id" type=select_ajax options=[] preload=1 optionsview=optionsremote modpath='datasources/itax' empty_option=1 source_args=[group=>departments]}
{call e field="itax_defaults_product_id" type=select_ajax options=[] preload=1 optionsview=optionsremote modpath='datasources/itax' empty_option=1 source_args=[group=>products]}
{call e field="itax_defaults_tax_id" type=select_ajax options=[] preload=1 optionsview=optionsremote modpath='datasources/itax' empty_option=1 source_args=[group=>sales_taxes]}
{call e field="itax_defaults_journal_balanceable_id" type=select_ajax options=[] preload=1 optionsview=optionsremote modpath='datasources/itax' empty_option=1 source_args=[group=>supliers]}


{include file="default_form_close.tpl" submit_buttons=[save,cancel]}