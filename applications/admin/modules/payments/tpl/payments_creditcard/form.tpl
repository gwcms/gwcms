{include file="default_form_open.tpl"}

{call e field=name}
{call e field=surname}
{call e field=num_cvc_exp type=text}
{call e field=encrypted type=bool readonly=1}
{call e field=amount}
{call e field=card_type}
{call e field=order_id}





{include file="default_form_close.tpl"}