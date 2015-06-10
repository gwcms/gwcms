{include file="default_form_open.tpl"}





{$users = $app->user->getOptions(true)}
{include file="elements/input.tpl" type=select name=user_id empty_option=1 options=$users default=$app->user->id}

{include file="elements/input.tpl" name=subject}
{include file="elements/input.tpl" name=sender}
{include file="elements/input.tpl" name=message type="textarea"}



{include file="default_form_close.tpl"}