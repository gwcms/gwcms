{include file="default_form_open.tpl"}



{include file="elements/input.tpl" name=name}
{include file="elements/input.tpl" name=error_code}
{include file="elements/input.tpl" name=running}
{include file="elements/input.tpl" name=insert_time}
{include file="elements/input.tpl" name=halt_time}
{include file="elements/input.tpl" name=speed}

{include file="elements/input.tpl" type=textarea name=output}

{include file="elements/input.tpl" type=textarea name=arguments}


{include file="default_form_close.tpl" extra_fields=[id,insert_time,update_time,halt_time]}