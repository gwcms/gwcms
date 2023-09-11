{include file="default_form_open.tpl" form_width=1000px}


{call e field=module after_input_f=textopts options=$options.module options_fix=1}
{call e field=key}

{if preg_match('/_HTML$/', $item->key)}
	{$height="500px"}
	{$type=htmlarea}
{else}
	{$height="50px"}
	{$type=textarea}
{/if}

{foreach GW::$settings.LANGS as $lncode}
	{call e field="value_$lncode" type=$type}
{/foreach}	

{foreach $app->i18next as $lncode => $x}
	{call e field="value_$lncode" type=$type}
{/foreach}	





{include file="default_form_close.tpl"}


<style>
	.input_label_td{ width: 120px !important; }

</style>