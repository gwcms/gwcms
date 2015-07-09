{include file="default_form_open.tpl"}

{*$nowrap=1*}


{include file="elements/input.tpl" name=portion_size type=text default=100}
{include file="elements/input.tpl" name=default_sender type=text hidden_note=$m->lang.email_note}
{include file="elements/input.tpl" name=default_replyto type=text hidden_note=$m->lang.email_note}


{$ck_options=[toolbarStartupExpanded=>false]}

{include file="elements/input.tpl" name=subscribe_confirm_msg type=htmlarea hidden_note=$m->lang.subscribe_confirm_msg_note remember_size=1}


{include file="elements/input.tpl" name=dkim_private_key type=textarea height=60px}
{include file="elements/input.tpl" name=dkim_domain}
{include file="elements/input.tpl" name=dkim_domain_selector}


{include file="default_form_close.tpl" submit_buttons=[save]}