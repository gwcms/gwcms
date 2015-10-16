{include file="default_form_open.tpl"}

{include file="elements/input.tpl" name=module}
{include file="elements/input.tpl" name=key}


{foreach GW::$settings.LANGS as $lncode}
	{include file="elements/input.tpl" name="value_$lncode"}
{/foreach}	






{include file="default_form_close.tpl"}