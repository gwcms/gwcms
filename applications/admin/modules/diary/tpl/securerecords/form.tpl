{include file="default_form_open.tpl"}


	

{*

{foreach GW::$settings.LANGS as $lncode}
	{call e field="title_$lncode" type=text}
{/foreach}	
*}

{call e field="title" type=text}
{call e field="username" type=text}
{call e field="pass" type=text}
{call e field="comments" type=text}

{call e field="encryped" type=read}



{include file="default_form_close.tpl"}