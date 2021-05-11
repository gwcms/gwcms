{include file="default_form_open.tpl"}

{*$nowrap=1*}


{call e field=notify_mail type=text}

{call e field=recapPublicKey hidden_note="leave this field empty to disable feature"}
{call e field=recapPrivateKey}


{include file="default_form_close.tpl" submit_buttons=[save]}