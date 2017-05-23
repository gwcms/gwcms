{include file="default_form_open.tpl"}

{include file="elements/input.tpl" name=title hidden_note=$m->lang.FIELD_NOTE.template_title}
{include file="elements/input.tpl" name=name hidden_note=$m->lang.FIELD_NOTE.template_name}

{include file="elements/input.tpl" name=note}
{include file="elements/input.tpl" name=type type=select options=$m->lang.VAR_TYPE_OPT}

{include file="elements/input.tpl"  name=params type=code_json height=100px nopading=1}  
{*{include file="elements/input.tpl" name=params type=textarea height="100px"}*}

{*include file="elements/input.tpl" name=params type=textarea height="100px" default="{ldelim}{rdelim}"*}

{include file="default_form_close.tpl"}