{include file="default_form_open.tpl"}

{include file="elements/input.tpl" name=title note="(Nesiunčiama)"}
{include file="elements/input.tpl" name=comments type=textarea note="(Nesiunčiama)" autoresize=1 height=40px}


{include file="elements/input.tpl" name=sender hidden_note="Sender title &lt;email@address.com&gt;"}
{include file="elements/input.tpl" name=subject}



{$ck_options=[toolbarStartupExpanded=>false]}


{include file="elements/input.tpl" type=htmlarea name=body remember_size=1}

{include file="elements/input.tpl" name=groups type=multiselect options=$opt.groups}
{include file="elements/input.tpl" type=select name=lang options=$m->lang.OPT.lang empty_option=1 required=1}
{include file="elements/input.tpl" type=select name=status options=$m->lang.OPT.status}




{include file="default_form_close.tpl"}