{include file="default_form_open.tpl"}


{call e field=name}
{call e field=surname}
{call e field=email required=1}

{call e field=lang type=select options=$m->lang.OPT.lang empty_option=1 required=1}

{call e field=groups type=multiselect options=$options.groups}

{call e field=active type=bool}
{call e field=unsubscribed type=bool}
{call e field=unsubscribe_note type=text}




{include file="default_form_close.tpl"}