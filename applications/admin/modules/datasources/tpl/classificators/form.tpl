{include file="default_form_open.tpl" form_width="1000px"}

{call e field=type type=select options=$options.classtypes empty_option=1 default=$m->filters.type}
{call e field=title type=text  i18n=4}
{call e field=active type=bool}


{include file="default_form_close.tpl"}