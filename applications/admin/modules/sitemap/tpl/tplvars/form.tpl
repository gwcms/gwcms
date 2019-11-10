{include file="default_form_open.tpl"}

{call e field=title hidden_note=$m->lang.FIELD_NOTE.template_title}
{call e field=name hidden_note=$m->lang.FIELD_NOTE.template_name}

{call e field=note}
{call e field=type type=select options=$m->lang.VAR_TYPE_OPT}


{capture assign=defaultparams}
	{literal}{}{/literal}
{/capture}

{call e field=params type=code_json height=100px nopading=1 default=$defaultparams}  
{*{call e field=params type=textarea height="100px"}*}

{*include file="elements/input.tpl" name=params type=textarea height="100px" default="{ldelim}{rdelim}"*}

{call e field=multilang type=bool default=1}

{include file="default_form_close.tpl"}