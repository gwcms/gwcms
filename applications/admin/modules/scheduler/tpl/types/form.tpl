{include file="default_form_open.tpl"}

{call e field=title i18n=4 i18n_expand=1}


{call e field=key hidden_note="Unikalus raktas tik lotyniškos raidės ir skaičiai"}
{call e field=color type=color}
{*
{call e field=fcolor type=select options=[white=>white,black=>black,orange=>orange,yellow=>yellow,blue=>blue,green=>green,brown=>brown,red=>red]}
*}
{*
{call e field=active type=bool default=1}
*}


{include file="default_form_close.tpl"}