{include file="default_form_open.tpl"}


	

{*

{foreach GW::$settings.LANGS as $lncode}
	{include file="elements/input.tpl" name="title_$lncode" type=text}
{/foreach}	
*}

{include file="elements/input.tpl" name="title" type=text}
{include file="elements/input.tpl" name="username" type=text}
{include file="elements/input.tpl" name="pass" type=text}
{include file="elements/input.tpl" name="comments" type=text}

{include file="elements/input.tpl" name="encryped" type=read}



{include file="default_form_close.tpl"}