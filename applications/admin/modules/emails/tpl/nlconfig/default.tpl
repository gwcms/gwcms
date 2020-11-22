{include file="default_form_open.tpl" form_width="1000px"}

{*$nowrap=1*}


{call e field=portion_size type=text default=100}
{call e field=default_sender type=text hidden_note=GW::l('/m/email_note')}
{call e field=default_replyto type=text hidden_note=GW::l('/m/email_note')}


{$ck_options=[toolbarStartupExpanded=>false]}

{call e field=subscribe_confirm_msg type=htmlarea hidden_note=GW::l('/m/subscribe_confirm_msg_note') remember_size=1 layout=wide}


{call e field=dkim_private_key type=textarea height=60px}
{call e field=dkim_domain}
{call e field=dkim_domain_selector}


{include file="default_form_close.tpl" submit_buttons=[save]}