{include file="default_form_open.tpl"}


	

{*

{foreach GW::$settings.LANGS as $lncode}
	{include file="elements/input.tpl" name="title_$lncode" type=text}
{/foreach}	
*}

{include file="elements/input.tpl" name="title_lt" type=text}
{include file="elements/input.tpl" name="title_en" type=text}

{include file="elements/input.tpl" name=approved type=bool}



{if $item->user_id}
	{include file="elements/input.tpl" name=user_id type=read value="`$item->userobj->name` `$item->userobj->surname`" title=GW::l('/m/CREATED_BY')}
{/if}

{if $item->admin_id}
	{include file="elements/input.tpl" name=admin_id type=read value="`$item->adminobj->name` `$item->adminobj->surname`"}
{/if}


{include file="default_form_close.tpl"}