{include file="default_form_open.tpl" form_width="1024px"}



{call e field=notes type=text}
{call e field=user_id type=select_ajax modpath="customers/users"  preload=1 options=[]}
{call e field=event_id type=select_ajax modpath="events/evnts"  preload=1 options=[]}

{call e field=points type=number}


{call e field=active type=bool}






{include file="default_form_close.tpl"}