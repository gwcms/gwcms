
{include file="default_form_open.tpl"}






{if $app->user->isRoot()}
	{call e field="features" type=multiselect options=GW::l('/m/OPTIONS/features') value_format=json1}
{/if}





{include file="default_form_close.tpl" submit_buttons=[save,cancel]}