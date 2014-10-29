{include file="default_form_open.tpl"}


{include file="elements/input.tpl" name=username}
{include file="elements/input.tpl" name=email}
{include file="elements/input.tpl" name=first_name}
{include file="elements/input.tpl" name=second_name}
{include file="elements/input.tpl" name=phone}
{include file="elements/input.tpl" name=desc type=textarea height=100px}
{include file="elements/input.tpl" type=bool name=active}
{include file="elements/input.tpl" type=select name=session_validity options=$m->lang.SESSION_VALIDITY_OPT}

{include file="elements/input.tpl" type=pass_visible name=pass_new title=$m->lang.FIELDS.pass}
{include file="elements/input.tpl" type=bool name=allow_credit}
{include file="elements/input.tpl" name=funds}

{include file="default_form_close.tpl" extra_fields=[id,funds]}
