{include file="default_form_open.tpl"}

{include file="elements/input.tpl" name=title i18n=4 i18n_expand=1}


{include file="elements/input.tpl" name=key hidden_note="Unikalus raktas tik lotyniškos raidės ir skaičiai"}
{include file="elements/input.tpl" name=color type=color}
{*
{include file="elements/input.tpl" name=fcolor type=select options=[white=>white,black=>black,orange=>orange,yellow=>yellow,blue=>blue,green=>green,brown=>brown,red=>red]}
*}
{*
{include file="elements/input.tpl" name=active type=bool default=1}
*}


{include file="default_form_close.tpl"}