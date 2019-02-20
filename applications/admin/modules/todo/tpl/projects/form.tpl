{include file="default_form_open.tpl"}

{call e field=title}
{call e field=description type=textarea height=100px}
{call e field=color type=color}
{call e field=fcolor type=select options=[white=>white,black=>black,orange=>orange,yellow=>yellow,blue=>blue,green=>green,brown=>brown,red=>red]}

{call e field=active type=bool default=1}


{include file="default_form_close.tpl"}