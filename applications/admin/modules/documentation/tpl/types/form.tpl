{include file="default_form_open.tpl"}

{call e field=title}


{call e field=color type=color}
{*
items=$iconsdata
*}
{call e field=icon type=selecticon datasource=$m->buildUri(false,[act=>doGetIcons])}


{*
{call e field=fcolor type=select options=[white=>white,black=>black,orange=>orange,yellow=>yellow,blue=>blue,green=>green,brown=>brown,red=>red]}
*}
{*
{call e field=active type=bool default=1}
*}


{include file="default_form_close.tpl"}