{include file="default_form_open.tpl"}


{call e field=module after_input_f=textopts options=$options.module options_fix=1}
{call e field=key}


{foreach GW::$settings.LANGS as $lncode}
	{call e field="value_$lncode" type=textarea height="50px"}
{/foreach}	

{foreach $app->i18next as $lncode => $x}
	{call e field="value_$lncode" type=textarea height="50px"}
{/foreach}	





{include file="default_form_close.tpl"}