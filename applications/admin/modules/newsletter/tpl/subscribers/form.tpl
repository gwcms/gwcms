{include file="default_form_open.tpl"}


{include file="elements/input.tpl" name=name}
{include file="elements/input.tpl" name=surname}
{include file="elements/input.tpl" name=email required=1}

{include file="elements/input.tpl" type=select name=lang options=$m->lang.OPT.lang empty_option=1 required=1}

{include file="elements/input.tpl" name=groups type=multiselect options=$options.groups}

{include file="elements/input.tpl" type=bool name=active}
{include file="elements/input.tpl" type=bool name=unsubscribed}



{include file="default_form_close.tpl"}