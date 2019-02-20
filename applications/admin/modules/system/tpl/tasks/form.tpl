{include file="default_form_open.tpl"}



{call e field=name}
{call e field=error_code}
{call e field=running}
{call e field=insert_time}
{call e field=halt_time}
{call e field=speed}

{call e field=output type=textarea}

{call e field=arguments type=textarea}


{include file="default_form_close.tpl" extra_fields=[id,insert_time,update_time,halt_time]}