{include file="default_form_open.tpl"}

{include file="elements/input.tpl" name=title}

{include file="elements/input.tpl" name=sender hidden_note="Sender title &lt;email@address.com&gt;"}
{include file="elements/input.tpl" name=subject}



{$ck_options=[toolbarStartupExpanded=>false]}


{include file="elements/input.tpl" type=htmlarea name=body remember_size=1}





{include file="default_form_close.tpl"}