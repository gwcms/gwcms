{include file="default_form_open.tpl" form_width=1024}




{call e field=user_id type=select_ajax modpath="customers/users"  preload=1 options=[]}
{call e field=validfrom type=date}
{call e field=expires type=date}


{call e field=pay_id type=select_ajax modpath="datasources/payments_paysera"  preload=1 options=[]}


{call e field=active type=bool}






{include file="default_form_close.tpl"}