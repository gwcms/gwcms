{include file="default_form_open.tpl"}

{*$nowrap=1*}


{include file="elements/input.tpl" name=portion_size type=text default=100}
{include file="elements/input.tpl" name=default_sender type=text hidden_note="Sender title &lt;email@address.com&gt;"}
{include file="elements/input.tpl" name=default_replyto type=text hidden_note="Sender title &lt;email@address.com&gt;"}


{include file="default_form_close.tpl" submit_buttons=[save]}