{include file="default_form_open.tpl"}

{include file="elements/input.tpl" name=title}
{include file="elements/input.tpl" name=note}
{include file="elements/input.tpl" name=type type=select options=$m->lang.VAR_TYPE_OPT}
{include file="elements/input.tpl" name=params type=textarea height="100px"}


{*include file="elements/input.tpl" name=params type=textarea height="100px" default="{ldelim}{rdelim}"*}

{include file="default_form_close.tpl"}