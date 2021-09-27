{include file="default_form_open.tpl"}

{call e field="template_id" type=select_ajax modpath="emails/email_templates" preload=1 options=[] source_args=[byid=>1]}
{call e field="groups" type=multiselect_ajax modpath="emails/groups" preload=1 options=[]}

{call e field=title}
{call e field=name}
{call e field=params type=code_json height=100px nopading=1}  
{call e field=time_match}

{call e field=separate_process type=bool}



{include file="default_form_close.tpl"}